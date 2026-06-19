<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoomRequest;
use App\Services\HotelService;
use App\Services\RoomService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function __construct(
        private readonly RoomService $rooms,
        private readonly HotelService $hotels,
    ) {}

    public function index(): View
    {
        try {
            return view('rooms.index', [
                'rooms' => $this->rooms->paginateWithHotel(10),
                'hotels' => $this->hotels->all(),
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            session()->now('error', 'We could not load the rooms list.');

            return view('rooms.index', [
                'rooms' => new LengthAwarePaginator([], 0, 10),
                'hotels' => collect(),
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
