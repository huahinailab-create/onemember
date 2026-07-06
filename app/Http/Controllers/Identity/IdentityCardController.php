<?php

namespace App\Http\Controllers\Identity;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\CustomerPortalService;
use App\Services\IdentityService;

/**
 * The OneMember Card (PH2-001A): the customer's portable identity card.
 * Public by unguessable public_uuid (same trust model as the Phase 1 member
 * portal). Displays only what the card must show — the QR encodes a signed
 * token, never personal data. Theme is structured for Apple/Google Wallet
 * passes later (PH2-001E).
 */
class IdentityCardController extends Controller
{
    public function show(string $publicUuid, IdentityService $identity, CustomerPortalService $portal)
    {
        abort_unless(config('features.identity'), 404);

        $customer = Customer::where('public_uuid', $publicUuid)->firstOrFail();

        app()->setLocale(in_array($customer->locale, ['en', 'th'], true) ? $customer->locale : 'th');

        return view('identity.card', [
            'customer' => $customer,
            'qrSvg'    => $portal->qrCodeSvg($identity->qrPayload($customer)),
        ]);
    }
}
