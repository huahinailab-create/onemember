<?php

namespace App\Events\Domain;


/**
 * PLATFORM-002 Part 3 — A queue ticket was issued (Queue App, Part 8).
 */
class QueueTicketCreated extends DomainEvent
{
    public function __construct(public readonly \App\Apps\Queue\Models\QueueTicket $ticket)
    {
    }

    public function name(): string
    {
        return 'queue.ticket_created';
    }

    public function payload(): array
    {
        return ["ticket_id" => $this->ticket->id, "number" => $this->ticket->number, "priority" => $this->ticket->priority, "type" => $this->ticket->type];
    }

    public function merchantId(): ?int
    {
        return $this->ticket->merchant_id;
    }
}
