@php
$navItems = [
    ['route' => 'dev.users',       'icon' => 'bi-person',          'label' => 'Users'],
    ['route' => 'dev.members',     'icon' => 'bi-people',          'label' => 'Members'],
    ['route' => 'dev.merchants',   'icon' => 'bi-shop',            'label' => 'Merchant'],
    ['route' => 'dev.mail',        'icon' => 'bi-envelope',        'label' => 'Test Mail'],
    ['route' => 'dev.database',    'icon' => 'bi-database',        'label' => 'Database'],
    ['route' => 'dev.queue',       'icon' => 'bi-stack',           'label' => 'Queue'],
    ['route' => 'dev.storage',     'icon' => 'bi-hdd',             'label' => 'Storage'],
    ['route' => 'dev.helpers',     'icon' => 'bi-magic',           'label' => 'Helpers'],
    ['route' => 'dev.environment', 'icon' => 'bi-info-circle',     'label' => 'Environment'],
    ['route' => 'dev.health',      'icon' => 'bi-heart-pulse',     'label' => 'System Health'],
    ['route' => 'dev.danger',      'icon' => 'bi-exclamation-triangle', 'label' => 'Danger Zone'],
];
@endphp

<div class="dev-sidebar border-end bg-light p-3" style="min-width:200px;min-height:100vh;">
    <div class="fw-bold text-uppercase text-muted small mb-3 px-1">
        <i class="bi bi-tools me-1"></i>Developer Tools
    </div>
    <ul class="nav flex-column gap-1">
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
</div>
