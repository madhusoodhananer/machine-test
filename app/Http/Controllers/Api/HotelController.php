<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHotelRequest;
use App\Http\Resources\HotelResource;
use App\Services\HotelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class HotelController extends Controller
{
    public function __construct(
        private readonly HotelService $hotels,
    ) {}

    /**
     * GET /api/hotels — paginated, filterable list.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = [
            'city' => $request->query('city'),
            'rating' => $request->query('rating'),
        ];

        $perPage = $request->integer('per_page', 15);

        return HotelResource::collection(
            $this->hotels->paginate($filters, $perPage > 0 ? $perPage : 15),
        );
    }

    /**
     * POST /api/hotels — create a hotel.
     */
    public function store(StoreHotelRequest $request): JsonResponse
    {
        $hotel = $this->hotels->create($request->validated());

        return (new HotelResource($hotel))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
