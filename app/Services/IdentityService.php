<?php

namespace App\Services;

use App\Enums\MemberStatus;
use App\Models\AuditLog;
use App\Models\Consent;
use App\Models\Customer;
use App\Models\CustomerMemberLink;
use App\Models\Member;
use App\Models\Merchant;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * PH2-001A — the OneMember Identity platform (ADR-010).
 *
 * Identity is global; loyalty is local. This service is the only write path
 * for identities, links, and join consents. Rules enforced here:
 *  - one phone = one identity (find-or-create, never duplicate)
 *  - one live link per (customer, merchant)
 *  - joins share only customer-approved fields
 *  - every join is audited
 */
class IdentityService
{
    public const CONSENT_VERSION = 'v1-2026-07';

    /** Fields a merchant may request during scan-to-join. Name is mandatory —
     *  a Member record cannot exist without it. */
    public const SHAREABLE_FIELDS = ['name', 'phone', 'email', 'birthday', 'postal_code'];

    /**
     * Ensure a global identity exists for a freshly registered member and
     * link it. Called from the merchant registration flow (customer present,
     * providing their details to this merchant). Profile fields seed the
     * identity ONLY on first creation — an existing identity is never
     * overwritten by a merchant's data entry.
     */
    public function ensureIdentityForMember(Member $member, string $via = 'registration'): ?Customer
    {
        $phone = $this->normalisePhone($member->phone);
        if ($phone === '') {
            return null; // no phone → no global identity (identity anchor is the phone)
        }

        return DB::transaction(function () use ($member, $phone, $via) {
            $customer = Customer::where('phone', $phone)->first();
            $created  = false;

            if (! $customer) {
                $customer = Customer::create([
                    'name'        => $member->name,
                    'phone'       => $phone,
                    'email'       => $member->email,
                    'birthday'    => $member->birthday?->format('Y-m-d'),
                    'postal_code' => $member->postal_code,
                    'locale'      => app()->getLocale(),
                ]);
                $created = true;

                AuditLog::record('identity.created', $customer, [], [
                    'onemember_id' => $customer->onemember_id,
                    'via'          => $via,
                ], $member->merchant_id);
            }

            $this->linkIfMissing($customer, $member, $via);

            return $created ? $customer->refresh() : $customer;
        });
    }

    /**
     * Signed QR payload for the OneMember Card. Contains ONLY the OneMember
     * ID plus an integrity signature — never personal data (ADR-010).
     */
    public function qrPayload(Customer $customer): string
    {
        return 'OM2:' . $customer->onemember_id . ':' . $this->sign($customer->onemember_id);
    }

    /**
     * Resolve a scanned QR payload to an identity. Returns null for anything
     * malformed, forged, or unknown — indistinguishable to the caller (no
     * enumeration of valid IDs).
     */
    public function resolveQr(?string $payload): ?Customer
    {
        if (! is_string($payload) || ! preg_match('/^OM2:(OM-[A-Z2-9]{4}-[A-Z2-9]{4}):([a-f0-9]{16})$/', trim($payload), $m)) {
            return null;
        }

        [, $onememberId, $signature] = $m;

        if (! hash_equals($this->sign($onememberId), $signature)) {
            return null;
        }

        return Customer::where('onemember_id', $onememberId)->first();
    }

    /**
     * Scan-to-join (ADR-010 §6): create a membership at $merchant from the
     * customer's consented fields only. Behaviour:
     *  - already linked at this merchant           → InvalidArgumentException
     *  - existing UNLINKED member with same phone  → consented connect (no duplicate)
     *  - otherwise                                 → new Member from approved fields
     *
     * @param  list<string>  $approvedFields  subset of SHAREABLE_FIELDS the customer approved
     */
    public function joinMerchant(Customer $customer, Merchant $merchant, array $approvedFields): Member
    {
        $approvedFields = array_values(array_intersect(self::SHAREABLE_FIELDS, $approvedFields));

        if (! in_array('name', $approvedFields, true)) {
            throw new InvalidArgumentException(__('identity.error_name_required'));
        }

        if ($customer->liveLinks()->where('merchant_id', $merchant->id)->exists()) {
            throw new InvalidArgumentException(__('identity.error_already_member'));
        }

        return DB::transaction(function () use ($customer, $merchant, $approvedFields) {
            // Consent ledger: one append-only row per shareable field.
            foreach (self::SHAREABLE_FIELDS as $field) {
                Consent::create([
                    'customer_id'     => $customer->id,
                    'merchant_id'     => $merchant->id,
                    'data_type'       => $field,
                    'granted'         => in_array($field, $approvedFields, true),
                    'consent_version' => self::CONSENT_VERSION,
                    'source'          => 'scan_to_join',
                    'acted_at'        => now(),
                ]);
            }

            // Duplicate-membership prevention: an existing member record with
            // the identity's phone at this merchant is connected (consent was
            // just given), never duplicated.
            $member = null;
            if (in_array('phone', $approvedFields, true)) {
                $member = Member::where('merchant_id', $merchant->id)
                    ->where('phone', $customer->phone)
                    ->whereDoesntHave('identityLink')
                    ->first();
            }

            $via = 'scan_to_join';

            if (! $member) {
                $member = Member::create(array_merge(
                    ['merchant_id' => $merchant->id, 'status' => MemberStatus::Active],
                    $this->approvedAttributes($customer, $approvedFields),
                ));
            }

            $this->linkIfMissing($customer, $member, $via);

            AuditLog::record('identity.scan_join', $customer, [], [
                'onemember_id'    => $customer->onemember_id,
                'member_id'       => $member->id,
                'approved_fields' => $approvedFields,
            ], $merchant->id);

            return $member;
        });
    }

    /** @return array<string, mixed> member attributes limited to approved fields */
    private function approvedAttributes(Customer $customer, array $approvedFields): array
    {
        $map = [
            'name'        => $customer->name,
            'phone'       => $customer->phone,
            'email'       => $customer->email,
            'birthday'    => $customer->birthday?->format('Y-m-d'),
            'postal_code' => $customer->postal_code,
        ];

        return array_intersect_key($map, array_flip($approvedFields));
    }

    private function linkIfMissing(Customer $customer, Member $member, string $via): void
    {
        $exists = CustomerMemberLink::where('member_id', $member->id)
            ->whereNull('unlinked_at')
            ->exists();

        $liveAtMerchant = $customer->liveLinks()
            ->where('merchant_id', $member->merchant_id)
            ->exists();

        if ($exists || $liveAtMerchant) {
            return;
        }

        CustomerMemberLink::create([
            'customer_id' => $customer->id,
            'member_id'   => $member->id,
            'merchant_id' => $member->merchant_id,
            'linked_via'  => $via,
            'linked_at'   => now(),
        ]);

        AuditLog::record('identity.linked', $customer, [], [
            'member_id' => $member->id,
            'via'       => $via,
        ], $member->merchant_id);
    }

    private function normalisePhone(?string $phone): string
    {
        return preg_replace('/[^0-9+]/', '', (string) $phone) ?? '';
    }

    private function sign(string $onememberId): string
    {
        $key = hash('sha256', 'onemember-identity:' . config('app.key'), true);

        return substr(hash_hmac('sha256', $onememberId, $key), 0, 16);
    }
}
