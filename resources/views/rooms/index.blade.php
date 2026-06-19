@extends('layouts.app')

@section('title', 'Rooms')
@section('breadcrumb', 'Inventory · Rooms')

@section('content')
    @php($activeHotel = ($hotelFilter ?? null) ? $hotels->firstWhere('id', $hotelFilter) : null)
    @php($hasFilters = ($hotelFilter ?? null) || filled($search ?? null))
    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
        <form method="GET" action="{{ route('rooms.index') }}" class="d-flex gap-2" style="flex:1 1 360px; max-width:480px;">
            @if ($hotelFilter ?? null)
                <input type="hidden" name="hotel" value="{{ $hotelFilter }}">
            @endif
            <div class="hi-search flex-grow-1">
                <i class="bi bi-search"></i>
                <input name="search" value="{{ $search ?? '' }}" class="form-control" placeholder="Search rooms or hotels…">
            </div>
            <button class="btn btn-primary" type="submit"><i class="bi bi-search me-1"></i>Search</button>
        </form>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoomModal">
            <i class="bi bi-plus-lg me-1"></i> Add room
        </button>
    </div>

    @if ($hasFilters)
        <div class="mb-3 d-flex flex-wrap gap-2 align-items-center">
            @if ($hotelFilter ?? null)
                <span class="badge text-bg-primary"><i class="bi bi-funnel-fill me-1"></i>Rooms at {{ $activeHotel?->name ?? 'selected hotel' }}</span>
            @endif
            @if (filled($search ?? null))
                <span class="badge text-bg-secondary"><i class="bi bi-search me-1"></i>“{{ $search }}”</span>
            @endif
            <a href="{{ route('rooms.index') }}" class="btn btn-sm btn-link text-decoration-none px-1"><i class="bi bi-x-lg"></i> Clear all</a>
        </div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table hi-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Price</th>
                        <th>Occupancy</th>
                        <th>Inventory</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rooms as $room)
                        @include('rooms.partials.row', ['room' => $room])
                    @empty
                        <tr>
                            <td colspan="5">
                                @if ($hasFilters)
                                    @include('partials.empty', ['icon' => 'bi-search', 'title' => 'No matching rooms', 'text' => 'No rooms match your current search or filter.'])
                                @else
                                    @include('partials.empty', ['icon' => 'bi-door-closed', 'title' => 'No rooms yet', 'text' => 'Add a room to one of your hotels to get started.'])
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($rooms->hasPages())
            <div class="card-body border-top">{{ $rooms->withQueryString()->links() }}</div>
        @endif
    </div>

    @include('rooms.partials.create-modal')
    @include('rooms.partials.edit-modal')
    @include('rooms.partials.details-modal')
    @include('partials.confirm-delete-modal')

    @if ($errors->any())
        @push('scripts')
            <script>
                (function () {
                    // Re-open whichever modal was submitted (edit carries an __action URL).
                    const isEdit = @json((bool) old('__action'));
                    const id = isEdit ? 'editRoomModal' : 'createRoomModal';
                    new bootstrap.Modal(document.getElementById(id)).show();
                })();
            </script>
        @endpush
    @endif
@endsection
