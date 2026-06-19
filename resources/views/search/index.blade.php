@extends('layouts.app')

@section('title', 'Search Availability')
@section('breadcrumb', 'Booking · Search')

@section('content')
    @include('search.partials.form')

    @if (is_null($results))
        {{-- Placeholder shown before the first search --}}
        <div class="card">
            <div class="card-body">
                @include('partials.empty', ['icon' => 'bi-calendar2-search', 'title' => 'Find available rooms', 'text' => 'Enter a city and your dates above to see live, date-aware availability.'])
            </div>
        </div>
    @else
        <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
            <span class="badge text-bg-light"><i class="bi bi-building me-1"></i>{{ count($results) }} hotel(s)</span>
            <span class="badge text-bg-light"><i class="bi bi-moon-stars me-1"></i>{{ $meta['nights'] }} night(s)</span>
            <span class="badge text-bg-light"><i class="bi bi-calendar-range me-1"></i>{{ $meta['checkin_date'] }} → {{ $meta['checkout_date'] }}</span>
            <span class="badge text-bg-light"><i class="bi bi-people me-1"></i>{{ $meta['guests'] }} guest(s)</span>
        </div>

        @forelse ($results as $item)
            @include('search.partials.result-card', ['item' => $item])
        @empty
            <div class="card">
                <div class="card-body">
                    @include('partials.empty', ['icon' => 'bi-emoji-frown', 'title' => 'No availability', 'text' => 'No rooms match the selected city, dates and guest count. Try widening your search.'])
                </div>
            </div>
        @endforelse
    @endif

    @include('partials.tom-select')
@endsection
