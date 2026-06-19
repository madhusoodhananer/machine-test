{{-- Hotel table row. Param: $hotel (with rooms_count) --}}
@php
    $palette = ['hi-grad-indigo', 'hi-grad-emerald', 'hi-grad-sky', 'hi-grad-amber', 'hi-grad-rose'];
    $chip = $palette[abs(crc32((string) $hotel->id)) % count($palette)];
    $roomsUrl = route('rooms.index', ['hotel' => $hotel->id]);
@endphp
<tr>
    <td>
        <div class="d-flex align-items-center gap-3">
            <span class="hi-avatar-sq {{ $chip }}">{{ strtoupper(mb_substr($hotel->name, 0, 1)) }}</span>
            <div>
                <div class="fw-semibold">{{ $hotel->name }}</div>
                <div class="text-muted small">ID {{ \Illuminate\Support\Str::limit($hotel->id, 8, '…') }}</div>
            </div>
        </div>
    </td>
    <td><i class="bi bi-geo-alt text-muted me-1"></i>{{ $hotel->city }}, {{ $hotel->country }}</td>
    <td>
        <span class="text-warning">{{ str_repeat('★', $hotel->rating) }}</span><span class="text-secondary opacity-50">{{ str_repeat('★', 5 - $hotel->rating) }}</span>
    </td>
    <td><span class="badge text-bg-light"><i class="bi bi-door-open me-1"></i>{{ $hotel->rooms_count }} rooms</span></td>
    <td class="text-end">
        <a href="{{ $roomsUrl }}" class="btn btn-sm btn-outline-secondary" title="View rooms"><i class="bi bi-door-open"></i></a>
        <div class="btn-group">
            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="More actions">
                <i class="bi bi-three-dots"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius:12px;">
                <li>
                    <button type="button" class="dropdown-item hi-hotel-details"
                            data-bs-toggle="modal" data-bs-target="#hotelDetailsModal"
                            data-id="{{ $hotel->id }}"
                            data-name="{{ $hotel->name }}"
                            data-city="{{ $hotel->city }}"
                            data-country="{{ $hotel->country }}"
                            data-rating="{{ $hotel->rating }}"
                            data-rooms="{{ $hotel->rooms_count }}"
                            data-rooms-url="{{ $roomsUrl }}">
                        <i class="bi bi-info-circle me-2"></i>View details
                    </button>
                </li>
                <li><a class="dropdown-item" href="{{ $roomsUrl }}"><i class="bi bi-door-open me-2"></i>View rooms</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <button type="button" class="dropdown-item hi-hotel-edit"
                            data-bs-toggle="modal" data-bs-target="#editHotelModal"
                            data-action="{{ route('hotels.update', $hotel->id) }}"
                            data-name="{{ $hotel->name }}"
                            data-city="{{ $hotel->city }}"
                            data-country="{{ $hotel->country }}"
                            data-rating="{{ $hotel->rating }}">
                        <i class="bi bi-pencil me-2"></i>Edit
                    </button>
                </li>
            </ul>
        </div>
    </td>
</tr>
