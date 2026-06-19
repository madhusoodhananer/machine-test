<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Room;
use App\Repositories\Contracts\RoomRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentRoomRepository implements RoomRepositoryInterface
{
    public function create(array $attributes): Room
    {
        return Room::query()->create($attributes);
    }

    public function update(Room $room, array $attributes): Room
    {
        $room->update($attributes);

        return $room;
    }

    public function delete(Room $room): void
    {
        $room->delete();
    }

    public function find(string $id): ?Room
    {
        return Room::query()->find($id);
    }

    public function findForUpdate(string $id): ?Room
    {
        return Room::query()->lockForUpdate()->find($id);
    }

    public function paginateWithHotel(int $perPage, ?string $hotelId = null, ?string $search = null): LengthAwarePaginator
    {
        return Room::query()
            ->with('hotel')
            ->when(filled($hotelId), fn ($query) => $query->where('hotel_id', $hotelId))
            ->when(filled($search), function ($query) use ($search) {
                $term = '%'.$search.'%';
                $query->where(function ($inner) use ($term) {
                    $inner->where('name', 'like', $term)
                        ->orWhereHas('hotel', fn ($hotel) => $hotel->where('name', 'like', $term));
                });
            })
            ->latest() // newest first by created_at (UUID keys are not time-ordered)
            ->paginate($perPage);
    }

    public function allWithHotel(): Collection
    {
        return Room::query()
            ->with('hotel')
            ->orderBy('name')
            ->get();
    }

    public function count(): int
    {
        return Room::query()->count();
    }
}
