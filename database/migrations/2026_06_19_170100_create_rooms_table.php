<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->foreignId('hotel_id')
                ->constrained()
                ->cascadeOnDelete()
                ->index();
            $table->string('name'); // e.g. "Deluxe King"
            $table->decimal('price_per_night', 10, 2);
            $table->unsignedSmallInteger('max_occupancy');
            // Total physical inventory of this room type (replaces the static `available_rooms`).
            // Date-range availability is derived from the bookings table, not stored here.
            $table->unsignedSmallInteger('total_rooms');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
