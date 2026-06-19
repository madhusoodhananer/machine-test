<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Hotel;
use App\Models\Room;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Contracts\HotelRepositoryInterface;
use App\Services\Search\SearchResultCache;
use Illuminate\Support\Carbon;

class SearchService
{
    public function __construct(
        private readonly HotelRepositoryInterface $hotels,
        private readonly BookingRepositoryInterface $bookings,
        private readonly BookingService $bookingService,
        private readonly SearchResultCache $cache,
    ) {}

    /**
     * Search hotels in a city for rooms available across the requested range.
     *
     * @param  array{city: string, checkin_date: string, checkout_date: string, guests: int}  $params
     * @return array{results: list<array<string, mixed>>, meta: array<string, mixed>}
     */
    public function search(array $params): array
    {
        $city = trim($params['city']);
        $guests = (int) $params['guests'];
        $checkin = Carbon::parse($params['checkin_date'])->startOfDay();
        $checkout = Carbon::parse($params['checkout_date'])->startOfDay();
        $nights = (int) $checkin->diffInDays($checkout);

        $fingerprint = md5(implode('|', [
            mb_strtolower($city),
            $checkin->toDateString(),
            $checkout->toDateString(),
            $guests,
        ]));

        return $this->cache->remember(
            $fingerprint,
            fn (): array => $this->compute($city, $guests, $checkin, $checkout, $nights),
        );
    }

    /**
     * @return array{results: list<array<string, mixed>>, meta: array<string, mixed>}
     */
    private function compute(string $city, int $guests, Carbon $checkin, Carbon $checkout, int $nights): array
    {
        $hotels = $this->hotels->withRoomsInCity($city, $guests);

        $roomIds = $hotels
            ->flatMap(fn (Hotel $hotel) => $hotel->rooms->pluck('id'))
            ->map(fn ($id): int => (int) $id)
            ->values()
            ->all();

        $overlappingByRoom = $this->bookings->overlappingForRooms(
            $roomIds,
            $checkin->toDateString(),
            $checkout->toDateString(),
        );

        $results = $hotels
            ->map(function (Hotel $hotel) use ($overlappingByRoom, $checkin, $checkout, $nights): ?array {
                $rooms = $hotel->rooms
                    ->map(function (Room $room) use ($overlappingByRoom, $checkin, $checkout, $nights): ?array {
                        $overlapping = $overlappingByRoom->get($room->id, collect());

                        $units = $this->bookingService->availableUnits(
                            $room->total_rooms,
                            $overlapping,
                            $checkin,
                            $checkout,
                        );

                        if ($units < 1) {
                            return null;
                        }

                        return [
                            'id' => $room->id,
                            'name' => $room->name,
                            'price_per_night' => (float) $room->price_per_night,
                            'max_occupancy' => $room->max_occupancy,
                            'available_units' => $units,
                            'total_price' => round((float) $room->price_per_night * $nights, 2),
                        ];
                    })
                    ->filter()
                    ->values();

                if ($rooms->isEmpty()) {
                    return null;
                }

                return [
                    'hotel' => [
                        'id' => $hotel->id,
                        'name' => $hotel->name,
                        'city' => $hotel->city,
                        'country' => $hotel->country,
                        'rating' => $hotel->rating,
                    ],
                    'nights' => $nights,
                    'rooms' => $rooms->all(),
                ];
            })
            ->filter()
            ->values()
            ->all();

        return [
            'results' => $results,
            'meta' => [
                'checkin_date' => $checkin->toDateString(),
                'checkout_date' => $checkout->toDateString(),
                'guests' => $guests,
                'nights' => $nights,
            ],
        ];
    }
}
