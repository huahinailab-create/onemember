<?php

namespace App\Providers;

use App\Contracts\InsightProviderInterface;
use App\Services\Intelligence\RuleBasedInsightProvider;
use App\Services\Media\Contracts\ImagePipeline;
use App\Services\Media\GdImagePipeline;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(InsightProviderInterface::class, RuleBasedInsightProvider::class);

        // OMEGA-001C — default to a no-op pipeline so MediaService's
        // optimize()/variant() calls change nothing until a real
        // Intervention/Imagick-backed pipeline is bound in its place.
        // OMEGA merge: real GD pipeline (WebP ≤ media.max_edge) is the default.
        $this->app->bind(ImagePipeline::class, GdImagePipeline::class);

        // CUSTOMER-001A — SMS delivery seam, config-driven so a real
        // gateway is one class + one config value (see ADR-016).
        $this->app->bind(
            \App\Services\CustomerIdentity\Contracts\SmsProvider::class,
            function () {
                $key = config('customer_identity.sms_provider', 'log');
                $class = config("customer_identity.sms_providers.{$key}")
                    ?? \App\Services\CustomerIdentity\LogSmsProvider::class;

                return new $class();
            },
        );
    }

    public function boot(): void
    {
        // ADR-007: "Campaign" is the product-layer name for LoyaltyProgram.
        // The alias lets new code use the product vocabulary without a
        // schema migration. Both names resolve to the same class.
        if (! class_exists(\App\Models\Campaign::class, false)) {
            class_alias(\App\Models\LoyaltyProgram::class, \App\Models\Campaign::class);
        }

        Password::defaults(function () {
            return Password::min(12)
                ->mixedCase()
                ->numbers()
                ->symbols();
        });

        // PLATFORM-002 P5: public API rate limit — per API key when
        // authenticated, per IP for the unauthenticated ping.
        \Illuminate\Support\Facades\RateLimiter::for('api', function (\Illuminate\Http\Request $request) {
            $key = $request->bearerToken()
                ? 'key:' . hash('sha256', $request->bearerToken())
                : 'ip:' . $request->ip();

            return \Illuminate\Cache\RateLimiting\Limit::perMinute(60)->by($key);
        });

        // CUSTOMER-001A — customer-auth throttles. Password lockout keys on
        // identifier+IP (config-tunable); OTP request/verify and register
        // key on IP — per-destination send limits live inside OtpService.
        \Illuminate\Support\Facades\RateLimiter::for('customer-login', function (\Illuminate\Http\Request $request) {
            $key = mb_strtolower((string) $request->input('identifier')) . '|' . $request->ip();

            return \Illuminate\Cache\RateLimiting\Limit::perMinutes(
                (int) ceil(config('customer_identity.login.decay_seconds') / 60),
                config('customer_identity.login.max_attempts'),
            )->by($key);
        });
        \Illuminate\Support\Facades\RateLimiter::for('customer-otp-request', fn (\Illuminate\Http\Request $r) =>
            \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($r->ip()));
        \Illuminate\Support\Facades\RateLimiter::for('customer-otp-verify', fn (\Illuminate\Http\Request $r) =>
            \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by($r->ip()));
        \Illuminate\Support\Facades\RateLimiter::for('customer-register', fn (\Illuminate\Http\Request $r) =>
            \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($r->ip()));

        // Share absolute app-domain URL with all corporate views so CTAs
        // link to app.onemember.co without relying on route() which would
        // generate a domain-constrained URL pointing to the wrong domain.
        View::composer(['corporate.*', 'layouts.corporate'], function ($view) {
            $view->with('appUrl', 'https://' . config('domains.app'));
        });

        // TD-003: branding is composed here instead of instantiating the
        // service inside Blade views (keeps views container-free).
        View::composer(['layouts.app', 'settings.index'], function ($view) {
            $view->with('merchantBranding', new \App\Services\MerchantBrandingService(
                \Illuminate\Support\Facades\Auth::user()?->merchant
            ));
        });
    }
}
