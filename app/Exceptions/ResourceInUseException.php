<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when a record cannot be deleted because other records still depend on
 * it. The message is user-facing and explains how to resolve the conflict.
 */
class ResourceInUseException extends RuntimeException
{
    public static function hotelHasRooms(int $count): self
    {
        $rooms = $count === 1 ? 'room' : 'rooms';

        return new self("This hotel still has {$count} {$rooms}. Delete its {$rooms} before deleting the hotel.");
    }

    public static function roomHasBookings(int $count): self
    {
        $bookings = $count === 1 ? 'booking' : 'bookings';

        return new self("This room has {$count} {$bookings}. Cancel its {$bookings} before deleting the room.");
    }
}
