<?php

namespace App\Services;

use App\Contracts\InsightProviderInterface;
use App\Models\Merchant;
use Illuminate\Support\Facades\Cache;

class MerchantIntelligenceService
{
    public function __construct(private InsightProviderInterface $provider) {}

    /** @return array<int, array{icon: string, priority: string, text: string, action_label: string|null, action_url: string|null}> */
    public function getInsights(Merchant $merchant): array
    {
        return $this->analyze($merchant)['insights'];
    }

    /** @return array{score: int, label: string, label_text: string, explanation: string, badge_class: string} */
    public function getHealthScore(Merchant $merchant): array
    {
        return $this->analyze($merchant)['health_score'];
    }

    /** @return array<int, array{icon: string, title: string, description: string, action_label: string, action_url: string}> */
    public function getOpportunities(Merchant $merchant): array
    {
        return $this->analyze($merchant)['opportunities'];
    }

    public function getWeeklySummary(Merchant $merchant): array
    {
        $newMembers  = $merchant->members()->where('joined_at', '>=', now()->subWeek())->count();
        $purchases   = $merchant->transactions()->where('created_at', '>=', now()->subWeek())->count();
        $redemptions = $merchant->redemptions()->where('created_at', '>=', now()->subWeek())->count();
        $health      = $this->getHealthScore($merchant);

        return [
            'period_start'     => now()->subWeek()->startOfDay(),
            'period_end'       => now()->endOfDay(),
            'new_members'      => $newMembers,
            'purchases'        => $purchases,
            'rewards_redeemed' => $redemptions,
            'health_score'     => $health['score'],
            'health_label'     => $health['label_text'],
        ];
    }

    public function clearCache(Merchant $merchant): void
    {
        Cache::forget("merchant_intelligence_{$merchant->id}");
    }

    private function analyze(Merchant $merchant): array
    {
        return Cache::remember(
            "merchant_intelligence_{$merchant->id}",
            900,
            fn () => $this->provider->analyze($merchant)
        );
    }
}
