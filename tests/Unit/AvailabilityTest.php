<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

class AvailabilityTest extends TestCase
{
    private function service(): BookingService
    {
        return app(BookingService::class);
    }

    /**
     * @param  array<int, array{0: string, 1: string}>  $ranges
     * @return Collection<int, Booking>
     */
    private function bookings(array $ranges): Collection
    {
        return collect($ranges)->map(fn (array $range): Booking => new Booking([
            'checkin_date' => $range[0],
            'checkout_date' => $range[1],
        ]));
    }

    public function test_full_inventory_available_when_no_bookings(): void
    {
        $units = $this->service()->availableUnits(
            totalRooms: 5,
            overlapping: collect(),
            checkin: Carbon::parse('2026-07-01'),
            checkout: Carbon::parse('2026-07-04'),
        );

        $this->assertSame(5, $units);
    }

    public function test_room_is_excluded_when_fully_booked_for_the_range(): void
    {
        // 2 units, both booked across the whole requested range -> 0 left.
        $overlapping = $this->bookings([
            ['2026-07-01', '2026-07-04'],
            ['2026-07-01', '2026-07-04'],
        ]);

        $units = $this->service()->availableUnits(
            totalRooms: 2,
            overlapping: $overlapping,
            checkin: Carbon::parse('2026-07-01'),
            checkout: Carbon::parse('2026-07-04'),
        );

        $this->assertSame(0, $units);
    }

    public function test_partial_overlap_reduces_units_by_busiest_night(): void
    {
        // total 3. One booking covers the first two nights only.
        $overlapping = $this->bookings([
            ['2026-07-01', '2026-07-03'],
        ]);

        $units = $this->service()->availableUnits(
            totalRooms: 3,
            overlapping: $overlapping,
            checkin: Carbon::parse('2026-07-01'),
            checkout: Carbon::parse('2026-07-04'),
        );

        // Busiest night has 1 booking -> 3 - 1 = 2 available.
        $this->assertSame(2, $units);
    }

    public function test_checkout_day_frees_a_unit_half_open_interval(): void
    {
        // A booking that genuinely covers the first requested night counts.
        $units = $this->service()->availableUnits(
            totalRooms: 1,
            overlapping: $this->bookings([['2026-07-02', '2026-07-03']]),
            checkin: Carbon::parse('2026-07-02'),
            checkout: Carbon::parse('2026-07-04'),
        );

        $this->assertSame(0, $units);

        // A booking whose checkout is the requested check-in does not overlap
        // (the repository would not even return it) -> full inventory free.
        $units = $this->service()->availableUnits(
            totalRooms: 1,
            overlapping: collect(),
            checkin: Carbon::parse('2026-07-02'),
            checkout: Carbon::parse('2026-07-04'),
        );

        $this->assertSame(1, $units);
    }
}
