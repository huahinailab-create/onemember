<?php

namespace App\Http\Controllers\DevTools;

use App\Services\DevTools\DevAuditLogger;
use App\Services\DevTools\DevSystemService;
use Illuminate\View\View;

class DevHealthController extends DevController
{
    public function __construct(
        DevAuditLogger $audit,
        private readonly DevSystemService $system,
    ) {
        parent::__construct($audit);
    }

    public function index(): View
    {
        $health = $this->system->getSystemHealth();
        return view('dev.health', compact('health'));
    }
}
