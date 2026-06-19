<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Booking;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BookingRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Booking;

    /**
     * Soft-delete a booking.
     */
    public function delete(Booking $booking): void;

    public function count(): int;

    /**
     * Paginate bookings, newest first, with their room and hotel eager-loaded
     * (for the bookings listing page).
     *
     * @return LengthAwarePaginator<int, Booking>
     */
    public function paginateWithRoomAndHotel(int $perPage): LengthAwarePaginator;

    /**
     * Confirmed bookings that overlap [checkin, checkout) for the given rooms,
     * grouped by room_id. Overlap rule (half-open intervals):
     *   existing.checkin < requested.checkout AND existing.checkout > requested.checkin
     *
     * @param  list<string>  $roomIds
     * @return Collection<string, Collection<int, Booking>>
     */
    public function overlappingForRooms(array $roomIds, string $checkin, string $checkout): Collection;

    /**
     * Confirmed overlapping bookings for a single room. When $lock is true the
     * rows are read with a pessimistic lock (inside a transaction) so concurrent
     * booking attempts cannot overbook.
     *
     * @return Collection<int, Booking>
     */
    public function overlappingForRoom(string $roomId, string $checkin, string $checkout, bool $lock = false): Collection;
}
