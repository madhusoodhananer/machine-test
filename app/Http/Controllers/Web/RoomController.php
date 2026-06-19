<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoomRequest;
use App\Services\HotelService;
use App\Services\RoomService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class RoomController extends Controller
{
    /** Rooms shown per page in the list. */
    private const PER_PAGE = 10;

    public function __construct(
        private readonly RoomService $rooms,
        private readonly HotelService $hotels,
    ) {}

    public function index(Request $request): View
    {
        $hotelId = $request->query('hotel');

        try {
            return view('rooms.index', [
                'rooms' => $this->rooms->paginateWithHotel(self::PER_PAGE, $hotelId)->withQueryString(),
                'hotels' => $this->hotels->all(),
                'hotelFilter' => $hotelId,
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            session()->now('error', 'We could not load the rooms list.');

            return view('rooms.index', [
                'rooms' => new LengthAwarePaginator([], 0, self::PER_PAGE),
                'hotels' => collect(),
                'hotelFilter' => $hotelId,
            ]);
        }
    }

    public function store(StoreRoomRequest $request): RedirectResponse
    {
        try {
            $this->rooms->create($request->validated());

            return redirect()
                ->route('rooms.index')
                ->with('status', 'Room created successfully.');
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'We could not create the room. Please try again.');
        }
    }
}
