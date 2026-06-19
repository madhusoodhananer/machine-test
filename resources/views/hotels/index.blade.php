@extends('layouts.app')

@section('title', 'Hotels')
@section('breadcrumb', 'Inventory · Hotels')

@section('content')
    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
        <form method="GET" action="{{ route('hotels.index') }}" class="hi-search" style="max-width:320px;">
            <i class="bi bi-search"></i>
            <input name="city" value="{{ $city }}" class="form-control" placeholder="Filter by city…">
        </form>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createHotelModal">
            <i class="bi bi-plus-lg me-1"></i> Add hotel
        </button>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table hi-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Hotel</th>
                        <th>Location</th>
                        <th>Rating</th>
                        <th>Rooms</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($hotels as $hotel)
                        @include('hotels.partials.row', ['hotel' => $hotel])
                    @empty
                        <tr>
                            <td colspan="5">
                                @include('partials.empty', ['icon' => 'bi-buildings', 'title' => 'No hotels found', 'text' => 'Try a different city, or add your first hotel.'])
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($hotels->hasPages())
            <div class="card-body border-top">{{ $hotels->withQueryString()->links() }}</div>
        @endif
    </div>

    @include('hotels.partials.create-modal')
    @include('hotels.partials.edit-modal')
    @include('hotels.partials.details-modal')

    @if ($errors->any())
        @push('scripts')
            <script>
                (function () {
                    // Re-open whichever modal was submitted (edit carries an __action URL).
                    const isEdit = @json((bool) old('__action'));
                    const id = isEdit ? 'editHotelModal' : 'createHotelModal';
                    new bootstrap.Modal(document.getElementById(id)).show();
                })();
            </script>
        @endpush
    @endif
@endsection
