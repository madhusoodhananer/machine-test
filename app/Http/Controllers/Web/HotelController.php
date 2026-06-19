<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHotelRequest;
use App\Models\Hotel;
use App\Services\HotelService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class HotelController extends Controller
{
    /** Hotels shown per page in the list. */
    private const PER_PAGE = 8;

    public function __construct(
        private readonly HotelService $hotels,
    ) {}

    public function index(Request $request): View
    {
        $city = $request->query('city');

        try {
            $hotels = $this->hotels->paginate(['city' => $city], self::PER_PAGE)->withQueryString();

            return view('hotels.index', [
                'hotels' => $hotels,
                'city' => $city,
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            session()->now('error', 'We could not load the hotels list.');

            return view('hotels.index', [
                'hotels' => new LengthAwarePaginator([], 0, self::PER_PAGE),
                'city' => $city,
            ]);
        }
    }

    public function store(StoreHotelRequest $request): RedirectResponse
    {
        try {
            $this->hotels->create($request->validated());

            return redirect()
                ->route('hotels.index')
                ->with('status', 'Hotel created successfully.');
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'We could not create the hotel. Please try again.');
        }
    }

    public function update(StoreHotelRequest $request, Hotel $hotel): RedirectResponse
    {
        try {
            $this->hotels->update($hotel, $request->validated());

            return redirect()
                ->route('hotels.index')
                ->with('status', 'Hotel updated successfully.');
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'We could not update the hotel. Please try again.');
        }
    }
}
