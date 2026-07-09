<?php

namespace App\Apps\Queue;

use App\Apps\Queue\Models\QueueTicket;
use App\Events\Domain\QueueTicketCreated;
use App\Marketplace\AppManager;
use App\Models\Merchant;
use Illuminate\Support\Facades\Log;

/**
 * PLATFORM-002 Part 8 — Queue App domain service.
 * Architecture + basic operations; SMS/LINE notifications are placeholders
 * until providers are approved (feature-flagged in the manifest).
 */
class QueueService
{
    public function __construct(private readonly AppManager $apps)
    {
    }

    public function issueTicket(Merchant $merchant, array $attributes = []): QueueTicket
    {
        $ticket = QueueTicket::create(array_merge([
            'merchant_id' => $merchant->id,
            'number'      => $this->nextNumber($merchant),
            'type'        => 'walk_in',
            'status'      => 'waiting',
        ], $attributes));

        event(new QueueTicketCreated($ticket));

        return $ticket;
    }

    /** Daily sequence per merchant — resets each day, gaps allowed. */
    public function nextNumber(Merchant $merchant): int
    {
        $last = QueueTicket::where('merchant_id', $merchant->id)
            ->whereDate('created_at', today())
            ->max('number');

        return ($last ?? 0) + 1;
    }

    /** Simple estimate: people ahead × configured average service minutes. */
    public function estimatedWaitMinutes(Merchant $merchant, QueueTicket $ticket): int
    {
        $ahead = QueueTicket::where('merchant_id', $merchant->id)
            ->waitingLine()
            ->where('id', '!=', $ticket->id)
            ->where(function ($q) use ($ticket) {
                $q->where('priority', '>', (int) $ticket->priority)
                  ->orWhere(fn ($qq) => $qq->where('priority', (int) $ticket->priority)->where('number', '<', $ticket->number));
            })
            ->count();

        $avg = (int) ($this->apps->configFor($merchant, 'queue')['avg_service_minutes'] ?? 5);

        return $ahead * max(1, $avg);
    }

    /** Today's board analytics (counts + average wait of served tickets). */
    public function todayStats(Merchant $merchant): array
    {
        $today = QueueTicket::where('merchant_id', $merchant->id)->whereDate('created_at', today());

        $avgWaitSeconds = (clone $today)->whereNotNull('called_at')
            ->get(['created_at', 'called_at'])
            ->map(fn ($t) => $t->called_at->diffInSeconds($t->created_at))
            ->avg();

        return [
            'issued'   => (clone $today)->count(),
            'waiting'  => (clone $today)->where('status', 'waiting')->count(),
            'serving'  => (clone $today)->whereIn('status', ['called', 'serving'])->count(),
            'done'     => (clone $today)->where('status', 'done')->count(),
            'no_show'  => (clone $today)->where('status', 'no_show')->count(),
            'avg_wait_minutes' => $avgWaitSeconds ? (int) round(abs($avgWaitSeconds) / 60) : null,
        ];
    }

    /** SMS notification placeholder — provider selection is a DR-gated future step. */
    public function notifyBySms(QueueTicket $ticket): void
    {
        Log::info('queue.notify_sms_placeholder', ['ticket_id' => $ticket->id]);
    }

    /** LINE notification placeholder — provider selection is a DR-gated future step. */
    public function notifyByLine(QueueTicket $ticket): void
    {
        Log::info('queue.notify_line_placeholder', ['ticket_id' => $ticket->id]);
    }
}
