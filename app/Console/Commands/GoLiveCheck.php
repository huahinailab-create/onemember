<?php

namespace App\Console\Commands;

use App\Services\GoLiveChecklistService;
use Illuminate\Console\Command;

class GoLiveCheck extends Command
{
    protected $signature   = 'onemember:go-live-check';
    protected $description = 'Run the private-beta / go-live readiness checklist (config + local state only).';

    public function handle(GoLiveChecklistService $service): int
    {
        $summary = $service->summary();

        $this->table(['Check', 'Status', 'Detail'], array_map(fn ($c) => [
            $c['key'],
            $c['pass'] ? '<fg=green>PASS</>' : ($c['critical'] ? '<fg=red>FAIL</>' : '<fg=yellow>WARN</>'),
            $c['detail'],
        ], $summary['checks']));

        $this->line("Passed {$summary['passed']}/{$summary['total']}.");

        if ($summary['ready']) {
            $this->info('No critical blockers. Ready for private beta (review warnings).');
            return Command::SUCCESS;
        }

        $this->error('Critical checks failed: ' . implode(', ', array_column($summary['critical_failed'], 'key')));
        return Command::FAILURE;
    }
}
