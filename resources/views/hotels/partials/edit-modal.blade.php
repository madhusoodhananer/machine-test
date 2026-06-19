{{-- Shared Edit Hotel modal. Action + field values are set from the triggering
     row's data-* attributes, or restored from old() after a validation error. --}}
<div class="modal fade" id="editHotelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editHotelForm" method="POST" action="{{ old('__action') }}" novalidate>
                @csrf
                @method('PUT')
                {{-- Carries the update URL through a failed submit so the modal can re-open. --}}
                <input type="hidden" name="__action" value="{{ old('__action') }}">

                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i>Edit hotel</h5>
                        <p class="text-muted small mb-0">Update this property's details.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="edit_name">Hotel name</label>
                        <input id="edit_name" name="name" value="{{ old('name') }}"
                               class="form-control @error('name') is-invalid @enderror">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="edit_city">City</label>
                            <input id="edit_city" name="city" value="{{ old('city') }}"
                                   class="form-control @error('city') is-invalid @enderror">
                            @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="edit_country">Country</label>
                            <input id="edit_country" name="country" value="{{ old('country') }}"
                                   class="form-control @error('country') is-invalid @enderror">
                            @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label" for="edit_rating">Star rating</label>
                        <select id="edit_rating" name="rating" class="form-select @error('rating') is-invalid @enderror">
                            <option value="">Choose…</option>
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" @selected((int) old('rating') === $i)>{{ str_repeat('★', $i) }} ({{ $i }})</option>
                            @endfor
                        </select>
                        @error('rating')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
        const modal = document.getElementById('editHotelModal');
        if (!modal) return;
        const form = document.getElementById('editHotelForm');
        modal.addEventListener('show.bs.modal', function (event) {
            const t = event.relatedTarget;
            if (!t) return; // re-opened after a validation error: keep old() values + action
            const d = t.dataset;
            const action = d.action || '';
            form.setAttribute('action', action);
            modal.querySelector('input[name="__action"]').value = action;
            modal.querySelector('#edit_name').value = d.name || '';
            modal.querySelector('#edit_city').value = d.city || '';
            modal.querySelector('#edit_country').value = d.country || '';
            modal.querySelector('#edit_rating').value = d.rating || '';
        });
    })();
</script>
@endpush
