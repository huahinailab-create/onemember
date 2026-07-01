<?php

namespace App\Http\Controllers\DevTools;

use App\Services\DevTools\DevAuditLogger;
use App\Services\DevTools\DevSystemService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DevPerformanceController extends DevController
{
    private const ALLOWED_COMMANDS = [
        'optimize', 'optimize:clear', 'cache:clear', 'config:clear',
        'route:clear', 'view:clear', 'event:clear', 'queue:restart',
    ];

    public function __construct(
        DevAuditLogger $audit,
        private readonly DevSystemService $system,
    ) {
        parent::__construct($audit);
    }

    public function index(): View
    {
        return view('dev.performance');
    }

    public function run(Request $request): RedirectResponse
    {
        $request->validate(['command' => 'required|string|in:' . implode(',', self::ALLOWED_COMMANDS)]);

        $command = $request->command;
        $this->audit->log('performance.' . str_replace(':', '_', $command));
        $output = $this->system->runArtisan($command);

        return back()->with('success', "Ran `{$command}`: " . (trim($output) ?: 'Done.'));
    }
}
