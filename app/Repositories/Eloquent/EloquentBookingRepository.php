<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Booking;
use App\Repositories\Contracts\BookingRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class EloquentBookingRepository implements BookingRepositoryInterface
{
    public function create(array $attributes): Booking
    {
        return Booking::query()->create($attributes);
    }

    public function count(): int
    {
        return Booking::query()->count();
    }

    public function overlappingForRooms(array $roomIds, string $checkin, string $checkout): Collection
    {
        if ($roomIds === []) {
            return collect();
        }

        return Booking::query()
            ->whereIn('room_id', $roomIds)
            ->tap(fn (Builder $query) => $this->applyOverlap($query, $checkin, $checkout))
            ->get(['id', 'room_id', 'checkin_date', 'checkout_date'])
            ->groupBy('room_id');
    }

    public function overlappingForRoom(string $roomId, string $checkin, string $checkout, bool $lock = false): Collection
    {
        return Booking::query()
            ->where('room_id', $roomId)
            ->tap(fn (Builder $query) => $this->applyOverlap($query, $checkin, $checkout))
            ->when($lock, fn (Builder $query) => $query->lockForUpdate())
            ->get(['id', 'room_id', 'checkin_date', 'checkout_date']);
    }

    /**
     * Confirmed bookings overlapping the half-open range [checkin, checkout):
     *   existing.checkin < requested.checkout AND existing.checkout > requested.checkin
     */
    private function applyOverlap(Builder $query, string $checkin, string $checkout): void
    {
        $query
            ->where('status', Booking::STATUS_CONFIRMED)
            ->where('checkin_date', '<', $checkout)
            ->where('checkout_date', '>', $checkin);
    }
}
