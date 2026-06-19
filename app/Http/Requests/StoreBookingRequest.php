<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Room;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
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
            'room_id' => ['required', 'uuid', 'exists:rooms,id'],
            'checkin_date' => ['required', 'date', 'after_or_equal:today'],
            'checkout_date' => ['required', 'date', 'after:checkin_date'],
            'guests' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Reject bookings whose guest count exceeds the room's capacity.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $roomId = $this->input('room_id');
            $guests = (int) $this->input('guests');

            if (! is_string($roomId) || $guests < 1) {
                return;
            }

            $room = Room::query()->find($roomId);

            if ($room !== null && $guests > $room->max_occupancy) {
                $validator->errors()->add('guests', "This room allows a maximum of {$room->max_occupancy} guests.");
            }
        });
    }
}
