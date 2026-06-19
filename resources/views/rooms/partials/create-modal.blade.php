{{-- Add Room modal. Posts to rooms.store. Param: $hotels (collection for the select). --}}
<div class="modal fade" id="createRoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('rooms.store') }}" novalidate>
                @csrf
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold"><i class="bi bi-plus-square text-primary me-2"></i>Add room</h5>
                        <p class="text-muted small mb-0">Add a room type and its inventory to a hotel.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="hotel_id">Hotel</label>
                        <select id="hotel_id" name="hotel_id" data-tomselect class="form-select @error('hotel_id') is-invalid @enderror">
                            <option value="">Choose a hotel…</option>
                            @foreach ($hotels as $hotel)
                                <option value="{{ $hotel->id }}" @selected(old('hotel_id') === $hotel->id)>
                                    {{ $hotel->name }} ({{ $hotel->city }})
                                </option>
                            @endforeach
                        </select>
                        @error('hotel_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="room-name">Room name</label>
                        <input id="room-name" name="name" value="{{ old('name') }}" placeholder="Deluxe King"
                               class="form-control @error('name') is-invalid @enderror">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="price_per_night">Price / night</label>
                            <input id="price_per_night" name="price_per_night" type="number" step="0.01" min="0"
                                   value="{{ old('price_per_night') }}" placeholder="220.00"
                                   class="form-control @error('price_per_night') is-invalid @enderror">
                            @error('price_per_night')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="max_occupancy">Max guests</label>
                            <input id="max_occupancy" name="max_occupancy" type="number" min="1"
                                   value="{{ old('max_occupancy') }}" placeholder="2"
                                   class="form-control @error('max_occupancy') is-invalid @enderror">
                            @error('max_occupancy')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="total_rooms">Inventory</label>
                            <input id="total_rooms" name="total_rooms" type="number" min="1"
                                   value="{{ old('total_rooms') }}" placeholder="5"
                                   class="form-control @error('total_rooms') is-invalid @enderror">
                            @error('total_rooms')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit"><i class="bi bi-check-lg me-1"></i>Create room</button>
                </div>
            </form>
        </div>
    </div>
</div>
