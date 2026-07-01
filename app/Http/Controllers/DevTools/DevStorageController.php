<?php

namespace App\Http\Controllers\DevTools;

use App\Services\DevTools\DevAuditLogger;
use App\Services\DevTools\DevSystemService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class DevStorageController extends DevController
{
    public function __construct(
        DevAuditLogger $audit,
        private readonly DevSystemService $system,
    ) {
        parent::__construct($audit);
    }

    public function index(): View
    {
        $logSize   = file_exists(storage_path('logs/laravel.log'))
            ? filesize(storage_path('logs/laravel.log'))
            : 0;
        return view('dev.storage', compact('logSize'));
    }

    public function clearLogs(): RedirectResponse
    {
        $this->audit->log('storage.clear_logs');
        $this->system->clearLogs();
        return back()->with('success', "Laravel log cleared.");
    }

    public function downloadLog(): Response
    {
        $path = storage_path('logs/laravel.log');
        if (! file_exists($path)) {
            return back()->with('error', 'Log file not found.');
        }
        $this->audit->log('storage.download_log');
        return response()->download($path, 'laravel.log');
    }

    public function clearSessions(): RedirectResponse
    {
        $this->audit->log('storage.clear_sessions');
        $this->system->clearSessions();
        return back()->with('success', "Sessions cleared.");
    }

    public function storageLink(): RedirectResponse
    {
        $this->audit->log('storage.storage_link');
        $output = $this->system->storageLink();
        return back()->with('success', "Storage link: " . trim($output));
    }
}
