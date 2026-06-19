<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ResourceInUseException;
use App\Models\Room;
use App\Repositories\Contracts\RoomRepositoryInterface;
use App\Services\Search\SearchResultCache;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class RoomService
{
    public function __construct(
        private readonly RoomRepositoryInterface $rooms,
        private readonly SearchResultCache $searchCache,
    ) {}

    /**
     * @return Collection<int, Room>
     */
    public function allWithHotel(): Collection
    {
        return $this->rooms->allWithHotel();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Room
    {
        return $this->rooms->create($attributes);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Room $room, array $attributes): Room
    {
        return $this->rooms->update($room, $attributes);
    }

    /**
     * Delete a room — only when it has no bookings. Busts cached search
     * results since removing a room changes availability.
     *
     * @throws ResourceInUseException when the room still has bookings.
     */
    public function delete(Room $room): void
    {
        $bookingCount = $room->bookings()->count();

        if ($bookingCount > 0) {
            throw ResourceInUseException::roomHasBookings($bookingCount);
        }

        $this->rooms->delete($room);

        $this->searchCache->bump();
    }

    /**
     * @return LengthAwarePaginator<int, Room>
     */
    public function paginateWithHotel(int $perPage = 15, ?string $hotelId = null, ?string $search = null): LengthAwarePaginator
    {
        return $this->rooms->paginateWithHotel($perPage, $hotelId, $search);
    }

    public function count(): int
    {
        return $this->rooms->count();
    }
}
