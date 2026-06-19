<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Exceptions\ResourceInUseException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoomRequest;
use App\Models\Room;
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
        $search = $request->query('search');

        try {
            return view('rooms.index', [
                'rooms' => $this->rooms->paginateWithHotel(self::PER_PAGE, $hotelId, $search)->withQueryString(),
                'hotels' => $this->hotels->all(),
                'hotelFilter' => $hotelId,
                'search' => $search,
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            session()->now('error', 'We could not load the rooms list.');

            return view('rooms.index', [
                'rooms' => new LengthAwarePaginator([], 0, self::PER_PAGE),
                'hotels' => collect(),
                'hotelFilter' => $hotelId,
                'search' => $search,
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

    public function update(StoreRoomRequest $request, Room $room): RedirectResponse
    {
        try {
            $this->rooms->update($room, $request->validated());

            return redirect()
                ->route('rooms.index')
                ->with('status', 'Room updated successfully.');
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'We could not update the room. Please try again.');
        }
    }

    public function destroy(Room $room): RedirectResponse
    {
        try {
            $this->rooms->delete($room);

            return redirect()
                ->route('rooms.index')
                ->with('status', 'Room deleted successfully.');
        } catch (ResourceInUseException $exception) {
            return redirect()
                ->back()
                ->with('error', $exception->getMessage());
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()
                ->back()
                ->with('error', 'We could not delete the room. Please try again.');
        }
    }
}
