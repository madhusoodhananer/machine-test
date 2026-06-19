@extends('layouts.app')

@section('title', 'Search')

@section('content')
    <h1 class="h3 mb-4">Search Availability</h1>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('search.index') }}" class="row g-3" novalidate>
                <div class="col-md-4">
                    <label class="form-label" for="city">City</label>
                    <input id="city" name="city" value="{{ $filters['city'] ?? '' }}"
                           class="form-control @error('city') is-invalid @enderror" placeholder="Dubai">
                    @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="checkin_date">Check-in</label>
                    <input id="checkin_date" name="checkin_date" type="date" value="{{ $filters['checkin_date'] ?? '' }}"
                           class="form-control @error('checkin_date') is-invalid @enderror">
                    @error('checkin_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="checkout_date">Check-out</label>
                    <input id="checkout_date" name="checkout_date" type="date" value="{{ $filters['checkout_date'] ?? '' }}"
                           class="form-control @error('checkout_date') is-invalid @enderror">
                    @error('checkout_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="guests">Guests</label>
                    <input id="guests" name="guests" type="number" min="1" value="{{ $filters['guests'] ?? 1 }}"
                           class="form-control @error('guests') is-invalid @enderror">
                    @error('guests')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <button class="btn btn-dark" type="submit">Search</button>
                </div>
            </form>
        </div>
    </div>

    @if (! is_null($results))
        <p class="text-muted">
            {{ count($results) }} hotel(s) with availability for
            <strong>{{ $meta['nights'] }}</strong> night(s)
            ({{ $meta['checkin_date'] }} → {{ $meta['checkout_date'] }}), {{ $meta['guests'] }} guest(s).
        </p>

        @forelse ($results as $item)
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h2 class="h5 mb-0">{{ $item['hotel']['name'] }}</h2>
                            <div class="text-muted small">
                                {{ $item['hotel']['city'] }}, {{ $item['hotel']['country'] }}
                            </div>
                        </div>
                        <span class="badge text-bg-warning">{{ str_repeat('★', $item['hotel']['rating']) }}</span>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Room</th>
                                    <th>Max guests</th>
                                    <th>Price/night</th>
                                    <th>Available units</th>
                                    <th class="text-end">Total ({{ $item['nights'] }} nights)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($item['rooms'] as $room)
                                    <tr>
                                        <td>{{ $room['name'] }}</td>
                                        <td>{{ $room['max_occupancy'] }}</td>
                                        <td>{{ number_format((float) $room['price_per_night'], 2) }}</td>
                                        <td><span class="badge text-bg-success">{{ $room['available_units'] }} left</span></td>
                                        <td class="text-end fw-bold">{{ number_format((float) $room['total_price'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-warning">No available rooms for the selected city and dates.</div>
        @endforelse
    @endif
@endsection
