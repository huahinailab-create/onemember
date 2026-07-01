<?php

namespace App\Http\Controllers\DevTools;

use App\Services\DevTools\DevAuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class DevLogViewerController extends DevController
{
    private string $logPath;

    public function __construct(DevAuditLogger $audit)
    {
        parent::__construct($audit);
        $this->logPath = storage_path('logs/laravel.log');
    }

    public function index(Request $request): View
    {
        $search = $request->get('search', '');
        $level  = $request->get('level', '');
        $lines  = $this->readLog(100, $search, $level);
        $size   = file_exists($this->logPath) ? filesize($this->logPath) : 0;

        return view('dev.log-viewer', compact('lines', 'search', 'level', 'size'));
    }

    public function download(): Response
    {
        $this->audit->log('logs.download');
        if (! file_exists($this->logPath)) {
            abort(404, 'Log file not found');
        }
        return response()->download($this->logPath, 'laravel.log');
    }

    public function clear(): RedirectResponse
    {
        $this->audit->log('logs.clear');
        if (file_exists($this->logPath)) {
            file_put_contents($this->logPath, '');
        }
        return back()->with('success', 'Log file cleared.');
    }

    private function readLog(int $tail, string $search, string $level): array
    {
        if (! file_exists($this->logPath)) {
            return [];
        }

        $lines = array_reverse(array_slice(
            file($this->logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [],
            -500
        ));

        if ($level) {
            $lines = array_filter($lines, fn ($l) => stripos($l, ".{$level}:") !== false || stripos($l, "[{$level}]") !== false);
        }

        if ($search) {
            $lines = array_filter($lines, fn ($l) => stripos($l, $search) !== false);
        }

        return array_slice(array_values($lines), 0, $tail);
    }
}
