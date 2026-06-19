{{-- Reusable empty-state placeholder. Params: $icon, $title, $text --}}
<div class="hi-empty">
    <i class="bi {{ $icon ?? 'bi-inbox' }} d-block mb-2"></i>
    <div class="fw-semibold text-body">{{ $title ?? 'Nothing here yet' }}</div>
    <div class="small">{{ $text ?? '' }}</div>
</div>
