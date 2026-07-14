<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Services\CustomerPortalService;
use Illuminate\Http\Request;

class LaunchKitController extends Controller
{
    // Free-item offers the campaign copy supports (RELEASE-5A). The copy is
    // keyed per offer in lang/*/launch.php so new offers only add lang keys.
    public const OFFERS = ['coffee', 'dessert', 'discount', 'gift'];

    public function index(Request $request, CustomerPortalService $portal)
    {
        [$merchant, $offer] = $this->merchantAndOffer($request);

        app(\App\Services\LaunchChecklistService::class)->markFlag($merchant, 'launch_kit_opened');

        return view('launch-kit.index', [
            'merchant'   => $merchant,
            'offer'      => $offer,
            'offers'     => self::OFFERS,
            'joinUrl'    => $this->joinUrl($merchant),
            'joinQrSvg'  => $portal->qrCodeSvg($this->joinUrl($merchant)),
        ]);
    }

    public function poster(Request $request, CustomerPortalService $portal)
    {
        [$merchant, $offer] = $this->merchantAndOffer($request);

        // MR-001 launch checklist: the QR poster has been viewed/printed.
        app(\App\Services\LaunchChecklistService::class)->markFlag($merchant, 'qr_poster_viewed');

        return view('launch-kit.poster', [
            'merchant'  => $merchant,
            'offer'     => $offer,
            'joinUrl'   => $this->joinUrl($merchant),
            'joinQrSvg' => $portal->qrCodeSvg($this->joinUrl($merchant)),
        ]);
    }

    public function counterCard(Request $request, CustomerPortalService $portal)
    {
        [$merchant, $offer] = $this->merchantAndOffer($request);

        return view('launch-kit.counter-card', [
            'merchant'  => $merchant,
            'offer'     => $offer,
            'joinUrl'   => $this->joinUrl($merchant),
            'joinQrSvg' => $portal->qrCodeSvg($this->joinUrl($merchant)),
        ]);
    }

    public function staffGuide(Request $request, CustomerPortalService $portal)
    {
        [$merchant, $offer] = $this->merchantAndOffer($request);

        return view('launch-kit.staff-guide', [
            'merchant'     => $merchant,
            'offer'        => $offer,
            'counterUrl'   => route('counter'),
            'counterQrSvg' => $portal->qrCodeSvg(route('counter')),
        ]);
    }

    /** @return array{0: Merchant, 1: string} */
    private function merchantAndOffer(Request $request): array
    {
        $merchant = $request->user()->merchant;
        abort_unless($merchant, 403);

        $offer = $request->query('offer', 'coffee');
        if (! in_array($offer, self::OFFERS, true)) {
            $offer = 'coffee';
        }

        return [$merchant, $offer];
    }

    private function joinUrl(Merchant $merchant): string
    {
        return route('join.show', $merchant->slug);
    }
}
