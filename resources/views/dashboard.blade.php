@extends('layouts.app')

@section('title', 'Dashboard')
@section('breadcrumb', 'Overview')

@section('content')
    {{-- Welcome hero --}}
    <div class="card border-0 mb-4" style="background:linear-gradient(120deg,#4f46e5,#7c3aed);color:#fff;">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center p-4">
            <div>
                <h2 class="h4 fw-bold mb-1">Welcome back, {{ auth()->user()?->name }} 👋</h2>
                <p class="mb-0 opacity-75">Here's what's happening across your properties today.</p>
            </div>
            <a href="{{ route('search.index') }}" class="btn btn-light fw-semibold mt-3 mt-md-0">
                <i class="bi bi-search me-1"></i> Search availability
            </a>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="row g-3">
        @include('dashboard.partials.stat-card', ['label' => 'Total Hotels', 'value' => $totalHotels, 'icon' => 'bi-buildings-fill', 'grad' => 'hi-grad-indigo', 'trend' => '+2 this month', 'up' => true])
        @include('dashboard.partials.stat-card', ['label' => 'Total Rooms', 'value' => $totalRooms, 'icon' => 'bi-door-open-fill', 'grad' => 'hi-grad-emerald', 'trend' => '+8 this month', 'up' => true])
        @include('dashboard.partials.stat-card', ['label' => 'Total Bookings', 'value' => $totalBookings, 'icon' => 'bi-calendar-check-fill', 'grad' => 'hi-grad-sky', 'trend' => '+12% vs last week', 'up' => true])
        @include('dashboard.partials.stat-card', ['label' => 'Avg. Rating', 'value' => number_format($averageRating, 1), 'icon' => 'bi-star-fill', 'grad' => 'hi-grad-amber', 'trend' => 'Steady', 'up' => true, 'suffix' => '★'])
    </div>

    <div class="row g-3 mt-1">
        {{-- Occupancy (demo placeholder) --}}
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="h6 fw-bold mb-0">Occupancy by region</h3>
                        <span class="badge text-bg-light"><i class="bi bi-bar-chart me-1"></i>Demo data</span>
                    </div>
                    @foreach ([['Dubai', 82, 'hi-grad-indigo'], ['Paris', 64, 'hi-grad-emerald'], ['London', 47, 'hi-grad-sky'], ['Tokyo', 35, 'hi-grad-amber']] as [$region, $pct, $grad])
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="fw-medium"><i class="bi bi-geo-alt text-muted me-1"></i>{{ $region }}</span>
                                <span class="text-muted">{{ $pct }}%</span>
                            </div>
                            <div class="progress" style="height:8px;border-radius:8px;">
                                <div class="progress-bar {{ $grad }}" role="progressbar" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Quick actions + recent activity --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="h6 fw-bold mb-3">Quick actions</h3>
                    <div class="d-grid gap-2">
                        <a href="{{ route('hotels.index') }}" class="btn btn-primary text-start"><i class="bi bi-plus-circle me-2"></i>Add hotel</a>
                        <a href="{{ route('rooms.index') }}" class="btn btn-outline-secondary text-start"><i class="bi bi-plus-square me-2"></i>Add room</a>
                        <a href="{{ route('search.index') }}" class="btn btn-outline-secondary text-start"><i class="bi bi-search me-2"></i>Search availability</a>
                    </div>

                    <hr class="my-4">

                    <h3 class="h6 fw-bold mb-3">Recent activity</h3>
                    @foreach ([['bi-calendar-check', 'New booking', 'Burj Marina · 2m ago', 'text-bg-info'], ['bi-building-add', 'Hotel added', 'Palm Stay · 1h ago', 'text-bg-success'], ['bi-star-fill', 'Review received', 'Eiffel Luxe · 3h ago', 'text-bg-warning']] as [$icon, $title, $sub, $badge])
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <span class="badge {{ $badge }} p-2"><i class="bi {{ $icon }}"></i></span>
                            <div class="small">
                                <div class="fw-semibold">{{ $title }}</div>
                                <div class="text-muted">{{ $sub }}</div>
                            </div>
                        </div>
                    @endforeach
                    <div class="text-center">
                        <a href="#" class="small text-decoration-none" data-bs-toggle="modal" data-bs-target="#comingSoonModal">View all activity →</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
