@extends('layouts.app')

@section('title', 'Rooms')
@section('breadcrumb', 'Inventory · Rooms')

@section('content')
    @php($activeHotel = ($hotelFilter ?? null) ? $hotels->firstWhere('id', $hotelFilter) : null)
    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
        <div>
            @if ($hotelFilter ?? null)
                <span class="badge text-bg-primary"><i class="bi bi-funnel-fill me-1"></i>Rooms at {{ $activeHotel?->name ?? 'selected hotel' }}</span>
                <a href="{{ route('rooms.index') }}" class="btn btn-sm btn-link text-decoration-none px-1"><i class="bi bi-x-lg"></i> Clear</a>
            @else
                <p class="text-muted mb-0"><i class="bi bi-info-circle me-1"></i>Room types and their physical inventory per hotel.</p>
            @endif
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoomModal">
            <i class="bi bi-plus-lg me-1"></i> Add room
        </button>
    </div>

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
                                @include('partials.empty', ['icon' => 'bi-door-closed', 'title' => 'No rooms yet', 'text' => 'Add a room to one of your hotels to get started.'])
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

    @if ($errors->any())
        @push('scripts')
            <script>new bootstrap.Modal(document.getElementById('createRoomModal')).show();</script>
        @endpush
    @endif
@endsection
