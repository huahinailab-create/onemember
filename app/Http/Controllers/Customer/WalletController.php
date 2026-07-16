<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Wallet\WalletService;
use Illuminate\Http\Request;

/**
 * CUSTOMER-001C — the OneMember Wallet: the customer's home inside the
 * ecosystem. Read-only in the MVP (no redemption, no payments). Every route
 * is behind the `customer` guard and every query starts from the signed-in
 * customer's own consented links — cross-customer and merchant access are
 * impossible by construction.
 */
class WalletController extends Controller
{
    public function __construct(private WalletService $wallet)
    {
    }

    public function home(Request $request)
    {
        $customer = $request->user('customer');

        return view('customer.wallet.home', [
            'customer'    => $customer,
            'summary'     => $this->wallet->summary($customer),
            'memberships' => $this->wallet->memberships($customer)->take(3),
            'activity'    => $this->wallet->activity($customer, 5),
        ]);
    }

    public function memberships(Request $request)
    {
        $customer = $request->user('customer');

        return view('customer.wallet.memberships', [
            'customer'     => $customer,
            'memberships'  => $this->wallet->memberships($customer),
            'rewardCounts' => $this->wallet->availableRewardCounts($customer),
        ]);
    }

    public function membership(Request $request, string $memberUuid)
    {
        $customer = $request->user('customer');
        $link     = $this->wallet->membership($customer, $memberUuid);
        abort_if($link === null, 404); // foreign membership: never confirm existence

        $member = $link->member;

        return view('customer.wallet.membership', [
            'customer'     => $customer,
            'link'         => $link,
            'member'       => $member,
            'merchant'     => $member->merchant,
            'programme'    => $member->merchant->loyaltyPrograms->first(),
            'unit'         => $this->wallet->balanceUnit($link),
            'rewards'      => $this->wallet->rewardsByMerchant($customer)
                ->get($member->merchant->displayName(), collect()),
            'transactions' => $member->transactions()->latest('created_at')->limit(10)->get(),
        ]);
    }

    public function rewards(Request $request)
    {
        $customer = $request->user('customer');

        return view('customer.wallet.rewards', [
            'customer'          => $customer,
            'rewardsByMerchant' => $this->wallet->rewardsByMerchant($customer),
        ]);
    }

    public function activity(Request $request)
    {
        $customer = $request->user('customer');

        return view('customer.wallet.activity', [
            'customer' => $customer,
            'activity' => $this->wallet->activity($customer, 50),
        ]);
    }

    public function orders(Request $request)
    {
        $customer = $request->user('customer');

        return view('customer.wallet.orders', [
            'customer' => $customer,
            'orders'   => $this->wallet->orders($customer),
        ]);
    }
}
