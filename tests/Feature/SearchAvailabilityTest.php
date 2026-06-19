<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    private const CITY = 'Testville';

    private const CHECKIN = '2026-07-01';

    private const CHECKOUT = '2026-07-04'; // 3 nights

    private function hotel(): Hotel
    {
        return Hotel::factory()->inCity(self::CITY)->create();
    }

    private function searchUrl(int $guests = 2, string $checkin = self::CHECKIN, string $checkout = self::CHECKOUT): string
    {
        return '/api/search?'.http_build_query([
            'city' => self::CITY,
            'checkin_date' => $checkin,
            'checkout_date' => $checkout,
            'guests' => $guests,
        ]);
    }

    public function test_room_with_no_bookings_exposes_full_inventory_and_price(): void
    {
        Room::factory()->forHotel($this->hotel())->create([
            'name' => 'Deluxe King',
            'price_per_night' => 120,
            'max_occupancy' => 2,
            'total_rooms' => 4,
        ]);

        $this->getJson($this->searchUrl())
            ->assertOk()
            ->assertJsonPath('meta.nights', 3)
            ->assertJsonPath('data.0.rooms.0.available_units', 4)
            ->assertJsonPath('data.0.rooms.0.price_per_night', '120.00')
            ->assertJsonPath('data.0.rooms.0.total_price', '360.00');
    }

    public function test_fully_booked_room_is_excluded(): void
    {
        $room = Room::factory()->forHotel($this->hotel())->create([
            'max_occupancy' => 2,
            'total_rooms' => 1,
        ]);

        Booking::factory()->forRoom($room)->forRange(self::CHECKIN, self::CHECKOUT)->create();

        $this->getJson($this->searchUrl())
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_partial_overlap_reduces_available_units(): void
    {
        $room = Room::factory()->forHotel($this->hotel())->create([
            'max_occupancy' => 2,
            'total_rooms' => 3,
        ]);

        // Two bookings overlap the busiest night -> 3 - 2 = 1 unit free.
        Booking::factory()->forRoom($room)->forRange('2026-07-01', '2026-07-03')->create();
        Booking::factory()->forRoom($room)->forRange('2026-07-02', '2026-07-04')->create();

        $this->getJson($this->searchUrl())
            ->assertOk()
            ->assertJsonPath('data.0.rooms.0.available_units', 1);
    }

    public function test_checkout_day_is_free_under_half_open_rule(): void
    {
        $room = Room::factory()->forHotel($this->hotel())->create([
            'max_occupancy' => 2,
            'total_rooms' => 1,
        ]);

        // Existing booking ends exactly on the requested check-in date -> no overlap.
        Booking::factory()->forRoom($room)->forRange('2026-06-28', self::CHECKIN)->create();

        $this->getJson($this->searchUrl())
            ->assertOk()
            ->assertJsonPath('data.0.rooms.0.available_units', 1);
    }

    public function test_rooms_below_requested_occupancy_are_excluded(): void
    {
        Room::factory()->forHotel($this->hotel())->create([
            'max_occupancy' => 2,
            'total_rooms' => 5,
        ]);

        // guests = 3 exceeds max_occupancy 2 -> no rooms returned.
        $this->getJson($this->searchUrl(guests: 3))
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_validation_rejects_checkout_before_checkin(): void
    {
        $this->getJson($this->searchUrl(checkin: '2026-07-04', checkout: '2026-07-01'))
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('checkout_date');
    }
}
