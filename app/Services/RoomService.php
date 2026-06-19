<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Room;
use App\Repositories\Contracts\RoomRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RoomService
{
    public function __construct(
        private readonly RoomRepositoryInterface $rooms,
    ) {}

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Room
    {
        return $this->rooms->create($attributes);
    }

    /**
     * @return LengthAwarePaginator<int, Room>
     */
    public function paginateWithHotel(int $perPage = 15): LengthAwarePaginator
    {
        return $this->rooms->paginateWithHotel($perPage);
    }

    public function count(): int
    {
        return $this->rooms->count();
    }
}
