<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Resources\RoomResource;
use App\Services\RoomService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class RoomController extends Controller
{
    public function __construct(
        private readonly RoomService $rooms,
    ) {}

    /**
     * POST /api/rooms — create a room type for a hotel.
     */
    public function store(StoreRoomRequest $request): JsonResponse
    {
        try {
            $room = $this->rooms->create($request->validated());

            return (new RoomResource($room->load('hotel')))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        } catch (\Throwable $exception) {
            report($exception);

            return $this->respondError('Unable to create the room right now.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
