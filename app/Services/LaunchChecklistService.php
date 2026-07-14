<?php

namespace App\Services;

use App\Models\Merchant;

/**
 * LAUNCH-001, evolved by MERCHANT-READY-001 / MR-001 — the merchant launch
 * checklist. Computes each item from real tenant data where possible; the
 * two "did you try it" items (QR poster, storefront visit) are flags in
 * merchant settings (`launch_flags`) set when the merchant opens those pages.
 *
 * Everything here is deterministic — no AI, no randomness. The same merchant
 * state always yields the same checklist, next action, and health statuses.
 */
class LaunchChecklistService
{
    /**
     * Flags that are set by visiting a page (not derivable from data).
     * The first two feed MR-001 checklist items; the rest are legacy
     * LAUNCH-001 flags still recorded for future use.
     */
    public const FLAGS = [
        'qr_poster_viewed',
        'storefront_visited',
        'launch_kit_opened',
        'counter_tried',
        'storefront_reviewed',
    ];

    /**
     * Checklist items in fixed priority order — this order also defines the
     * Next Recommended Action (first incomplete item wins).
     *
     * @return array{items: list<array{key:string,done:bool,label_key:string,action_key:string,url:string|null}>, done:int, total:int, percent:int, launch_ready:bool}
     */
    public function for(Merchant $merchant): array
    {
        $flags       = $merchant->settings['launch_flags'] ?? [];
        $hasCommerce = $merchant->hasApp('commerce');

        $items = [
            [
                'key'  => 'profile',
                'done' => filled($merchant->name) && filled($merchant->business_type),
                'url'  => route('settings'),
            ],
            [
                'key'  => 'logo',
                'done' => filled($merchant->logo_path),
                'url'  => route('settings'),
            ],
            [
                'key'  => 'store_url',
                'done' => filled($merchant->slug),
                'url'  => route('settings'),
            ],
        ];

        if ($hasCommerce) {
            $items[] = [
                'key'  => 'product',
                'done' => $merchant->products()->exists(),
                'url'  => route('commerce.products.index'),
            ];
        }

        $items[] = [
            'key'  => 'campaign',
            'done' => $merchant->loyaltyPrograms()->exists(),
            'url'  => route('campaigns.index'),
        ];
        $items[] = [
            'key'  => 'reward',
            'done' => $merchant->rewards()->exists(),
            'url'  => route('rewards'),
        ];
        $items[] = [
            'key'  => 'member',
            'done' => $merchant->members()->exists(),
            'url'  => route('members.create'),
        ];
        $items[] = [
            'key'  => 'qr_poster',
            'done' => (bool) ($flags['qr_poster_viewed'] ?? false),
            'url'  => route('launch-kit.poster'),
        ];

        if ($hasCommerce) {
            $items[] = [
                'key'  => 'storefront',
                'done' => (bool) ($flags['storefront_visited'] ?? false),
                'url'  => route('storefront.show', $merchant->slug),
            ];
        }

        foreach ($items as &$item) {
            $item['label_key']  = "launch_check.{$item['key']}";
            $item['action_key'] = "launch_check.action_{$item['key']}";
        }
        unset($item);

        $done  = count(array_filter($items, fn ($i) => $i['done']));
        $total = count($items);

        return [
            'items'        => $items,
            'done'         => $done,
            'total'        => $total,
            'percent'      => $total > 0 ? (int) round($done / $total * 100) : 0,
            'launch_ready' => $done === $total,
        ];
    }

    /**
     * MR-001 — the ONE next recommended action: the first incomplete item in
     * checklist order. Null when the merchant is Launch Ready.
     *
     * @return array{key:string,label_key:string,action_key:string,url:string|null}|null
     */
    public function nextAction(Merchant $merchant, ?array $checklist = null): ?array
    {
        $checklist ??= $this->for($merchant);

        foreach ($checklist['items'] as $item) {
            if (! $item['done']) {
                return $item;
            }
        }

        return null;
    }

    /**
     * MR-001 — Merchant Health Card rows. Fixed green/amber/red rules:
     *   green = complete / has data · amber = recommended but missing, or
     *   partial · red = core dimension still empty.
     *
     * @return array{rows: list<array{key:string,status:string,label_key:string,value:string|null,url:string|null}>, percent:int, launch_ready:bool}
     */
    public function health(Merchant $merchant, ?array $checklist = null): array
    {
        $checklist ??= $this->for($merchant);
        $items = collect($checklist['items'])->keyBy('key');
        $hasCommerce = $merchant->hasApp('commerce');

        $rows = [];

        // Business Profile: green complete · amber name only · red no name.
        $rows[] = [
            'key'    => 'profile',
            'status' => filled($merchant->name)
                ? (filled($merchant->business_type) ? 'green' : 'amber')
                : 'red',
            'value'  => null,
            'url'    => route('settings'),
        ];

        // Logo: recommended, never blocking.
        $rows[] = [
            'key'    => 'logo',
            'status' => filled($merchant->logo_path) ? 'green' : 'amber',
            'value'  => null,
            'url'    => route('settings'),
        ];

        // Store URL: auto-generated at registration; red only if missing.
        $rows[] = [
            'key'    => 'store_url',
            'status' => filled($merchant->slug) ? 'green' : 'red',
            'value'  => $merchant->slug,
            'url'    => route('settings'),
        ];

        // Products: green = has an active product · amber = products exist
        // but none active · red = none. Commerce merchants only.
        if ($hasCommerce) {
            $productCount = $merchant->products()->count();
            $activeCount  = $merchant->products()->where('status', 'active')->count();
            $rows[] = [
                'key'    => 'products',
                'status' => $activeCount > 0 ? 'green' : ($productCount > 0 ? 'amber' : 'red'),
                'value'  => (string) $productCount,
                'url'    => route('commerce.products.index'),
            ];
        }

        // Campaigns: green = active campaign · amber = exists but none
        // active · red = none.
        $campaignCount = $merchant->loyaltyPrograms()->count();
        $activeCampaigns = $merchant->loyaltyPrograms()->where('status', 'active')->count();
        $rows[] = [
            'key'    => 'campaigns',
            'status' => $activeCampaigns > 0 ? 'green' : ($campaignCount > 0 ? 'amber' : 'red'),
            'value'  => (string) $campaignCount,
            'url'    => route('campaigns.index'),
        ];

        // Members: core — red until the first member exists.
        $memberCount = $merchant->members()->count();
        $rows[] = [
            'key'    => 'members',
            'status' => $memberCount > 0 ? 'green' : 'red',
            'value'  => (string) $memberCount,
            'url'    => route('members'),
        ];

        // Storefront: visited = green, otherwise a nudge. Commerce only.
        if ($hasCommerce) {
            $rows[] = [
                'key'    => 'storefront',
                'status' => ($items['storefront']['done'] ?? false) ? 'green' : 'amber',
                'value'  => null,
                'url'    => route('storefront.show', $merchant->slug),
            ];
        }

        foreach ($rows as &$row) {
            $row['label_key'] = "launch_check.health_{$row['key']}";
        }
        unset($row);

        return [
            'rows'         => $rows,
            'percent'      => $checklist['percent'],
            'launch_ready' => $checklist['launch_ready'],
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
