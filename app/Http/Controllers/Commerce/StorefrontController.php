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

        // MR-001 launch checklist: mark "storefront visited" only when the
        // authenticated owner is viewing their OWN storefront — a public
        // visitor or another merchant must never flip this tenant's flag.
        if ($request->user()?->merchant?->id === $merchant->id) {
            app(\App\Services\LaunchChecklistService::class)->markFlag($merchant, 'storefront_visited');
        }

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

        // CUSTOMER-001B — a signed-in customer checks out from their address
        // book (active addresses only, default first). Guests see the plain
        // address field; nothing about the book reaches the merchant.
        $customer          = $request->user('customer');
        $customerAddresses = $customer
            ? $customer->addresses()->active()->orderByDesc('is_default')->orderByDesc('updated_at')->get()
            : collect();

        return view('storefront.show', compact('merchant', 'products', 'campaign', 'rewards', 'commerce', 'customer', 'customerAddresses'));
    }
}
