{{-- New Booking modal. Posts to bookings.store. Param: $rooms (collection with hotel). --}}
<div class="modal fade" id="createBookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('bookings.store') }}" novalidate>
                @csrf
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold"><i class="bi bi-calendar-plus text-primary me-2"></i>New booking</h5>
                        <p class="text-muted small mb-0">Reserve a room for a date range. Total price is calculated automatically.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="booking_room_id">Room</label>
                        <select id="booking_room_id" name="room_id" data-tomselect class="form-select @error('room_id') is-invalid @enderror">
                            <option value="">Choose a room…</option>
                            @foreach ($rooms as $room)
                                <option value="{{ $room->id }}" @selected(old('room_id') === $room->id)>
                                    {{ $room->hotel?->name }} — {{ $room->name }}
                                    (up to {{ $room->max_occupancy }} guests · {{ number_format((float) $room->price_per_night, 2) }}/night)
                                </option>
                            @endforeach
                        </select>
                        @error('room_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="booking_checkin">Check-in</label>
                            <input id="booking_checkin" name="checkin_date" type="date" value="{{ old('checkin_date') }}"
                                   min="{{ now()->toDateString() }}"
                                   class="form-control @error('checkin_date') is-invalid @enderror">
                            @error('checkin_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="booking_checkout">Check-out</label>
                            <input id="booking_checkout" name="checkout_date" type="date" value="{{ old('checkout_date') }}"
                                   min="{{ now()->addDay()->toDateString() }}"
                                   class="form-control @error('checkout_date') is-invalid @enderror">
                            @error('checkout_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label" for="booking_guests">Guests</label>
                        <input id="booking_guests" name="guests" type="number" min="1" value="{{ old('guests', 1) }}"
                               class="form-control @error('guests') is-invalid @enderror">
                        @error('guests')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit"><i class="bi bi-check-lg me-1"></i>Confirm booking</button>
                </div>
            </form>
        </div>
    </div>
</div>
