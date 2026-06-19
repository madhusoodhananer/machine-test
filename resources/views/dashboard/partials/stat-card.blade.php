{{-- Stat card. Params: $label, $value, $icon, $grad, $trend, $up (bool), $suffix (optional) --}}
@php($up = $up ?? true)
<div class="col-sm-6 col-xl-3">
    <div class="card h-100">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div class="hi-stat-icon {{ $grad }}"><i class="bi {{ $icon }}"></i></div>
                <span class="{{ $up ? 'hi-trend-up' : 'hi-trend-down' }}">
                    <i class="bi {{ $up ? 'bi-arrow-up-right' : 'bi-arrow-down-right' }}"></i>
                </span>
            </div>
            <div class="hi-stat-value mt-3">
                {{ $value }}@isset($suffix)<span class="fs-5 text-warning">{{ $suffix }}</span>@endisset
            </div>
            <div class="hi-stat-label mt-1">{{ $label }}</div>
            <div class="mt-2"><span class="{{ $up ? 'hi-trend-up' : 'hi-trend-down' }}">{{ $trend }}</span></div>
        </div>
    </div>
</div>
