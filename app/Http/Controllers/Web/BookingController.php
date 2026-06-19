<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Exceptions\RoomNotAvailableException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Services\BookingService;
use App\Services\RoomService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class BookingController extends Controller
{
    /** Bookings shown per page in the list. */
    private const PER_PAGE = 10;

    public function __construct(
        private readonly BookingService $bookings,
        private readonly RoomService $rooms,
    ) {}

    public function index(): View
    {
        try {
            return view('bookings.index', [
                'bookings' => $this->bookings->paginate(self::PER_PAGE),
                'rooms' => $this->rooms->allWithHotel(),
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            session()->now('error', 'We could not load the bookings list.');

            return view('bookings.index', [
                'bookings' => new LengthAwarePaginator([], 0, self::PER_PAGE),
                'rooms' => collect(),
            ]);
        }
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        /** @var array{room_id: string, checkin_date: string, checkout_date: string, guests: int} $data */
        $data = $request->validated();

        try {
            $this->bookings->create($data);

            return redirect()
                ->route('bookings.index')
                ->with('status', 'Booking confirmed successfully.');
        } catch (RoomNotAvailableException $exception) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $exception->getMessage());
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'We could not create the booking. Please try again.');
        }
    }

    public function destroy(Booking $booking): RedirectResponse
    {
        try {
            $this->bookings->delete($booking);

            return redirect()
                ->route('bookings.index')
                ->with('status', 'Booking deleted successfully.');
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()
                ->back()
                ->with('error', 'We could not delete the booking. Please try again.');
        }
    }
}
