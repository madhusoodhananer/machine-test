{{-- Shared Edit Room modal. Action + field values come from the triggering row's
     data-* attributes, or are restored from old() after a validation error.
     Param: $hotels (collection for the select). --}}
<div class="modal fade" id="editRoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editRoomForm" method="POST" action="{{ old('__action') }}" novalidate>
                @csrf
                @method('PUT')
                <input type="hidden" name="__action" value="{{ old('__action') }}">

                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i>Edit room</h5>
                        <p class="text-muted small mb-0">Update this room type and its inventory.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="edit_room_hotel_id">Hotel</label>
                        <select id="edit_room_hotel_id" name="hotel_id" class="form-select @error('hotel_id') is-invalid @enderror">
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
                        <label class="form-label" for="edit_room_name">Room name</label>
                        <input id="edit_room_name" name="name" value="{{ old('name') }}"
                               class="form-control @error('name') is-invalid @enderror">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="edit_room_price">Price / night</label>
                            <input id="edit_room_price" name="price_per_night" type="number" step="0.01" min="0"
                                   value="{{ old('price_per_night') }}"
                                   class="form-control @error('price_per_night') is-invalid @enderror">
                            @error('price_per_night')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="edit_room_occupancy">Max guests</label>
                            <input id="edit_room_occupancy" name="max_occupancy" type="number" min="1"
                                   value="{{ old('max_occupancy') }}"
                                   class="form-control @error('max_occupancy') is-invalid @enderror">
                            @error('max_occupancy')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="edit_room_total">Inventory</label>
                            <input id="edit_room_total" name="total_rooms" type="number" min="1"
                                   value="{{ old('total_rooms') }}"
                                   class="form-control @error('total_rooms') is-invalid @enderror">
                            @error('total_rooms')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit"><i class="bi bi-check-lg me-1"></i>Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        const modal = document.getElementById('editRoomModal');
        if (!modal) return;
        const form = document.getElementById('editRoomForm');
        modal.addEventListener('show.bs.modal', function (event) {
            const t = event.relatedTarget;
            if (!t) return; // re-opened after a validation error: keep old() values + action
            const d = t.dataset;
            const action = d.action || '';
            form.setAttribute('action', action);
            modal.querySelector('input[name="__action"]').value = action;
            modal.querySelector('#edit_room_hotel_id').value = d.hotelId || '';
            modal.querySelector('#edit_room_name').value = d.name || '';
            modal.querySelector('#edit_room_price').value = d.price || '';
            modal.querySelector('#edit_room_occupancy').value = d.occupancy || '';
            modal.querySelector('#edit_room_total').value = d.total || '';
        });
    })();
</script>
@endpush
