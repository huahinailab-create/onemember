<?php

namespace App\Http\Controllers\Identity;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\IdentityService;
use Illuminate\Http\Request;
use InvalidArgumentException;

/**
 * Merchant-side scan-to-join workflow (PH2-001A, ADR-010 §6):
 * "Add Existing OneMember Member" → scan card → customer consent → member.
 */
class MemberIdentityController extends Controller
{
    public function addForm(Request $request)
    {
        abort_unless(config('features.identity'), 404);
        abort_unless($request->user()->merchant, 403);

        return view('identity.add');
    }

    public function resolve(Request $request, IdentityService $identity)
    {
        abort_unless(config('features.identity'), 404);
        $merchant = $request->user()->merchant;
        abort_unless($merchant, 403);

        $request->validate(['qr_payload' => ['required', 'string', 'max:100']]);

        $customer = $identity->resolveQr($request->input('qr_payload'));

        if (! $customer) {
            return back()->withErrors(['qr_payload' => __('identity.error_invalid_qr')]);
        }

        if ($customer->liveLinks()->where('merchant_id', $merchant->id)->exists()) {
            return back()->withErrors(['qr_payload' => __('identity.error_already_member')]);
        }

        // Consent screen. Only masked identity hints are shown until the
        // customer approves (ADR-010: merchant never sees profile data
        // before consent).
        return view('identity.consent', [
            'customer'    => $customer,
            'merchant'    => $merchant,
            'fields'      => IdentityService::SHAREABLE_FIELDS,
        ]);
    }

    public function join(Request $request, IdentityService $identity)
    {
        abort_unless(config('features.identity'), 404);
        $merchant = $request->user()->merchant;
        abort_unless($merchant, 403);

        $validated = $request->validate([
            'customer_uuid' => ['required', 'uuid'],
            'fields'        => ['required', 'array', 'min:1'],
            'fields.*'      => ['string', 'in:' . implode(',', IdentityService::SHAREABLE_FIELDS)],
        ]);

        $customer = Customer::where('public_uuid', $validated['customer_uuid'])->firstOrFail();

        try {
            $member = $identity->joinMerchant($customer, $merchant, $validated['fields']);
        } catch (InvalidArgumentException $e) {
            return redirect()->route('members.identity.add')
                ->withErrors(['qr_payload' => $e->getMessage()]);
        }

        return redirect()->route('members.show', $member)
            ->with('success', __('identity.join_success', ['id' => $customer->onemember_id]));
    }
}
