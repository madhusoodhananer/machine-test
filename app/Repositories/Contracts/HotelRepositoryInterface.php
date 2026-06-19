<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Hotel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface HotelRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Hotel;

    /**
     * Paginate hotels applying optional filters.
     *
     * @param  array{city?: string|null, rating?: int|null}  $filters
     * @return LengthAwarePaginator<int, Hotel>
     */
    public function paginate(array $filters, int $perPage): LengthAwarePaginator;

    /**
     * All hotels, lightweight, for dropdowns.
     *
     * @return Collection<int, Hotel>
     */
    public function all(): Collection;

    /**
     * Hotels in a given city with rooms eager-loaded (used by search).
     *
     * @return Collection<int, Hotel>
     */
    public function withRoomsInCity(string $city, int $minOccupancy): Collection;

    public function count(): int;
}
