@extends('layouts.app')

@section('title', 'Rooms')

@section('content')
    <h1 class="h3 mb-4">Rooms</h1>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5 mb-3">Add Room</h2>
                    <form method="POST" action="{{ route('rooms.store') }}" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="hotel_id">Hotel</label>
                            <select id="hotel_id" name="hotel_id" class="form-select @error('hotel_id') is-invalid @enderror">
                                <option value="">Choose a hotel…</option>
                                @foreach ($hotels as $hotel)
                                    <option value="{{ $hotel->id }}" @selected((int) old('hotel_id') === $hotel->id)>
                                        {{ $hotel->name }} ({{ $hotel->city }})
                                    </option>
                                @endforeach
                            </select>
                            @error('hotel_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="room-name">Room name</label>
                            <input id="room-name" name="name" value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror" placeholder="Deluxe King">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="price_per_night">Price per night</label>
                            <input id="price_per_night" name="price_per_night" type="number" step="0.01" min="0"
                                   value="{{ old('price_per_night') }}"
                                   class="form-control @error('price_per_night') is-invalid @enderror">
                            @error('price_per_night')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="max_occupancy">Max occupancy</label>
                            <input id="max_occupancy" name="max_occupancy" type="number" min="1"
                                   value="{{ old('max_occupancy') }}"
                                   class="form-control @error('max_occupancy') is-invalid @enderror">
                            @error('max_occupancy')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="total_rooms">Total rooms (inventory)</label>
                            <input id="total_rooms" name="total_rooms" type="number" min="1"
                                   value="{{ old('total_rooms') }}"
                                   class="form-control @error('total_rooms') is-invalid @enderror">
                            @error('total_rooms')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button class="btn btn-dark w-100" type="submit">Create Room</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Hotel</th>
                                    <th>Room</th>
                                    <th>Price/night</th>
                                    <th>Occupancy</th>
                                    <th>Inventory</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rooms as $room)
                                    <tr>
                                        <td>{{ $room->id }}</td>
                                        <td>{{ $room->hotel?->name }}</td>
                                        <td>{{ $room->name }}</td>
                                        <td>{{ number_format((float) $room->price_per_night, 2) }}</td>
                                        <td>{{ $room->max_occupancy }}</td>
                                        <td>{{ $room->total_rooms }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted py-4">No rooms yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $rooms->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
