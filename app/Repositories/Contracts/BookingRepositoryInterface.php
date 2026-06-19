<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Booking;
use Illuminate\Support\Collection;

interface BookingRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Booking;

    public function count(): int;

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
