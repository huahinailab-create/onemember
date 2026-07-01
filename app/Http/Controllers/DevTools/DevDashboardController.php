<?php

namespace App\Http\Controllers\DevTools;

use App\Services\DevTools\DevAuditLogger;
use App\Services\DevTools\DevDemoService;
use App\Services\DevTools\DevSystemService;
use Illuminate\View\View;

class DevDashboardController extends DevController
{
    public function __construct(
        DevAuditLogger $audit,
        private readonly DevDemoService $demo,
        private readonly DevSystemService $system,
    ) {
        parent::__construct($audit);
    }

    public function index(): View
    {
        $stats  = $this->demo->getStats();
        $health = $this->system->getSystemHealth();
        $env    = $this->system->getEnvironmentInfo();
        return view('dev.dashboard', compact('stats', 'health', 'env'));
    }
}
