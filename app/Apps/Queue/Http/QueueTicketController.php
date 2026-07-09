<?php

namespace App\Apps\Queue\Http;

use App\Apps\Queue\Models\QueueCounter;
use App\Apps\Queue\Models\QueueTicket;
use App\Apps\Queue\QueueService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** PLATFORM-002 Part 8 — Queue board + ticket CRUD (architecture, basic ops). */
class QueueTicketController extends Controller
{
    public function __construct(private readonly QueueService $queue)
    {
    }

    public function index(Request $request)
    {
        $merchant = $request->user()->merchant;

        return view('apps.queue.index', [
            'merchant' => $merchant,
            'waiting'  => QueueTicket::where('merchant_id', $merchant->id)->waitingLine()->with('counter')->get(),
            'active'   => QueueTicket::where('merchant_id', $merchant->id)
                ->whereIn('status', ['called', 'serving'])->orderBy('called_at')->with('counter')->get(),
            'counters' => QueueCounter::where('merchant_id', $merchant->id)->where('active', true)->get(),
            'stats'    => $this->queue->todayStats($merchant),
        ]);
    }

    public function store(Request $request)
    {
        $merchant  = $request->user()->merchant;
        $validated = $request->validate([
            'customer_name'  => ['nullable', 'string', 'max:150'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'type'           => ['required', Rule::in(['walk_in', 'reservation'])],
            'priority'       => ['nullable', 'boolean'],
            'reserved_for'   => ['nullable', 'date', 'required_if:type,reservation'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ]);

        $ticket = $this->queue->issueTicket($merchant, array_merge($validated, [
            'priority' => (bool) ($validated['priority'] ?? false),
        ]));

        return redirect()->route('queue.tickets.index')
            ->with('success', __('queue.ticket_issued', ['number' => $ticket->number]));
    }

    public function updateStatus(Request $request, QueueTicket $ticket)
    {
        abort_unless($ticket->merchant_id === $request->user()->merchant?->id, 403);

        $validated = $request->validate([
            'status'           => ['required', Rule::in(['called', 'serving', 'done', 'no_show', 'cancelled', 'waiting'])],
            'queue_counter_id' => ['nullable', 'integer'],
        ]);

        if (! $ticket->canTransitionTo($validated['status'])) {
            return back()->withErrors(['status' => __('queue.invalid_transition')]);
        }

        if (! empty($validated['queue_counter_id'])) {
            $counter = QueueCounter::where('merchant_id', $ticket->merchant_id)
                ->find($validated['queue_counter_id']);
            $ticket->queue_counter_id = $counter?->id;
        }

        $ticket->status = $validated['status'];
        if ($validated['status'] === 'called') {
            $ticket->called_at = now();
        }
        if ($validated['status'] === 'serving') {
            $ticket->served_at = now();
        }
        $ticket->save();

        return redirect()->route('queue.tickets.index')
            ->with('success', __('queue.status_updated', ['number' => $ticket->number]));
    }

    /** Read-only display board (for a counter-top tablet/screen). */
    public function display(Request $request)
    {
        $merchant = $request->user()->merchant;

        return view('apps.queue.display', [
            'merchant' => $merchant,
            'called'   => QueueTicket::where('merchant_id', $merchant->id)
                ->whereIn('status', ['called', 'serving'])->orderByDesc('called_at')->with('counter')->take(5)->get(),
            'waiting'  => QueueTicket::where('merchant_id', $merchant->id)->waitingLine()->take(10)->get(),
        ]);
    }

    public function storeCounter(Request $request)
    {
        $merchant  = $request->user()->merchant;
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'staff_name' => ['nullable', 'string', 'max:100'],
        ]);

        QueueCounter::create(array_merge($validated, ['merchant_id' => $merchant->id]));

        return redirect()->route('queue.tickets.index')
            ->with('success', __('queue.counter_created'));
    }
}
