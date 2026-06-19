<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Exceptions\Contracts\HttpStatusProvider;
use Illuminate\Http\Response;
use RuntimeException;

class RoomNotAvailableException extends RuntimeException implements HttpStatusProvider
{
    public static function forRange(string $roomId, string $checkin, string $checkout): self
    {
        return new self("Room {$roomId} has no availability for {$checkin} to {$checkout}.");
    }

    /** Unprocessable: the request was valid but the room cannot be booked. */
    public function httpStatusCode(): int
    {
        return Response::HTTP_UNPROCESSABLE_ENTITY;
    }
}
