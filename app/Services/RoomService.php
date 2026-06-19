<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Room;
use App\Repositories\Contracts\RoomRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class RoomService
{
    public function __construct(
        private readonly RoomRepositoryInterface $rooms,
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
