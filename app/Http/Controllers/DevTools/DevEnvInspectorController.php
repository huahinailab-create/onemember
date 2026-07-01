<?php

namespace App\Http\Controllers\DevTools;

use App\Services\DevTools\DevAuditLogger;
use App\Services\DevTools\DevSystemService;
use Illuminate\View\View;

class DevEnvInspectorController extends DevController
{
    public function __construct(
        DevAuditLogger $audit,
        private readonly DevSystemService $system,
    ) {
        parent::__construct($audit);
    }

    public function index(): View
    {
        $info   = $this->system->getEnvironmentInfo();
        $health = $this->system->getSystemHealth();

        $extra = [
            'timezone'       => config('app.timezone'),
            'cache_driver'   => config('cache.default'),
            'session_driver' => config('session.driver'),
            'queue_driver'   => config('queue.default'),
            'dev_tools_flag' => config('devtools.enabled') ? 'true' : 'false',
        ];

        return view('dev.env-inspector', compact('info', 'health', 'extra'));
    }
}
