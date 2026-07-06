<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * CORE-002 — OneMember Apps marketplace (placeholder) + install/uninstall.
 * Apps live in config/apps.php; installed state in merchant settings JSON.
 */
class AppsController extends Controller
{
    public function index(Request $request)
    {
        $merchant = $request->user()->merchant;
        abort_unless($merchant, 403);

        return view('apps.index', [
            'registry'  => config('apps.registry'),
            'installed' => $merchant->installedApps(),
        ]);
    }

    public function install(Request $request)
    {
        $merchant = $request->user()->merchant;
        abort_unless($merchant, 403);

        $validated = $request->validate([
            'app' => ['required', 'string', Rule::in(array_keys(config('apps.registry')))],
        ]);

        $key = $validated['app'];

        if (config("apps.registry.{$key}.status") !== 'available') {
            return back()->withErrors(['app' => __('apps.error_coming_soon')]);
        }

        $apps = $merchant->installedApps();
        if (! in_array($key, $apps, true)) {
            $apps[]   = $key;
            $settings = array_merge($merchant->settings ?? [], ['installed_apps' => array_values($apps)]);
            $merchant->update(['settings' => $settings]);

            AuditLog::record('app.installed', $merchant, [], ['app' => $key], $merchant->id);
        }

        return redirect()->route('apps.index')
            ->with('success', __('apps.installed_success', ['app' => __('apps.name_' . $key)]));
    }

    public function uninstall(Request $request)
    {
        $merchant = $request->user()->merchant;
        abort_unless($merchant, 403);

        $validated = $request->validate([
            'app' => ['required', 'string', Rule::in(array_keys(config('apps.registry')))],
        ]);

        $key  = $validated['app'];
        $apps = array_values(array_diff($merchant->installedApps(), [$key]));

        // Uninstall disables access; App data is retained dormant (DR-34
        // pending a full uninstall-data policy — nothing is deleted here).
        $settings = array_merge($merchant->settings ?? [], ['installed_apps' => $apps]);
        $merchant->update(['settings' => $settings]);

        AuditLog::record('app.uninstalled', $merchant, [], ['app' => $key], $merchant->id);

        return redirect()->route('apps.index')
            ->with('success', __('apps.uninstalled_success', ['app' => __('apps.name_' . $key)]));
    }
}
