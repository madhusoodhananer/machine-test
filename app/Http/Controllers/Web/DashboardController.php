<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\BookingService;
use App\Services\HotelService;
use App\Services\RoomService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly HotelService $hotels,
        private readonly RoomService $rooms,
        private readonly BookingService $bookings,
    ) {}

    public function index(): View
    {
        return view('dashboard', [
            'totalHotels' => $this->hotels->count(),
            'totalRooms' => $this->rooms->count(),
            'totalBookings' => $this->bookings->count(),
            'averageRating' => $this->hotels->averageRating(),
        ]);
    }
}
