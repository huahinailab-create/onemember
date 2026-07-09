<?php

namespace App\Marketplace;

use App\Marketplace\Events\AppDisabled;
use App\Marketplace\Events\AppEnabled;
use App\Marketplace\Events\AppInstalled;
use App\Marketplace\Events\AppUninstalled;
use App\Models\AuditLog;
use App\Models\Merchant;
use App\Models\MerchantApp;
use Illuminate\Validation\ValidationException;

/**
 * PLATFORM-002 Part 1 — install / uninstall / enable / disable / configure.
 *
 * Wraps the legacy settings-JSON install list (kept in sync for backward
 * compatibility) and the merchant_apps state table. All lifecycle changes
 * are audited and dispatch marketplace events.
 */
class AppManager
{
    public function __construct(private readonly AppRegistry $registry)
    {
    }

    public function install(Merchant $merchant, string $key): void
    {
        $manifest = $this->registry->get($key);

        if (! $manifest || ! $manifest->isAvailable()) {
            throw ValidationException::withMessages(['app' => __('apps.error_coming_soon')]);
        }

        // Dependencies must be installed first.
        foreach ($manifest->dependencies as $dependency) {
            if (! in_array($dependency, $merchant->installedApps(), true)) {
                throw ValidationException::withMessages([
                    'app' => __('apps.error_missing_dependency', [
                        'app'        => $manifest->name(),
                        'dependency' => __('apps.name_' . $dependency),
                    ]),
                ]);
            }
        }

        $apps = $merchant->installedApps();
        if (! in_array($key, $apps, true)) {
            $apps[] = $key;
            $merchant->update(['settings' => array_merge($merchant->settings ?? [], [
                'installed_apps' => array_values($apps),
            ])]);
        }

        MerchantApp::updateOrCreate(
            ['merchant_id' => $merchant->id, 'app_key' => $key],
            ['version' => $manifest->version, 'enabled' => true, 'installed_at' => now(),
             'config' => $manifest->defaultConfig ?: null],
        );

        AuditLog::record('app.installed', $merchant, [], ['app' => $key, 'version' => $manifest->version], $merchant->id);
        event(new AppInstalled($merchant, $key, $manifest->version));
    }

    public function uninstall(Merchant $merchant, string $key): void
    {
        // Installed apps depending on this one block uninstall.
        $blocking = array_values(array_intersect(
            $this->registry->dependentsOf($key),
            $merchant->installedApps(),
        ));

        if ($blocking !== []) {
            throw ValidationException::withMessages([
                'app' => __('apps.error_dependents_installed', [
                    'apps' => implode(', ', array_map(fn ($k) => __('apps.name_' . $k), $blocking)),
                ]),
            ]);
        }

        $apps = array_values(array_diff($merchant->installedApps(), [$key]));

        // Uninstall disables access; App data is retained dormant (DR-34
        // pending a full uninstall-data policy — nothing is deleted here).
        $merchant->update(['settings' => array_merge($merchant->settings ?? [], [
            'installed_apps' => $apps,
        ])]);

        MerchantApp::where('merchant_id', $merchant->id)->where('app_key', $key)->delete();

        AuditLog::record('app.uninstalled', $merchant, [], ['app' => $key], $merchant->id);
        event(new AppUninstalled($merchant, $key));
    }

    public function enable(Merchant $merchant, string $key): void
    {
        $this->setEnabled($merchant, $key, true);
        AuditLog::record('app.enabled', $merchant, [], ['app' => $key], $merchant->id);
        event(new AppEnabled($merchant, $key));
    }

    /** Disable keeps the app installed (and its data) but gates all access. */
    public function disable(Merchant $merchant, string $key): void
    {
        $this->setEnabled($merchant, $key, false);
        AuditLog::record('app.disabled', $merchant, [], ['app' => $key], $merchant->id);
        event(new AppDisabled($merchant, $key));
    }

    /** Merge per-merchant app configuration (validated by the caller). */
    public function configure(Merchant $merchant, string $key, array $config): void
    {
        $state = $this->stateRow($merchant, $key);
        $state->update(['config' => array_merge($state->config ?? [], $config)]);
    }

    public function configFor(Merchant $merchant, string $key): array
    {
        $manifest = $this->registry->get($key);
        $state    = MerchantApp::where('merchant_id', $merchant->id)->where('app_key', $key)->first();

        return array_merge($manifest?->defaultConfig ?? [], $state?->config ?? []);
    }

    /** Per-merchant health: installed apps vs registry expectations. */
    public function healthFor(Merchant $merchant): array
    {
        return collect($merchant->installedApps())->map(function (string $key) use ($merchant) {
            $manifest = $this->registry->get($key);
            $state    = MerchantApp::where('merchant_id', $merchant->id)->where('app_key', $key)->first();

            return [
                'key'      => $key,
                'known'    => $manifest !== null,
                'enabled'  => $state?->enabled ?? true,
                'version'  => $state?->version ?? $manifest?->version,
                'outdated' => $manifest && $state && $state->version !== $manifest->version,
            ];
        })->values()->all();
    }

    private function setEnabled(Merchant $merchant, string $key, bool $enabled): void
    {
        if (! in_array($key, $merchant->installedApps(), true)) {
            throw ValidationException::withMessages(['app' => __('apps.error_not_installed')]);
        }

        $this->stateRow($merchant, $key)->update(['enabled' => $enabled]);
    }

    private function stateRow(Merchant $merchant, string $key): MerchantApp
    {
        return MerchantApp::firstOrCreate(
            ['merchant_id' => $merchant->id, 'app_key' => $key],
            ['version' => $this->registry->get($key)?->version ?? '1.0.0', 'enabled' => true, 'installed_at' => now()],
        );
    }
}
