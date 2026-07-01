<?php

namespace App\Services\DevTools;

use App\Models\DeveloperAction;
use Illuminate\Http\Request;

class DevAuditLogger
{
    public function __construct(private readonly Request $request) {}

    public function log(
        string $action,
        ?string $targetType = null,
        int|string|null $targetId = null,
        array $details = []
    ): void {
        DeveloperAction::create([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'target_type' => $targetType,
            'target_id'   => $targetId,
            'details'     => $details ?: null,
            'ip_address'  => $this->request->ip(),
            'user_agent'  => $this->request->userAgent(),
            'created_at'  => now(),
        ]);
    }
}
