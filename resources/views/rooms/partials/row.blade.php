{{-- Room table row. Param: $room (with hotel) --}}
@php
    $palette = ['hi-grad-indigo', 'hi-grad-emerald', 'hi-grad-sky', 'hi-grad-amber', 'hi-grad-rose'];
    $chip = $palette[abs(crc32((string) $room->id)) % count($palette)];
@endphp
<tr>
    <td>
        <div class="d-flex align-items-center gap-3">
            <span class="hi-avatar-sq {{ $chip }}"><i class="bi bi-door-open"></i></span>
            <div>
                <div class="fw-semibold">{{ $room->name }}</div>
                <div class="text-muted small"><i class="bi bi-building me-1"></i>{{ $room->hotel?->name }}</div>
            </div>
        </div>
    </td>
    <td class="fw-semibold">{{ number_format((float) $room->price_per_night, 2) }}<span class="text-muted small fw-normal"> /night</span></td>
    <td><span class="badge text-bg-light"><i class="bi bi-people me-1"></i>{{ $room->max_occupancy }} guests</span></td>
    <td><span class="badge text-bg-primary">{{ $room->total_rooms }} units</span></td>
    <td class="text-end">
        <div class="btn-group">
            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                <i class="bi bi-three-dots"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius:12px;">
                <li>
                    <button type="button" class="dropdown-item"
                            data-bs-toggle="modal" data-bs-target="#roomDetailsModal"
                            data-id="{{ $room->id }}"
                            data-name="{{ $room->name }}"
                            data-hotel="{{ $room->hotel?->name }}"
                            data-price="{{ number_format((float) $room->price_per_night, 2) }}"
                            data-occupancy="{{ $room->max_occupancy }}"
                            data-total="{{ $room->total_rooms }}"
                            data-hotel-url="{{ route('rooms.index', ['hotel' => $room->hotel_id]) }}">
                        <i class="bi bi-info-circle me-2"></i>View details
                    </button>
                </li>
                <li><a class="dropdown-item" href="{{ route('rooms.index', ['hotel' => $room->hotel_id]) }}"><i class="bi bi-building me-2"></i>Rooms at this hotel</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <button type="button" class="dropdown-item hi-room-edit"
                            data-bs-toggle="modal" data-bs-target="#editRoomModal"
                            data-action="{{ route('rooms.update', $room->id) }}"
                            data-hotel-id="{{ $room->hotel_id }}"
                            data-name="{{ $room->name }}"
                            data-price="{{ $room->price_per_night }}"
                            data-occupancy="{{ $room->max_occupancy }}"
                            data-total="{{ $room->total_rooms }}">
                        <i class="bi bi-pencil me-2"></i>Edit
                    </button>
                </li>
                <li>
                    <button type="button" class="dropdown-item text-danger"
                            data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"
                            data-action="{{ route('rooms.destroy', $room->id) }}"
                            data-title="Delete room?"
                            data-message="This removes the room. A room that still has bookings can't be deleted."
                            data-summary="{{ $room->name }} · {{ $room->hotel?->name }}">
                        <i class="bi bi-trash me-2"></i>Delete
                    </button>
                </li>
            </ul>
        </div>
    </td>
</tr>
