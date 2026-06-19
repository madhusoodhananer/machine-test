<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHotelRequest;
use App\Services\HotelService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HotelController extends Controller
{
    public function __construct(
        private readonly HotelService $hotels,
    ) {}

    public function index(Request $request): View
    {
        $city = $request->query('city');

        $hotels = $this->hotels->paginate(['city' => $city], 10)->withQueryString();

        return view('hotels.index', [
            'hotels' => $hotels,
            'city' => $city,
        ]);
    }

    public function store(StoreHotelRequest $request): RedirectResponse
    {
        $this->hotels->create($request->validated());

        return redirect()
            ->route('hotels.index')
            ->with('status', 'Hotel created successfully.');
    }
}
