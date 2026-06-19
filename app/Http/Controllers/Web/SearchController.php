<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchRequest;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __construct(
        private readonly SearchService $search,
    ) {}

    /**
     * Render the search form and, when submitted, the available hotels/rooms.
     * Uses the same SearchService and validation rules as the API.
     */
    public function index(Request $request): View
    {
        $results = null;
        $meta = null;

        try {
            if ($request->hasAny(['city', 'checkin_date', 'checkout_date', 'guests'])) {
                $validated = $request->validate((new SearchRequest)->rules());

                $payload = $this->search->search([
                    'city' => $validated['city'],
                    'checkin_date' => $validated['checkin_date'],
                    'checkout_date' => $validated['checkout_date'],
                    'guests' => (int) $validated['guests'],
                ]);

                $results = $payload['results'];
                $meta = $payload['meta'];
            }
        } catch (ValidationException $exception) {
            // Let validation errors surface inline on the form.
            throw $exception;
        } catch (\Throwable $exception) {
            report($exception);

            session()->now('error', 'We could not complete the search. Please try again.');
        }

        return view('search.index', [
            'results' => $results,
            'meta' => $meta,
            'filters' => $request->only(['city', 'checkin_date', 'checkout_date', 'guests']),
        ]);
    }
}
