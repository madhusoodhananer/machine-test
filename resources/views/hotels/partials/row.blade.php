{{-- Hotel table row. Param: $hotel (with rooms_count) --}}
@php
    $palette = ['hi-grad-indigo', 'hi-grad-emerald', 'hi-grad-sky', 'hi-grad-amber', 'hi-grad-rose'];
    $chip = $palette[abs(crc32((string) $hotel->id)) % count($palette)];
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
        <a href="{{ route('rooms.index') }}" class="btn btn-sm btn-outline-secondary" title="View rooms"><i class="bi bi-door-open"></i></a>
        <button class="btn btn-sm btn-outline-secondary" title="More" data-bs-toggle="modal" data-bs-target="#comingSoonModal"><i class="bi bi-three-dots"></i></button>
    </td>
</tr>
