<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\RoomNotAvailableException;
use App\Models\Booking;
use App\Models\Room;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Contracts\RoomRepositoryInterface;
use App\Services\Search\SearchResultCache;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function __construct(
        private readonly BookingRepositoryInterface $bookings,
        private readonly RoomRepositoryInterface $rooms,
        private readonly SearchResultCache $searchCache,
    ) {}

    /**
     * Available units of a room for the half-open range [checkin, checkout).
     *
     * Per the availability rule: for every night in the range we count the
     * confirmed bookings covering that night; the busiest single night caps
     * how many units are free. A booking covers night D when
     * booking.checkin <= D < booking.checkout.
     *
     * @param  Collection<int, Booking>  $overlapping  confirmed bookings already overlapping the range
     */
    public function availableUnits(int $totalRooms, Collection $overlapping, Carbon $checkin, Carbon $checkout): int
    {
        $maxConcurrent = 0;

        for ($night = $checkin->copy(); $night->lt($checkout); $night->addDay()) {
            $booked = $overlapping->filter(
                fn (Booking $booking): bool => $night->gte($booking->checkin_date)
                    && $night->lt($booking->checkout_date),
            )->count();

            $maxConcurrent = max($maxConcurrent, $booked);
        }

        return max(0, $totalRooms - $maxConcurrent);
    }

    /**
     * Create a confirmed booking, guarding against overbooking with a row lock
     * inside a transaction. Busts cached search results on success.
     *
     * @param  array{room_id: string, checkin_date: string, checkout_date: string, guests: int}  $data
     */
    public function create(array $data): Booking
    {
        $checkin = Carbon::parse($data['checkin_date'])->startOfDay();
        $checkout = Carbon::parse($data['checkout_date'])->startOfDay();
        $nights = (int) $checkin->diffInDays($checkout);

        $booking = DB::transaction(function () use ($data, $checkin, $checkout, $nights): Booking {
            $room = $this->rooms->findForUpdate($data['room_id']);

            if (! $room instanceof Room) {
                throw RoomNotAvailableException::forRange($data['room_id'], $checkin->toDateString(), $checkout->toDateString());
            }

            $overlapping = $this->bookings->overlappingForRoom(
                $room->id,
                $checkin->toDateString(),
                $checkout->toDateString(),
                lock: true,
            );

            if ($this->availableUnits($room->total_rooms, $overlapping, $checkin, $checkout) < 1) {
                throw RoomNotAvailableException::forRange($room->id, $checkin->toDateString(), $checkout->toDateString());
            }

            return $this->bookings->create([
                'room_id' => $room->id,
                'checkin_date' => $checkin->toDateString(),
                'checkout_date' => $checkout->toDateString(),
                'guests' => (int) $data['guests'],
                'status' => Booking::STATUS_CONFIRMED,
                'total_price' => round((float) $room->price_per_night * $nights, 2),
            ]);
        });

        $this->searchCache->bump();

        return $booking;
    }

    public function count(): int
    {
        return $this->bookings->count();
    }
}
