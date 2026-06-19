<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Room;
use App\Repositories\Contracts\RoomRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentRoomRepository implements RoomRepositoryInterface
{
    public function create(array $attributes): Room
    {
        return Room::query()->create($attributes);
    }

    public function find(string $id): ?Room
    {
        return Room::query()->find($id);
    }

    public function findForUpdate(string $id): ?Room
    {
        return Room::query()->lockForUpdate()->find($id);
    }

    public function paginateWithHotel(int $perPage): LengthAwarePaginator
    {
        return Room::query()
            ->with('hotel')
            ->latest() // newest first by created_at (UUID keys are not time-ordered)
            ->paginate($perPage);
    }

    public function count(): int
    {
        return Room::query()->count();
    }
}
