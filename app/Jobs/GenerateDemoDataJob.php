<?php

namespace App\Jobs;

use App\Models\Merchant;
use App\Services\DevTools\DevDemoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateDemoDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;
    public int $tries   = 1;

    public function __construct(
        private readonly int    $merchantId,
        private readonly string $type,
        private readonly int    $count = 10,
    ) {}

    public function handle(DevDemoService $service): void
    {
        $merchant = Merchant::findOrFail($this->merchantId);

        match ($this->type) {
            'members'      => $service->generateMembers($merchant, $this->count),
            'purchases'    => $service->generatePurchases($merchant, $this->count),
            'points'       => $service->generateLoyaltyPoints($merchant, $this->count),
            'stamps'       => $service->generateStampTransactions($merchant, $this->count),
            'redemptions'  => $service->generateRedemptions($merchant, $this->count),
            'birthday'     => $service->generateBirthdayMembers($merchant, $this->count),
            'notifications'=> $service->generateNotifications($merchant, $this->count),
            default        => throw new \InvalidArgumentException("Unknown demo type: {$this->type}"),
        };
    }
}
