<?php

namespace App\Http\Controllers\DevTools;

use App\Services\DevTools\DevAuditLogger;
use App\Services\DevTools\DevSystemService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DevMailInspectorController extends DevController
{
    public function __construct(
        DevAuditLogger $audit,
        private readonly DevSystemService $system,
    ) {
        parent::__construct($audit);
    }

    public function index(): View
    {
        $rawKey     = config('services.resend.key');
        $mailConfig = [
            'driver'      => config('mail.default'),
            'from_address'=> config('mail.from.address'),
            'from_name'   => config('mail.from.name'),
            'reply_to'    => config('mail.reply_to.address'),
            'resend_key'  => $rawKey ? '***' . substr($rawKey, -4) : 'NOT SET',
            'queue_connection' => config('queue.default'),
        ];

        $lastTest = DB::table('developer_actions')
            ->where('action', 'mail.send_test')
            ->orderByDesc('created_at')
            ->first();

        $queueStatus = [
            'pending' => DB::table('jobs')->count(),
            'failed'  => DB::table('failed_jobs')->count(),
        ];

        return view('dev.mail-inspector', compact('mailConfig', 'lastTest', 'queueStatus'));
    }

    public function sendTest(Request $request): RedirectResponse
    {
        $request->validate([
            'to'      => 'required|email',
            'subject' => 'required|string|max:255',
            'body'    => 'required|string',
        ]);

        try {
            $this->audit->log('mail.send_test', null, null, ['to' => $request->to, 'subject' => $request->subject]);
            $this->system->sendTestMail($request->to, $request->subject, $request->body);
            return back()->with('success', "Test email sent to {$request->to}.");
        } catch (\Throwable $e) {
            return back()->with('error', "Failed: {$e->getMessage()}");
        }
    }

    public function sendVerification(Request $request): RedirectResponse
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        $user = \App\Models\User::findOrFail($request->user_id);
        $this->audit->log('mail.send_verification', \App\Models\User::class, $user->id, ['email' => $user->email]);
        $user->sendEmailVerificationNotification();
        return back()->with('success', "Verification email queued for {$user->email}.");
    }

    public function testResend(): RedirectResponse
    {
        $this->audit->log('mail.test_resend_api');
        $result = $this->system->testResendApi();
        $type   = $result['ok'] ? 'success' : 'error';
        return back()->with($type, $result['message']);
    }

    public function checkApiKey(): RedirectResponse
    {
        $this->audit->log('mail.check_api_key');
        $key = config('services.resend.key');
        if (! $key) {
            return back()->with('error', 'RESEND_API_KEY is not set in the environment.');
        }
        return back()->with('success', 'RESEND_API_KEY is set (ends in ***' . substr($key, -4) . ').');
    }
}
