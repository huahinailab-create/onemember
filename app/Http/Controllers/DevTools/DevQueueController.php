<?php

namespace App\Http\Controllers\DevTools;

use App\Services\DevTools\DevAuditLogger;
use App\Services\DevTools\DevSystemService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DevQueueController extends DevController
{
    public function __construct(
        DevAuditLogger $audit,
        private readonly DevSystemService $system,
    ) {
        parent::__construct($audit);
    }

    public function index(): View
    {
        $stats      = $this->system->getQueueStats();
        $failedJobs = DB::table('failed_jobs')->latest('failed_at')->limit(20)->get();
        return view('dev.queue', compact('stats', 'failedJobs'));
    }

    public function retryFailed(): RedirectResponse
    {
        $this->audit->log('queue.retry_failed');
        $output = $this->system->runArtisan('queue:retry', ['id' => 'all']);
        return back()->with('success', "Retrying all failed jobs. " . trim($output));
    }

    public function deleteFailed(): RedirectResponse
    {
        $this->audit->log('queue.delete_failed');
        $output = $this->system->runArtisan('queue:flush');
        return back()->with('success', "All failed jobs deleted. " . trim($output));
    }

    public function restart(): RedirectResponse
    {
        $this->audit->log('queue.restart');
        $this->system->runArtisan('queue:restart');
        return back()->with('success', "Queue restart signal sent.");
    }
}
