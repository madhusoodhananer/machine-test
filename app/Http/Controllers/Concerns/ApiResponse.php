<?php

declare(strict_types=1);

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\JsonResponse;

/**
 * Shared JSON response helpers for API controllers.
 *
 * Successful resource payloads go through API Resources (HotelResource, …);
 * these helpers cover the plain message / no-content responses so every
 * controller emits them in one consistent shape instead of hand-rolling
 * response()->json([...]) at each call site.
 */
trait ApiResponse
{
    /**
     * A bare error payload: { "message": "..." } with the given status code.
     */
    protected function respondError(string $message, int $status): JsonResponse
    {
        return response()->json(['message' => $message], $status);
    }

    /**
     * An empty 204 No Content response.
     */
    protected function respondNoContent(): JsonResponse
    {
        return response()->json(status: 204);
    }
}
