<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Services\AnalyticsService;
use App\Services\CustomerPortalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class CustomerPortalController extends Controller
{
    public function __construct(
        private readonly CustomerPortalService $portalService,
        private readonly AnalyticsService $analytics,
    ) {}

    // ── Public portal routes (no auth) ────────────────────────────────────

    /** GET /member/{public_uuid} */
    public function show(string $publicUuid): mixed
    {
        $member = Member::where('public_uuid', $publicUuid)->firstOrFail();

        if (! $this->portalService->isPortalEnabled($member)) {
            return view('portal.disabled', [
                'branding' => new \App\Services\MerchantBrandingService($member->merchant),
            ]);
        }

        $this->analytics->track(
            'portal_viewed',
            ['via' => 'direct'],
            null,
            $member->merchant_id,
        );

        $portalData = $this->portalService->buildPortalData($member);

        return view('portal.show', [
            'branding'   => new \App\Services\MerchantBrandingService($member->merchant),
            'publicUuid' => $publicUuid,
            'portal'     => $portalData,
        ]);
    }

    /** GET /member/{public_uuid}/card */
    public function card(string $publicUuid): mixed
    {
        $member = Member::where('public_uuid', $publicUuid)->firstOrFail();

        if (! $this->portalService->isPortalEnabled($member)) {
            return view('portal.disabled', [
                'branding' => new \App\Services\MerchantBrandingService($member->merchant),
            ]);
        }

        $this->analytics->track(
            'member_card_downloaded',
            [],
            null,
            $member->merchant_id,
        );

        $branding  = new \App\Services\MerchantBrandingService($member->merchant);
        $qrSvg     = $this->portalService->qrCodeSvg(route('portal.show', $publicUuid));
        $barcodeSvg = $this->portalService->barcodeSvg($member->member_code);

        return view('portal.card', compact('member', 'branding', 'qrSvg', 'barcodeSvg', 'publicUuid'));
    }

    /** GET /member/{public_uuid}/qr.svg — raw SVG for img src */
    public function qrSvg(string $publicUuid): Response
    {
        $member = Member::where('public_uuid', $publicUuid)->firstOrFail();

        $this->analytics->track(
            'qr_scanned',
            [],
            null,
            $member->merchant_id,
        );

        $svg = $this->portalService->qrCodeSvg(route('portal.show', $publicUuid));

        return response($svg, 200, [
            'Content-Type'  => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    // ── Merchant control routes (auth required) ───────────────────────────

    /** PUT /members/{member}/portal/toggle */
    public function togglePortal(Request $request, Member $member): RedirectResponse
    {
        abort_unless($member->merchant_id === $request->user()->merchant?->id, 403);

        $member->update(['portal_enabled' => ! $member->portal_enabled]);

        $status = $member->portal_enabled ? 'enabled' : 'disabled';

        return back()->with('success', __('members.portal_toggled_' . $status));
    }

    /** POST /members/{member}/portal/regenerate */
    public function regenerateQr(Request $request, Member $member): RedirectResponse
    {
        abort_unless($member->merchant_id === $request->user()->merchant?->id, 403);

        $member->update(['public_uuid' => (string) Str::uuid()]);

        return back()->with('success', __('members.portal_qr_regenerated'));
    }
}
