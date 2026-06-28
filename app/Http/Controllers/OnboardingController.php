<?php

namespace App\Http\Controllers;

use App\Enums\CampaignStatus;
use App\Enums\LoyaltyProgramType;
use App\Enums\MerchantStatus;
use App\Enums\RewardStatus;
use App\Enums\RewardType;
use App\Http\Requests\StoreOnboardingBusinessInfoRequest;
use App\Http\Requests\StoreOnboardingBusinessSettingsRequest;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OnboardingController extends Controller
{
    public function index(Request $request)
    {
        $merchant = $request->user()->merchant;

        if ($merchant && $merchant->onboarding_completed_at) {
            return redirect()->route('dashboard');
        }

        if (! $merchant) {
            return redirect()->route('onboarding.welcome');
        }

        $step = $merchant->settings['onboarding_step'] ?? 0;

        return match (true) {
            $step >= 5  => redirect()->route('onboarding.finish'),
            $step >= 4  => redirect()->route('onboarding.quick-start'),
            $step >= 3  => redirect()->route('onboarding.loyalty'),
            $step >= 2  => redirect()->route('onboarding.business-settings'),
            default     => redirect()->route('onboarding.welcome'),
        };
    }

    public function welcome(Request $request)
    {
        $merchant = $request->user()->merchant;

        if ($merchant && $merchant->onboarding_completed_at) {
            return redirect()->route('dashboard');
        }

        return view('onboarding.welcome');
    }

    public function skip(Request $request)
    {
        session(['onboarding_skipped' => true]);

        return redirect()->route('dashboard');
    }

    public function businessInfo(Request $request)
    {
        $merchant = $request->user()->merchant;

        if ($merchant && $merchant->onboarding_completed_at) {
            return redirect()->route('dashboard');
        }

        return view('onboarding.business-info', compact('merchant'));
    }

    public function storeBusinessInfo(StoreOnboardingBusinessInfoRequest $request)
    {
        $user     = $request->user();
        $merchant = $user->merchant;
        $data     = $request->validated();

        if ($merchant) {
            $settings                   = $merchant->settings ?? [];
            $settings['onboarding_step'] = 2;
            $merchant->update(array_merge($data, ['settings' => $settings]));
        } else {
            Merchant::create(array_merge($data, [
                'user_id'  => $user->id,
                'email'    => $user->email,
                'status'   => MerchantStatus::Active,
                'currency' => 'THB',
                'timezone' => 'Asia/Bangkok',
                'settings' => ['onboarding_step' => 2],
            ]));
        }

        return redirect()->route('onboarding.business-settings');
    }

    public function businessSettings(Request $request)
    {
        $merchant = $request->user()->merchant;

        if (! $merchant) {
            return redirect()->route('onboarding.business-info');
        }
        if ($merchant->onboarding_completed_at) {
            return redirect()->route('dashboard');
        }

        return view('onboarding.business-settings', compact('merchant'));
    }

    public function storeBusinessSettings(StoreOnboardingBusinessSettingsRequest $request)
    {
        $merchant = $request->user()->merchant;
        abort_unless($merchant, 403);

        $validated = $request->validated();
        $settings  = $merchant->settings ?? [];
        $settings['onboarding_step'] = 3;
        $settings['date_format']     = $validated['date_format'];

        $merchant->update([
            'currency' => $validated['currency'],
            'timezone' => $validated['timezone'],
            'settings' => $settings,
        ]);

        return redirect()->route('onboarding.loyalty');
    }

    public function loyaltyPreference(Request $request)
    {
        $merchant = $request->user()->merchant;

        if (! $merchant) {
            return redirect()->route('onboarding.business-info');
        }
        if ($merchant->onboarding_completed_at) {
            return redirect()->route('dashboard');
        }
        if (($merchant->settings['onboarding_step'] ?? 0) < 2) {
            return redirect()->route('onboarding.business-settings');
        }

        return view('onboarding.loyalty-preference', compact('merchant'));
    }

    public function storeLoyaltyPreference(Request $request)
    {
        $merchant = $request->user()->merchant;
        abort_unless($merchant, 403);

        $request->validate(['loyalty_type' => ['required', 'in:points,stamps']]);

        $settings                          = $merchant->settings ?? [];
        $settings['onboarding_step']       = 4;
        $settings['onboarding_loyalty_type'] = $request->input('loyalty_type');
        $merchant->update(['settings' => $settings]);

        return redirect()->route('onboarding.quick-start');
    }

    public function quickStart(Request $request)
    {
        $merchant = $request->user()->merchant;

        if (! $merchant) {
            return redirect()->route('onboarding.business-info');
        }
        if ($merchant->onboarding_completed_at) {
            return redirect()->route('dashboard');
        }
        if (($merchant->settings['onboarding_step'] ?? 0) < 3) {
            return redirect()->route('onboarding.loyalty');
        }

        $loyaltyType    = $merchant->settings['onboarding_loyalty_type'] ?? 'points';
        $hasCampaigns   = $merchant->loyaltyPrograms()->withTrashed()->exists();

        return view('onboarding.quick-start', compact('merchant', 'loyaltyType', 'hasCampaigns'));
    }

    public function storeQuickStart(Request $request)
    {
        $merchant = $request->user()->merchant;
        abort_unless($merchant, 403);

        $request->validate(['choice' => ['required', 'in:yes,no']]);

        if ($request->input('choice') === 'yes') {
            if ($merchant->loyaltyPrograms()->withTrashed()->doesntExist()) {
                $loyaltyType = $merchant->settings['onboarding_loyalty_type'] ?? 'points';
                $this->createStarterCampaign($merchant, $loyaltyType);
            }
        }

        $settings                   = $merchant->settings ?? [];
        $settings['onboarding_step'] = 5;
        $merchant->update([
            'onboarding_completed_at' => now(),
            'settings'                => $settings,
        ]);

        return redirect()->route('onboarding.finish');
    }

    public function finish(Request $request)
    {
        $merchant = $request->user()->merchant;

        if (! $merchant || ! $merchant->onboarding_completed_at) {
            return redirect()->route('onboarding.index');
        }

        return view('onboarding.finish', compact('merchant'));
    }

    private function createStarterCampaign(Merchant $merchant, string $type): void
    {
        if ($type === 'stamps') {
            $campaign = $merchant->loyaltyPrograms()->create([
                'name'     => 'Stamp Card',
                'type'     => LoyaltyProgramType::Stamps,
                'status'   => CampaignStatus::Active,
                'settings' => [
                    'stamps_required'    => 10,
                    'reward_description' => 'Complete your stamp card to claim your reward.',
                ],
            ]);

            $campaign->rewards()->create([
                'merchant_id'        => $merchant->id,
                'name'               => 'Free Item',
                'type'               => RewardType::FreeItem,
                'status'             => RewardStatus::Active,
                'points_required'    => null,
                'quantity_available' => null,
            ]);
        } else {
            $campaign = $merchant->loyaltyPrograms()->create([
                'name'     => 'Points Rewards Program',
                'type'     => LoyaltyProgramType::Points,
                'status'   => CampaignStatus::Active,
                'settings' => [
                    'spend_amount'               => 100,
                    'points_awarded'             => 1,
                    'expiration_type'            => 'never',
                    'expiration_duration'        => null,
                    'birthday_enabled'           => false,
                    'birthday_points'            => null,
                    'birthday_valid_days_before' => 7,
                    'birthday_valid_days_after'  => 7,
                ],
            ]);

            $campaign->rewards()->create([
                'merchant_id'        => $merchant->id,
                'name'               => 'Free Item',
                'type'               => RewardType::Custom,
                'status'             => RewardStatus::Active,
                'points_required'    => 500,
                'quantity_available' => null,
            ]);
        }
    }
}
