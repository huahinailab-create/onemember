<?php

namespace App\Http\Controllers\Commerce;

use App\Enums\CampaignStatus;
use App\Enums\MerchantStatus;
use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Product;
use App\Models\Reward;
use Illuminate\Http\Request;

/**
 * APP-002 — public Merchant Storefront. Exists only while the merchant has
 * the Commerce App installed and is active. The merchant is the seller; the
 * page shows their profile, catalogue, loyalty programme and rewards
 * (ADR-011: OneMember provides identity/ordering/loyalty rails only).
 */
class StorefrontController extends Controller
{
    public function show(Request $request, string $slug)
    {
        $merchant = Merchant::where('slug', $slug)
            ->where('status', MerchantStatus::Active)
            ->firstOrFail();

        abort_unless($merchant->hasApp('commerce'), 404);

        // Storefront language follows the merchant's customer-language
        // settings (BETA-008B) — an explicit ?lang= wins when offered,
        // never the browser (GLOBAL-001 localization rule).
        app()->setLocale($merchant->resolveCustomerLocale($request->query('lang')));

        $products = Product::where('merchant_id', $merchant->id)
            ->where('status', 'active')
            ->with('category')
            ->orderBy('name')
            ->get()
            ->groupBy(fn (Product $p) => $p->category?->name ?? '');

        $campaign = $merchant->loyaltyPrograms()
            ->where('status', CampaignStatus::Active)
            ->whereNull('deleted_at')
            ->oldest('id')
            ->first();

        $rewards = Reward::where('merchant_id', $merchant->id)
            ->where('status', 'active')
            ->orderBy('points_required')
            ->take(6)
            ->get();

        $commerce = $merchant->settings['commerce'] ?? [];

        return view('storefront.show', compact('merchant', 'products', 'campaign', 'rewards', 'commerce'));
    }
}
