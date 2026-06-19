@php
    $mainNav = [
        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'bi-grid-1x2-fill', 'pattern' => 'dashboard'],
        ['route' => 'hotels.index', 'label' => 'Hotels', 'icon' => 'bi-buildings-fill', 'pattern' => 'hotels.*'],
        ['route' => 'rooms.index', 'label' => 'Rooms', 'icon' => 'bi-door-open-fill', 'pattern' => 'rooms.*'],
        ['route' => 'search.index', 'label' => 'Search', 'icon' => 'bi-search', 'pattern' => 'search.*'],
    ];

    $user = auth()->user();
    $initials = collect(explode(' ', trim($user?->name ?? 'A')))
        ->filter()
        ->map(fn ($part) => mb_substr($part, 0, 1))
        ->take(2)
        ->implode('');
@endphp

<aside class="hi-sidebar" id="hiSidebar">
    <a href="{{ route('dashboard') }}" class="hi-brand">
        <span class="hi-logo"><i class="bi bi-buildings-fill"></i></span>
        <span>Hotel<span class="text-info">Hub</span></span>
    </a>

    <div class="hi-nav-label">Main</div>
    <nav class="hi-nav">
        @foreach ($mainNav as $item)
            <a href="{{ route($item['route']) }}" class="{{ request()->routeIs($item['pattern']) ? 'active' : '' }}">
                <i class="bi {{ $item['icon'] }}"></i> {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    <div class="hi-nav-label">Workspace</div>
    <nav class="hi-nav">
        <a href="{{ route('bookings.index') }}" class="{{ request()->routeIs('bookings.*') ? 'active' : '' }}"><i class="bi bi-calendar-check"></i> Bookings</a>
    </nav>

    <div class="hi-sidebar-foot">
        <div class="hi-user-chip">
            <span class="hi-avatar">{{ strtoupper($initials) }}</span>
            <div class="small text-truncate">
                <div class="text-white fw-semibold text-truncate">{{ $user?->name }}</div>
                <div class="text-truncate" style="color:#64748b">{{ $user?->email }}</div>
            </div>
        </div>
    </div>
</aside>

@include('partials.coming-soon')
