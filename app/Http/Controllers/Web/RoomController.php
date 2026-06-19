<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoomRequest;
use App\Services\HotelService;
use App\Services\RoomService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function __construct(
        private readonly RoomService $rooms,
        private readonly HotelService $hotels,
    ) {}

    public function index(): View
    {
        return view('rooms.index', [
            'rooms' => $this->rooms->paginateWithHotel(10),
            'hotels' => $this->hotels->all(),
        ]);
    }

    public function store(StoreRoomRequest $request): RedirectResponse
    {
        $this->rooms->create($request->validated());

        return redirect()
            ->route('rooms.index')
            ->with('status', 'Room created successfully.');
    }
}
