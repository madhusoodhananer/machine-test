<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Room;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface RoomRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Room;

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

    public function count(): int;
}
