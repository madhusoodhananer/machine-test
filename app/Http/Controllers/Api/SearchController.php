<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\SearchResultResource;
use App\Services\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class SearchController extends Controller
{
    public function __construct(
        private readonly SearchService $search,
    ) {}

    /**
     * GET /api/search — available hotels/rooms for a city and date range.
     */
    public function __invoke(SearchRequest $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            $result = $this->search->search($request->searchParams());

            return SearchResultResource::collection($result['results'])
                ->additional(['meta' => $result['meta']]);
        } catch (\Throwable $exception) {
            report($exception);

            return $this->respondError('Unable to run the search right now.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
