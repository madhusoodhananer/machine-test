<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Exceptions\RoomNotAvailableException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookings,
    ) {}

    /**
     * POST /api/bookings — create a confirmed booking if the room is available.
     */
    public function store(StoreBookingRequest $request): JsonResponse
    {
        /** @var array{room_id: string, checkin_date: string, checkout_date: string, guests: int} $data */
        $data = $request->validated();

        try {
            $booking = $this->bookings->create($data);

            return (new BookingResource($booking))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        } catch (RoomNotAvailableException $exception) {
            return $this->respondError($exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Throwable $exception) {
            report($exception);

            return $this->respondError('Unable to create the booking right now.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
