<?php

namespace App\Http\Controllers;

use App\Marketplace\AppManager;
use App\Marketplace\AppRegistry;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * CORE-002 — OneMember Apps marketplace + install/uninstall.
 * PLATFORM-002 — lifecycle delegated to Marketplace\AppManager
 * (dependencies, enable/disable, state table, events, audit).
 */
class AppsController extends Controller
{
    public function __construct(
        private readonly AppRegistry $registry,
        private readonly AppManager $manager,
    ) {
    }

    public function index(Request $request)
    {
        $merchant = $request->user()->merchant;
        abort_unless($merchant, 403);

        return view('apps.index', [
            'registry'  => config('apps.registry'),
            'installed' => $merchant->installedApps(),
            'states'    => $merchant->appStates->keyBy('app_key'),
        ]);
    }

    public function install(Request $request)
    {
        $merchant = $request->user()->merchant;
        abort_unless($merchant, 403);

        $key = $this->validatedKey($request);

        $this->manager->install($merchant, $key);

        return redirect()->route('apps.index')
            ->with('success', __('apps.installed_success', ['app' => __('apps.name_' . $key)]));
    }

    public function uninstall(Request $request)
    {
        $merchant = $request->user()->merchant;
        abort_unless($merchant, 403);

        $key = $this->validatedKey($request);

        $this->manager->uninstall($merchant, $key);

        return redirect()->route('apps.index')
            ->with('success', __('apps.uninstalled_success', ['app' => __('apps.name_' . $key)]));
    }

    /** PLATFORM-002 — enable/disable an installed app without touching data. */
    public function toggle(Request $request)
    {
        $merchant = $request->user()->merchant;
        abort_unless($merchant, 403);

        $key = $this->validatedKey($request);

        if ($merchant->hasApp($key)) {
            $this->manager->disable($merchant, $key);
            $message = __('apps.disabled_success', ['app' => __('apps.name_' . $key)]);
        } else {
            $this->manager->enable($merchant, $key);
            $message = __('apps.enabled_success', ['app' => __('apps.name_' . $key)]);
        }

        return redirect()->route('apps.index')->with('success', $message);
    }

    private function validatedKey(Request $request): string
    {
        return $request->validate([
            'app' => ['required', 'string', Rule::in(array_keys(config('apps.registry')))],
        ])['app'];
    }
}
