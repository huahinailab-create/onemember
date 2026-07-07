<?php

namespace App\Http\Controllers;

use App\Enums\MerchantStatus;
use App\Models\Merchant;
use Illuminate\Http\Request;

/**
 * Public per-merchant join landing page (RELEASE-5A).
 *
 * Information-only: it tells the customer about the merchant's loyalty
 * programme and the join offer, and asks them to speak to staff. Enrolment
 * itself stays with staff via the existing member flows — this page collects
 * no data and creates no records. (Customer self-enrolment and the wallet
 * join flow are Phase 2 — out of scope here by design.)
 */
class JoinLandingController extends Controller
{
    public function show(Request $request, string $slug)
    {
        $merchant = Merchant::where('slug', $slug)
            ->where('status', MerchantStatus::Active)
            ->firstOrFail();

        // Customer-language settings (BETA-008B); ?lang= wins when offered.
        app()->setLocale($merchant->resolveCustomerLocale($request->query('lang')));

        $offer = $request->query('offer', 'coffee');
        if (! in_array($offer, LaunchKitController::OFFERS, true)) {
            $offer = 'coffee';
        }

        return view('join.show', compact('merchant', 'offer'));
    }
}
