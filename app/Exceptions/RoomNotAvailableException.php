<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class RoomNotAvailableException extends RuntimeException
{
    public static function forRange(string $roomId, string $checkin, string $checkout): self
    {
        return new self("Room {$roomId} has no availability for {$checkin} to {$checkout}.");
    }
}
