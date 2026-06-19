@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1 class="h3 mb-4">Dashboard</h1>

    <div class="row g-3">
        <div class="col-sm-6 col-lg-3">
            <div class="card text-bg-primary shadow-sm h-100">
                <div class="card-body">
                    <div class="text-uppercase small opacity-75">Total Hotels</div>
                    <div class="display-6 fw-bold">{{ $totalHotels }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card text-bg-success shadow-sm h-100">
                <div class="card-body">
                    <div class="text-uppercase small opacity-75">Total Rooms</div>
                    <div class="display-6 fw-bold">{{ $totalRooms }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card text-bg-info shadow-sm h-100">
                <div class="card-body">
                    <div class="text-uppercase small opacity-75">Total Bookings</div>
                    <div class="display-6 fw-bold">{{ $totalBookings }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card text-bg-warning shadow-sm h-100">
                <div class="card-body">
                    <div class="text-uppercase small opacity-75">Avg. Rating</div>
                    <div class="display-6 fw-bold">{{ number_format($averageRating, 1) }} ★</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">Quick actions</h2>
            <div class="d-flex gap-2 flex-wrap mt-2">
                <a href="{{ route('hotels.index') }}" class="btn btn-outline-dark btn-sm">Manage Hotels</a>
                <a href="{{ route('rooms.index') }}" class="btn btn-outline-dark btn-sm">Manage Rooms</a>
                <a href="{{ route('search.index') }}" class="btn btn-dark btn-sm">Search Availability</a>
            </div>
        </div>
    </div>
@endsection
