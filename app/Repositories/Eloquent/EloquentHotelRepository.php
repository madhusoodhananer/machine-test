<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Hotel;
use App\Repositories\Contracts\HotelRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentHotelRepository implements HotelRepositoryInterface
{
    public function create(array $attributes): Hotel
    {
        return Hotel::query()->create($attributes);
    }

    public function update(Hotel $hotel, array $attributes): Hotel
    {
        $hotel->update($attributes);

        return $hotel;
    }

    public function delete(Hotel $hotel): void
    {
        $hotel->delete();
    }

    public function paginate(array $filters, int $perPage): LengthAwarePaginator
    {
        return Hotel::query()
            ->withCount('rooms')
            ->when(
                filled($filters['city'] ?? null),
                fn ($query) => $query->whereRaw('LOWER(city) = ?', [mb_strtolower((string) $filters['city'])]),
            )
            ->when(
                filled($filters['rating'] ?? null),
                fn ($query) => $query->where('rating', '>=', (int) $filters['rating']),
            )
            ->latest() // newest first by created_at (UUID keys are not time-ordered)
            ->paginate($perPage);
    }

    public function all(): Collection
    {
        return Hotel::query()->orderBy('name')->get();
    }

    public function withRoomsInCity(string $city, int $minOccupancy): Collection
    {
        return Hotel::query()
            ->whereRaw('LOWER(city) = ?', [mb_strtolower($city)])
            ->with(['rooms' => fn ($query) => $query->where('max_occupancy', '>=', $minOccupancy)])
            ->orderByDesc('rating')
            ->get();
    }

    public function count(): int
    {
        return Hotel::query()->count();
    }
}
