<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    private const CHECKIN = '2026-08-01';

    private const CHECKOUT = '2026-08-04'; // 3 nights

    private function room(int $totalRooms = 1, float $price = 100): Room
    {
        return Room::factory()
            ->forHotel(Hotel::factory()->inCity('Bookville')->create())
            ->create([
                'price_per_night' => $price,
                'max_occupancy' => 2,
                'total_rooms' => $totalRooms,
            ]);
    }

    public function test_booking_requires_authentication(): void
    {
        $room = $this->room();

        $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'checkin_date' => self::CHECKIN,
            'checkout_date' => self::CHECKOUT,
            'guests' => 2,
        ])->assertStatus(401);
    }

    public function test_authenticated_user_can_book_and_price_is_snapshotted(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $room = $this->room(totalRooms: 1, price: 100);

        $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'checkin_date' => self::CHECKIN,
            'checkout_date' => self::CHECKOUT,
            'guests' => 2,
        ])
            ->assertCreated()
            ->assertJsonPath('data.status', Booking::STATUS_CONFIRMED)
            ->assertJsonPath('data.total_price', '300.00'); // 100 * 3 nights

        $this->assertDatabaseHas('bookings', ['room_id' => $room->id, 'guests' => 2]);
    }

    public function test_booking_is_rejected_with_422_when_no_inventory_left(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $room = $this->room(totalRooms: 1);

        // Consume the only unit.
        Booking::factory()->forRoom($room)->forRange(self::CHECKIN, self::CHECKOUT)->create();

        $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'checkin_date' => self::CHECKIN,
            'checkout_date' => self::CHECKOUT,
            'guests' => 2,
        ])->assertStatus(422);
    }

    public function test_booking_reduces_subsequent_search_availability(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $room = $this->room(totalRooms: 2);

        $searchUrl = '/api/search?'.http_build_query([
            'city' => 'Bookville',
            'checkin_date' => self::CHECKIN,
            'checkout_date' => self::CHECKOUT,
            'guests' => 2,
        ]);

        $this->getJson($searchUrl)->assertJsonPath('data.0.rooms.0.available_units', 2);

        $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'checkin_date' => self::CHECKIN,
            'checkout_date' => self::CHECKOUT,
            'guests' => 2,
        ])->assertCreated();

        // Cache was busted by the booking write -> availability now reflects 1 left.
        $this->getJson($searchUrl)->assertJsonPath('data.0.rooms.0.available_units', 1);
    }
}
