<?php

namespace App\Http\Controllers\DevTools;

use App\Services\DevTools\DevAuditLogger;
use App\Services\DevTools\DevSystemService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DevDatabaseController extends DevController
{
    public function __construct(
        DevAuditLogger $audit,
        private readonly DevSystemService $system,
    ) {
        parent::__construct($audit);
    }

    public function index(): View
    {
        return view('dev.database');
    }

    public function runCommand(Request $request): RedirectResponse
    {
        $request->validate(['command' => 'required|string|in:' . implode(',', $this->allowedCommands())]);
        $command = $request->command;

        $this->audit->log('database.' . str_replace(':', '_', $command));

        $output = match ($command) {
            'cache:clear'    => $this->system->runArtisan('cache:clear'),
            'optimize'       => $this->system->runArtisan('optimize'),
            'optimize:clear' => $this->system->runArtisan('optimize:clear'),
            'config:clear'   => $this->system->runArtisan('config:clear'),
            'route:clear'    => $this->system->runArtisan('route:clear'),
            'view:clear'     => $this->system->runArtisan('view:clear'),
            'queue:restart'  => $this->system->runArtisan('queue:restart'),
            'migrate'        => $this->system->runArtisan('migrate', ['--force' => true]),
            'migrate:rollback' => $this->system->runArtisan('migrate:rollback', ['--force' => true]),
            'db:seed'        => $this->system->runArtisan('db:seed', ['--force' => true]),
            default          => 'Unknown command',
        };

        return back()->with('success', "Ran `{$command}`: " . trim($output ?: 'Done.'));
    }

    public function freshSeed(Request $request): RedirectResponse
    {
        $request->validate(['confirm' => 'required|in:DELETE']);
        $this->audit->log('database.fresh_seed');
        $output = $this->system->runArtisan('migrate:fresh', ['--seed' => true, '--force' => true]);
        return back()->with('success', "Fresh migration + seed complete. " . trim($output));
    }

    private function allowedCommands(): array
    {
        return [
            'cache:clear', 'optimize', 'optimize:clear', 'config:clear',
            'route:clear', 'view:clear', 'queue:restart', 'migrate',
            'migrate:rollback', 'db:seed',
        ];
    }
}
