<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Wraps a single computed search result (one hotel with its available rooms)
 * produced by SearchService. The underlying resource is a plain array, so this
 * resource only re-shapes/formats the already-computed values.
 */
class SearchResultResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var array{hotel: array<string, mixed>, nights: int, rooms: list<array<string, mixed>>} $result */
        $result = $this->resource;

        return [
            'hotel' => $result['hotel'],
            'nights' => $result['nights'],
            'rooms' => array_map(
                static fn (array $room): array => [
                    'id' => $room['id'],
                    'name' => $room['name'],
                    'price_per_night' => number_format((float) $room['price_per_night'], 2, '.', ''),
                    'max_occupancy' => $room['max_occupancy'],
                    'available_units' => $room['available_units'],
                    'total_price' => number_format((float) $room['total_price'], 2, '.', ''),
                ],
                $result['rooms'],
            ),
        ];
    }
}
