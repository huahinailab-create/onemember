@php
$navGroups = [
    'DEV-02' => [
        ['route' => 'dev.dashboard',      'icon' => 'bi-grid-1x2',           'label' => 'Dashboard'],
        ['route' => 'dev.quick-actions',  'icon' => 'bi-lightning-charge',   'label' => 'Quick Actions'],
        ['route' => 'dev.mail-inspector', 'icon' => 'bi-envelope-check',     'label' => 'Mail Inspector'],
        ['route' => 'dev.queue-inspector','icon' => 'bi-stack-overflow',     'label' => 'Queue Inspector'],
        ['route' => 'dev.env-inspector',  'icon' => 'bi-sliders',            'label' => 'Env Inspector'],
        ['route' => 'dev.performance',    'icon' => 'bi-speedometer',        'label' => 'Performance'],
        ['route' => 'dev.logs',           'icon' => 'bi-journal-text',       'label' => 'Log Viewer'],
        ['route' => 'dev.demo-reset',     'icon' => 'bi-arrow-counterclockwise','label' => 'Demo Reset'],
        ['route' => 'dev.feature-flags',  'icon' => 'bi-toggles',            'label' => 'Feature Flags'],
    ],
    'DEV-01' => [
        ['route' => 'dev.users',          'icon' => 'bi-person',             'label' => 'Users'],
        ['route' => 'dev.members',        'icon' => 'bi-people',             'label' => 'Members'],
        ['route' => 'dev.merchants',      'icon' => 'bi-shop',               'label' => 'Merchant'],
        ['route' => 'dev.mail',           'icon' => 'bi-envelope',           'label' => 'Test Mail'],
        ['route' => 'dev.database',       'icon' => 'bi-database',           'label' => 'Database'],
        ['route' => 'dev.queue',          'icon' => 'bi-stack',              'label' => 'Queue'],
        ['route' => 'dev.storage',        'icon' => 'bi-hdd',                'label' => 'Storage'],
        ['route' => 'dev.helpers',        'icon' => 'bi-magic',              'label' => 'Helpers'],
        ['route' => 'dev.environment',    'icon' => 'bi-info-circle',        'label' => 'Environment'],
        ['route' => 'dev.health',         'icon' => 'bi-heart-pulse',        'label' => 'System Health'],
        ['route' => 'dev.danger',         'icon' => 'bi-exclamation-triangle','label' => 'Danger Zone'],
    ],
];
@endphp

<div class="dev-sidebar border-end bg-light p-3" style="min-width:210px;min-height:100vh;overflow-y:auto;">
    <div class="fw-bold text-uppercase text-muted small mb-3 px-1">
        <i class="bi bi-tools me-1"></i>Developer Tools
    </div>
    @foreach ($navGroups as $groupLabel => $navItems)
        <div class="text-uppercase text-muted px-1 mb-1" style="font-size:0.6rem;letter-spacing:0.05em;">{{ $groupLabel }}</div>
        <ul class="nav flex-column gap-1 mb-3">
            @foreach ($navItems as $item)
                <li class="nav-item">
                    <a href="{{ route($item['route']) }}"
                       class="nav-link d-flex align-items-center gap-2 px-2 py-1 rounded
                              {{ request()->routeIs($item['route']) ? 'bg-warning text-dark fw-semibold' : 'text-secondary' }}">
                        <i class="bi {{ $item['icon'] }}"></i>
                        <span>{{ $item['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    @endforeach
</div>
