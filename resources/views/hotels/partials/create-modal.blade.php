{{-- Add Hotel modal. Posts to hotels.store (name, city, country, rating). --}}
<div class="modal fade" id="createHotelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('hotels.store') }}" novalidate>
                @csrf
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold"><i class="bi bi-building-add text-primary me-2"></i>Add hotel</h5>
                        <p class="text-muted small mb-0">Create a new property in your inventory.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="name">Hotel name</label>
                        <input id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Burj Marina Resort"
                               class="form-control @error('name') is-invalid @enderror">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="city">City</label>
                            <select id="city" name="city" class="form-select @error('city') is-invalid @enderror">
                                <option value="">Choose a city…</option>
                                @foreach (config('locations.cities') as $cityOption)
                                    <option value="{{ $cityOption }}" @selected(old('city') === $cityOption)>{{ $cityOption }}</option>
                                @endforeach
                            </select>
                            @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="country">Country</label>
                            <select id="country" name="country" class="form-select @error('country') is-invalid @enderror">
                                <option value="">Choose a country…</option>
                                @foreach (config('locations.countries') as $countryOption)
                                    <option value="{{ $countryOption }}" @selected(old('country') === $countryOption)>{{ $countryOption }}</option>
                                @endforeach
                            </select>
                            @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label" for="rating">Star rating</label>
                        <select id="rating" name="rating" class="form-select @error('rating') is-invalid @enderror">
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
                    <button class="btn btn-primary" type="submit"><i class="bi bi-check-lg me-1"></i>Create hotel</button>
                </div>
            </form>
        </div>
    </div>
</div>
