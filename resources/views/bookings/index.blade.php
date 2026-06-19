@extends('layouts.app')

@section('title', 'Bookings')
@section('breadcrumb', 'Operations · Bookings')

@section('content')
    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
        <p class="text-muted mb-0"><i class="bi bi-info-circle me-1"></i>Confirmed bookings across all properties.</p>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBookingModal">
            <i class="bi bi-plus-lg me-1"></i> New booking
        </button>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table hi-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Stay</th>
                        <th>Guests</th>
                        <th>Status</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $booking)
                        @include('bookings.partials.row', ['booking' => $booking])
                    @empty
                        <tr>
                            <td colspan="6">
                                @include('partials.empty', ['icon' => 'bi-calendar-x', 'title' => 'No bookings yet', 'text' => 'Create your first booking to see it here.'])
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($bookings->hasPages())
            <div class="card-body border-top">{{ $bookings->links() }}</div>
        @endif
    </div>

    @include('bookings.partials.create-modal')
    @include('bookings.partials.delete-modal')
    @include('partials.tom-select')

    @if ($errors->any())
        @push('scripts')
            <script>new bootstrap.Modal(document.getElementById('createBookingModal')).show();</script>
        @endpush
    @endif
@endsection
