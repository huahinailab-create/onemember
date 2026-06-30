<?php

namespace App\Contracts;

use App\Models\Merchant;

interface InsightProviderInterface
{
    /**
     * Analyse a merchant and return all intelligence outputs in one pass.
     *
     * @return array{
     *     insights: array<int, array{icon: string, priority: string, text: string, action_label: string|null, action_url: string|null}>,
     *     health_score: array{score: int, label: string, label_text: string, explanation: string, badge_class: string},
     *     opportunities: array<int, array{icon: string, title: string, description: string, action_label: string, action_url: string, priority: string}>
     * }
     */
    public function analyze(Merchant $merchant): array;
}
