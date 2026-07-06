<?php

namespace App\Services;

use App\Models\Merchant;

/**
 * LAUNCH-001 — merchant success checklist. Computes each item from real data
 * where possible; a few "did you try it" items are tracked as flags in
 * merchant settings (`launch_flags`) set when the merchant visits/opens them.
 */
class LaunchChecklistService
{
    /** Flags that are set by visiting a page (not derivable from data). */
    public const FLAGS = ['launch_kit_opened', 'counter_tried', 'storefront_reviewed'];

    /**
     * @return array{items: list<array{key:string,done:bool,label_key:string,url:string|null}>, done:int, total:int, percent:int}
     */
    public function for(Merchant $merchant): array
    {
        $flags = $merchant->settings['launch_flags'] ?? [];

        $items = [
            [
                'key'       => 'profile',
                'done'      => filled($merchant->name) && filled($merchant->business_type),
                'label_key' => 'launch_check.profile',
                'url'       => route('settings'),
            ],
            [
                'key'       => 'campaign',
                'done'      => $merchant->loyaltyPrograms()->exists(),
                'label_key' => 'launch_check.campaign',
                'url'       => route('campaigns.index'),
            ],
            [
                'key'       => 'reward',
                'done'      => $merchant->rewards()->exists(),
                'label_key' => 'launch_check.reward',
                'url'       => route('rewards'),
            ],
            [
                'key'       => 'member',
                'done'      => $merchant->members()->exists(),
                'label_key' => 'launch_check.member',
                'url'       => route('members.create'),
            ],
            [
                'key'       => 'launch_kit',
                'done'      => (bool) ($flags['launch_kit_opened'] ?? false),
                'label_key' => 'launch_check.launch_kit',
                'url'       => route('launch-kit'),
            ],
            [
                'key'       => 'counter',
                'done'      => (bool) ($flags['counter_tried'] ?? false),
                'label_key' => 'launch_check.counter',
                'url'       => route('counter'),
            ],
        ];

        // Storefront review only applies when the Commerce App is installed.
        if ($merchant->hasApp('commerce')) {
            $items[] = [
                'key'       => 'storefront',
                'done'      => (bool) ($flags['storefront_reviewed'] ?? false),
                'label_key' => 'launch_check.storefront',
                'url'       => route('commerce.products.index'),
            ];
        }

        $done  = count(array_filter($items, fn ($i) => $i['done']));
        $total = count($items);

        return [
            'items'   => $items,
            'done'    => $done,
            'total'   => $total,
            'percent' => $total > 0 ? (int) round($done / $total * 100) : 0,
        ];
    }

    /** Set a visit flag (idempotent). */
    public function markFlag(Merchant $merchant, string $flag): void
    {
        if (! in_array($flag, self::FLAGS, true)) {
            return;
        }

        $settings = $merchant->settings ?? [];
        $settings['launch_flags'] = array_merge($settings['launch_flags'] ?? [], [$flag => true]);
        $merchant->update(['settings' => $settings]);
    }
}
