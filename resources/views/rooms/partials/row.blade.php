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
        <button class="btn btn-sm btn-outline-secondary" title="More" data-bs-toggle="modal" data-bs-target="#comingSoonModal"><i class="bi bi-three-dots"></i></button>
    </td>
</tr>
