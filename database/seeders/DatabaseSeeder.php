<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with an admin user, hotels, rooms,
     * and a spread of bookings so search results vary by date range.
     */
    public function run(): void
    {
        $this->seedAdmin();

        $hotels = $this->seedHotelsAndRooms();

        $this->seedBookings($hotels);
    }

    private function seedAdmin(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ],
        );
    }

    /**
     * @return Collection<int, Hotel>
     */
    private function seedHotelsAndRooms(): Collection
    {
        // name, city, country, rating, [room defs: name, price, occupancy, total_rooms]
        $blueprint = [
            ['Burj Marina Resort', 'Dubai', 'United Arab Emirates', 5, [
                ['Deluxe King', 220.00, 2, 6],
                ['Executive Suite', 450.00, 3, 3],
                ['Family Room', 320.00, 4, 4],
            ]],
            ['Palm Stay Hotel', 'Dubai', 'United Arab Emirates', 4, [
                ['Standard Twin', 120.00, 2, 8],
                ['Deluxe King', 180.00, 2, 5],
            ]],
            ['Thames View Inn', 'London', 'United Kingdom', 4, [
                ['Standard Twin', 140.00, 2, 6],
                ['Deluxe King', 210.00, 2, 4],
                ['Executive Suite', 380.00, 3, 2],
            ]],
            ['Westminster Grand', 'London', 'United Kingdom', 5, [
                ['Deluxe King', 260.00, 2, 5],
                ['Family Room', 360.00, 4, 3],
            ]],
            ['Seine Boutique Hotel', 'Paris', 'France', 4, [
                ['Standard Twin', 130.00, 2, 7],
                ['Deluxe King', 200.00, 2, 4],
            ]],
            ['Eiffel Luxe', 'Paris', 'France', 5, [
                ['Executive Suite', 420.00, 3, 3],
                ['Deluxe King', 240.00, 2, 5],
            ]],
            ['Shinjuku Sky Hotel', 'Tokyo', 'Japan', 4, [
                ['Standard Twin', 110.00, 2, 9],
                ['Deluxe King', 175.00, 2, 5],
                ['Family Room', 290.00, 4, 3],
            ]],
            ['Ginza Capsule Plus', 'Tokyo', 'Japan', 3, [
                ['Standard Twin', 75.00, 2, 12],
            ]],
            ['Asakusa Riverside', 'Tokyo', 'Japan', 4, [
                ['Deluxe King', 160.00, 2, 4],
                ['Executive Suite', 340.00, 3, 2],
            ]],
        ];

        return collect($blueprint)->map(function (array $def): Hotel {
            [$name, $city, $country, $rating, $rooms] = $def;

            $hotel = Hotel::query()->create([
                'name' => $name,
                'city' => $city,
                'country' => $country,
                'rating' => $rating,
            ]);

            foreach ($rooms as [$roomName, $price, $occupancy, $total]) {
                $hotel->rooms()->create([
                    'name' => $roomName,
                    'price_per_night' => $price,
                    'max_occupancy' => $occupancy,
                    'total_rooms' => $total,
                ]);
            }

            return $hotel;
        });
    }

    /**
     * Create bookings spread across dates so some rooms are partially or
     * fully booked for the upcoming weeks, making search results vary.
     *
     * @param  Collection<int, Hotel>  $hotels
     */
    private function seedBookings(Collection $hotels): void
    {
        $today = Carbon::today();

        $hotels->flatMap->rooms->each(function (Room $room) use ($today): void {
            // Book larger-inventory rooms a few times, smaller ones lightly.
            $bookingCount = $room->total_rooms > 4 ? 3 : 1;

            for ($i = 0; $i < $bookingCount; $i++) {
                $checkin = $today->copy()->addDays(($i * 4) + 2);
                $checkout = $checkin->copy()->addDays(rand(2, 4));
                $nights = (int) $checkin->diffInDays($checkout);

                Booking::query()->create([
                    'room_id' => $room->id,
                    'checkin_date' => $checkin->toDateString(),
                    'checkout_date' => $checkout->toDateString(),
                    'guests' => min(2, $room->max_occupancy),
                    'status' => Booking::STATUS_CONFIRMED,
                    'total_price' => round((float) $room->price_per_night * $nights, 2),
                ]);
            }
        });
    }
}
