{{-- Search filter card. Param: $filters (city, checkin_date, checkout_date, guests) --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('search.index') }}" class="row g-3" novalidate>
            <div class="col-md-4">
                <label class="form-label" for="city"><i class="bi bi-geo-alt me-1"></i>City</label>
                @php($selectedCity = $filters['city'] ?? '')
                <select id="city" name="city" class="form-select @error('city') is-invalid @enderror">
                    <option value="">Choose a city…</option>
                    @php($cityOptions = collect(config('locations.cities'))->push($selectedCity)->filter()->unique())
                    @foreach ($cityOptions as $cityOption)
                        <option value="{{ $cityOption }}" @selected($selectedCity === $cityOption)>{{ $cityOption }}</option>
                    @endforeach
                </select>
                @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            @php($today = now()->toDateString())
            @php($tomorrow = now()->addDay()->toDateString())
            <div class="col-md-3">
                <label class="form-label" for="checkin_date"><i class="bi bi-calendar-event me-1"></i>Check-in</label>
                <input id="checkin_date" name="checkin_date" type="date" value="{{ $filters['checkin_date'] ?? $today }}"
                       min="{{ $today }}"
                       class="form-control @error('checkin_date') is-invalid @enderror">
                @error('checkin_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label" for="checkout_date"><i class="bi bi-calendar-check me-1"></i>Check-out</label>
                <input id="checkout_date" name="checkout_date" type="date" value="{{ $filters['checkout_date'] ?? $tomorrow }}"
                       min="{{ $tomorrow }}"
                       class="form-control @error('checkout_date') is-invalid @enderror">
                @error('checkout_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-2">
                <label class="form-label" for="guests"><i class="bi bi-people me-1"></i>Guests</label>
                <input id="guests" name="guests" type="number" min="1" value="{{ $filters['guests'] ?? 1 }}"
                       class="form-control @error('guests') is-invalid @enderror">
                @error('guests')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <button class="btn btn-primary px-4" type="submit"><i class="bi bi-search me-1"></i>Search availability</button>
            </div>
        </form>
    </div>
</div>
