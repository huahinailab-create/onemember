<?php

namespace App\Http\Controllers\DevTools;

use App\Services\DevTools\DevAuditLogger;
use App\Services\DevTools\DevSystemService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DevMailController extends DevController
{
    public function __construct(
        DevAuditLogger $audit,
        private readonly DevSystemService $system,
    ) {
        parent::__construct($audit);
    }

    public function index(): View
    {
        $mailConfig = [
            'mailer'       => config('mail.default'),
            'from_address' => config('mail.from.address'),
            'from_name'    => config('mail.from.name'),
            'resend_key'   => config('services.resend.key') ? '***' . substr(config('services.resend.key'), -4) : 'not set',
        ];
        return view('dev.mail', compact('mailConfig'));
    }

    public function send(Request $request): RedirectResponse
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

    public function testResend(): RedirectResponse
    {
        $this->audit->log('mail.test_resend_api');
        $result = $this->system->testResendApi();
        $type   = $result['ok'] ? 'success' : 'error';
        return back()->with($type, $result['message']);
    }
}
