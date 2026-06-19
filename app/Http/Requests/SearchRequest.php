<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'city' => ['required', 'string', 'max:255'],
            'checkin_date' => ['required', 'date', 'after_or_equal:today'],
            'checkout_date' => ['required', 'date', 'after:checkin_date'],
            'guests' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Normalised, typed search parameters for the service layer.
     *
     * @return array{city: string, checkin_date: string, checkout_date: string, guests: int}
     */
    public function searchParams(): array
    {
        /** @var array{city: string, checkin_date: string, checkout_date: string, guests: mixed} $validated */
        $validated = $this->validated();

        return [
            'city' => $validated['city'],
            'checkin_date' => $validated['checkin_date'],
            'checkout_date' => $validated['checkout_date'],
            'guests' => (int) $validated['guests'],
        ];
    }
}
