<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->foreignId('room_id')
                ->constrained()
                ->cascadeOnDelete()
                ->index();
            $table->date('checkin_date')->index();
            $table->date('checkout_date')->index();
            $table->unsignedSmallInteger('guests');
            $table->string('status')->default('confirmed'); // confirmed | cancelled
            $table->decimal('total_price', 10, 2); // snapshot at booking time
            $table->timestamps();

            // Speeds up the date-range overlap queries used to compute availability.
            $table->index(['room_id', 'checkin_date', 'checkout_date'], 'bookings_room_date_range_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
