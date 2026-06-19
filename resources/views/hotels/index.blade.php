@extends('layouts.app')

@section('title', 'Hotels')

@section('content')
    <h1 class="h3 mb-4">Hotels</h1>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5 mb-3">Add Hotel</h2>
                    <form method="POST" action="{{ route('hotels.store') }}" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="name">Name</label>
                            <input id="name" name="name" value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="city">City</label>
                            <input id="city" name="city" value="{{ old('city') }}"
                                   class="form-control @error('city') is-invalid @enderror">
                            @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="country">Country</label>
                            <input id="country" name="country" value="{{ old('country') }}"
                                   class="form-control @error('country') is-invalid @enderror">
                            @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="rating">Rating (1–5)</label>
                            <select id="rating" name="rating" class="form-select @error('rating') is-invalid @enderror">
                                <option value="">Choose…</option>
                                @for ($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" @selected((int) old('rating') === $i)>{{ $i }}</option>
                                @endfor
                            </select>
                            @error('rating')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button class="btn btn-dark w-100" type="submit">Create Hotel</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('hotels.index') }}" class="row g-2 mb-3">
                        <div class="col">
                            <input name="city" value="{{ $city }}" class="form-control" placeholder="Filter by city">
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-dark" type="submit">Filter</button>
                            <a href="{{ route('hotels.index') }}" class="btn btn-link">Reset</a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>City</th>
                                    <th>Country</th>
                                    <th>Rating</th>
                                    <th>Rooms</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($hotels as $hotel)
                                    <tr>
                                        <td>{{ $hotel->id }}</td>
                                        <td>{{ $hotel->name }}</td>
                                        <td>{{ $hotel->city }}</td>
                                        <td>{{ $hotel->country }}</td>
                                        <td>{{ str_repeat('★', $hotel->rating) }}</td>
                                        <td>{{ $hotel->rooms_count }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted py-4">No hotels found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $hotels->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
