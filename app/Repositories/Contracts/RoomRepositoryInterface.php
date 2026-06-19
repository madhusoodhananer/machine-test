<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Room;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface RoomRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Room;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Room $room, array $attributes): Room;

    /**
     * Soft-delete a room.
     */
    public function delete(Room $room): void;

    public function find(string $id): ?Room;

    /**
     * Find a room for update inside a transaction (row lock) — used to
     * prevent overbooking races when creating a booking.
     */
    public function findForUpdate(string $id): ?Room;

    /**
     * Paginate rooms with their hotel eager-loaded (for the rooms listing page).
     * Optionally restrict to a single hotel and/or a free-text search that
     * matches the room name or its hotel name.
     *
     * @return LengthAwarePaginator<int, Room>
     */
    public function paginateWithHotel(int $perPage, ?string $hotelId = null, ?string $search = null): LengthAwarePaginator;

    /**
     * All rooms with their hotel eager-loaded, for selection dropdowns.
     *
     * @return Collection<int, Room>
     */
    public function allWithHotel(): Collection;

    public function count(): int;
}
