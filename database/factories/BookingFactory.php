<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Room;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkin = $this->faker->dateTimeBetween('now', '+30 days');
        $checkout = (clone $checkin)->modify('+'.$this->faker->numberBetween(1, 5).' days');

        return [
            'room_id' => Room::factory(),
            'checkin_date' => $checkin->format('Y-m-d'),
            'checkout_date' => $checkout->format('Y-m-d'),
            'guests' => $this->faker->numberBetween(1, 4),
            'status' => Booking::STATUS_CONFIRMED,
            'total_price' => $this->faker->randomFloat(2, 60, 1500),
        ];
    }

    /**
     * Pin the booking to an explicit date range (used by availability tests/seeder).
     */
    public function forRange(CarbonInterface|string $checkin, CarbonInterface|string $checkout): static
    {
        return $this->state(fn (): array => [
            'checkin_date' => $checkin instanceof CarbonInterface ? $checkin->format('Y-m-d') : $checkin,
            'checkout_date' => $checkout instanceof CarbonInterface ? $checkout->format('Y-m-d') : $checkout,
        ]);
    }

    public function forRoom(Room $room): static
    {
        return $this->state(fn (): array => ['room_id' => $room->id]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (): array => ['status' => Booking::STATUS_CANCELLED]);
    }
}
