{{-- Booking table row. Param: $booking (with room.hotel) --}}
@php
    $nights = (int) $booking->checkin_date->diffInDays($booking->checkout_date);
    $confirmed = $booking->status === \App\Models\Booking::STATUS_CONFIRMED;
@endphp
<tr>
    <td>
        <div class="d-flex align-items-center gap-3">
            <span class="hi-avatar-sq hi-grad-indigo"><i class="bi bi-door-open"></i></span>
            <div>
                <div class="fw-semibold">{{ $booking->room?->name ?? '—' }}</div>
                <div class="text-muted small"><i class="bi bi-building me-1"></i>{{ $booking->room?->hotel?->name ?? '—' }}</div>
            </div>
        </div>
    </td>
    <td>
        <div><i class="bi bi-calendar-range text-muted me-1"></i>{{ $booking->checkin_date->format('M j, Y') }} → {{ $booking->checkout_date->format('M j, Y') }}</div>
        <div class="text-muted small">{{ $nights }} night{{ $nights === 1 ? '' : 's' }}</div>
    </td>
    <td><span class="badge text-bg-light"><i class="bi bi-people me-1"></i>{{ $booking->guests }}</span></td>
    <td><span class="badge {{ $confirmed ? 'text-bg-success' : 'text-bg-secondary' }}">{{ ucfirst($booking->status) }}</span></td>
    <td class="text-end fw-bold">{{ number_format((float) $booking->total_price, 2) }}</td>
    <td class="text-end">
        <div class="btn-group">
            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                <i class="bi bi-three-dots"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius:12px;">
                <li><a class="dropdown-item" href="{{ route('rooms.index', ['hotel' => $booking->room?->hotel_id]) }}"><i class="bi bi-building me-2"></i>View hotel rooms</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <button type="button" class="dropdown-item text-danger"
                            data-bs-toggle="modal" data-bs-target="#deleteBookingModal"
                            data-action="{{ route('bookings.destroy', $booking->id) }}"
                            data-summary="{{ $booking->room?->name }} · {{ $booking->checkin_date->format('M j') }} → {{ $booking->checkout_date->format('M j, Y') }}">
                        <i class="bi bi-trash me-2"></i>Delete
                    </button>
                </li>
            </ul>
        </div>
    </td>
</tr>
