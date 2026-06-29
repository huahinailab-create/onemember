<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedbackRequest;
use App\Services\AnalyticsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FeedbackController extends Controller
{
    public function store(FeedbackRequest $request, AnalyticsService $analytics): RedirectResponse
    {
        $user     = $request->user();
        $merchant = $user->merchant;

        $payload = [
            'id'          => (string) \Illuminate\Support\Str::uuid(),
            'submitted_at' => now()->toISOString(),
            'category'    => $request->validated('category'),
            'subject'     => $request->validated('subject'),
            'message'     => $request->validated('message'),
            'current_url' => $request->validated('current_url') ?? '',
            'browser'     => $request->validated('browser') ?? '',
            'user_id'     => $user->id,
            'merchant_id' => $merchant?->id,
            'app_version' => config('app.version', '1.0'),
            'locale'      => app()->getLocale(),
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent() ?? '',
            'environment' => config('app.env', 'production'),
        ];

        // Write to storage/app/feedback/<timestamp>_<uuid>.json
        $filename = now()->format('Y-m-d_His') . '_' . $payload['id'] . '.json';
        Storage::disk('local')->put('feedback/' . $filename, json_encode($payload, JSON_PRETTY_PRINT));

        // Track as an analytics event
        $analytics->track('feedback_submitted', [
            'category' => $payload['category'],
        ], $user->id, $merchant?->id);

        return back()->with('success', __('feedback.submitted'));
    }
}
