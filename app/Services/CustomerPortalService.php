<?php

namespace App\Services;

use App\Enums\CampaignStatus;
use App\Enums\LoyaltyProgramType;
use App\Enums\RedemptionStatus;
use App\Enums\RewardStatus;
use App\Models\Member;
use App\Models\Merchant;
use Picqer\Barcode\BarcodeGeneratorSVG;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CustomerPortalService
{
    public function isPortalEnabled(Member $member): bool
    {
        return (bool) $member->portal_enabled && ! $member->trashed();
    }

    /**
     * Build customer-safe portal data. Never expose internal IDs, notes,
     * phone, email, staff names, or purchase amounts.
     */
    public function buildPortalData(Member $member): array
    {
        $merchant  = $member->merchant;
        $campaigns = $this->buildCampaignData($member, $merchant);
        $birthday  = $this->buildBirthdayData($member, $merchant);

        return [
            'merchant_name'  => $merchant->name,
            'member_name'    => $member->name,
            'member_code'    => $member->member_code,
            'member_since'   => $member->joined_at,
            'last_visit'     => $member->last_activity_at,
            'total_points'   => $member->total_points,
            'campaigns'      => $campaigns,
            'redemptions'    => $this->buildRedemptionData($member),
            'birthday'       => $birthday,
        ];
    }

    /**
     * Generate an inline SVG QR code for the given URL.
     * Output is always the same for the same URL (deterministic).
     */
    public function qrCodeSvg(string $url): string
    {
        return (string) QrCode::format('svg')
            ->size(220)
            ->margin(1)
            ->errorCorrection('M')
            ->generate($url);
    }

    /**
     * Generate a Code128 barcode as an SVG string for the given data.
     */
    public function barcodeSvg(string $data): string
    {
        $generator = new BarcodeGeneratorSVG();
        return $generator->getBarcode($data, BarcodeGeneratorSVG::TYPE_CODE_128, 2, 80);
    }

    // ── Future email stubs ────────────────────────────────────────────────

    /**
     * Prepare data for a "Send Member Card" email (not yet sent automatically).
     * @future Sprint 7.x — attach digital member card PDF or image
     */
    public function prepareMemberCardEmail(Member $member): array
    {
        return [
            'member_name'  => $member->name,
            'member_code'  => $member->member_code,
            'portal_url'   => route('portal.show', $member->public_uuid),
            'qr_url'       => route('portal.qr', $member->public_uuid),
            'merchant_name' => $member->merchant->name,
        ];
    }

    /**
     * Prepare data for a "Send QR Code" email (not yet sent automatically).
     * @future Sprint 7.x
     */
    public function prepareQrEmail(Member $member): array
    {
        return [
            'member_name'  => $member->name,
            'portal_url'   => route('portal.show', $member->public_uuid),
            'qr_url'       => route('portal.qr', $member->public_uuid),
            'merchant_name' => $member->merchant->name,
        ];
    }

    /**
     * Prepare data for a "Welcome Card" email (not yet sent automatically).
     * @future Sprint 7.x
     */
    public function prepareWelcomeEmail(Member $member): array
    {
        return [
            'member_name'  => $member->name,
            'member_code'  => $member->member_code,
            'portal_url'   => route('portal.show', $member->public_uuid),
            'merchant_name' => $member->merchant->name,
            'joined_at'    => $member->joined_at,
        ];
    }

    // ── Private helpers ───────────────────────────────────────────────────

    private function buildCampaignData(Member $member, Merchant $merchant): array
    {
        $programs = $merchant->loyaltyPrograms()
            ->where('status', CampaignStatus::Active->value)
            ->whereNull('deleted_at')
            ->with(['rewards' => function ($q) {
                $q->where('status', RewardStatus::Active->value)
                  ->whereNull('deleted_at')
                  ->orderBy('points_required');
            }, 'birthdayRewards' => function ($q) {
                $q->where('is_active', true);
            }])
            ->get();

        return $programs->map(function ($program) use ($member) {
            $isStamps     = $program->type === LoyaltyProgramType::Stamps;
            $balance      = $member->total_points;
            $stampsGoal   = $isStamps ? (int) ($program->settings['stamps_required'] ?? 10) : null;

            $available = [];
            $locked    = [];

            foreach ($program->rewards as $reward) {
                $required  = $isStamps ? ($stampsGoal ?? PHP_INT_MAX) : $reward->points_required;
                $canRedeem = $balance >= $required;

                $rewardData = [
                    'name'           => $reward->name,
                    'description'    => $reward->description,
                    'points_required' => $reward->points_required,
                    'required_label' => $isStamps
                        ? __('portal.stamps_required', ['n' => $stampsGoal])
                        : __('portal.points_required', ['n' => $reward->points_required]),
                    'progress'       => $required > 0 ? min(100, (int) round($balance / $required * 100)) : 100,
                    'remaining'      => max(0, $required - $balance),
                ];

                if ($canRedeem) {
                    $available[] = $rewardData;
                } else {
                    $locked[] = $rewardData;
                }
            }

            return [
                'name'        => $program->name,
                'type'        => $program->type->value,
                'type_label'  => $program->type->label(),
                'balance'     => $balance,
                'stamps_goal' => $stampsGoal,
                'available'   => $available,
                'locked'      => $locked,
            ];
        })->values()->all();
    }

    private function buildRedemptionData(Member $member): array
    {
        return $member->redemptions()
            ->where('status', RedemptionStatus::Used->value)
            ->with('reward:id,name')
            ->latest('redeemed_at')
            ->limit(5)
            ->get()
            ->map(fn ($r) => [
                'reward_name'  => $r->reward?->name ?? __('portal.reward_deleted'),
                'redeemed_at'  => $r->redeemed_at,
            ])
            ->all();
    }

    private function buildBirthdayData(Member $member, Merchant $merchant): ?array
    {
        if (! $member->birthday) {
            return null;
        }

        $birthdayReward = $merchant->birthdayRewards()
            ->where('is_active', true)
            ->with('reward:id,name,description')
            ->first();

        if (! $birthdayReward || ! $birthdayReward->isEligible($member)) {
            return null;
        }

        return [
            'name'        => $birthdayReward->name,
            'description' => $birthdayReward->reward?->description ?? $birthdayReward->name,
            'is_today'    => $member->isBirthdayToday(),
        ];
    }
}
