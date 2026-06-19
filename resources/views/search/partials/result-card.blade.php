{{-- Search result card. Param: $item (hotel, nights, rooms[]) --}}
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div class="d-flex align-items-center gap-3">
                <span class="hi-avatar-sq hi-grad-indigo"><i class="bi bi-building"></i></span>
                <div>
                    <h2 class="h5 mb-0">{{ $item['hotel']['name'] }}</h2>
                    <div class="text-muted small"><i class="bi bi-geo-alt me-1"></i>{{ $item['hotel']['city'] }}, {{ $item['hotel']['country'] }}</div>
                </div>
            </div>
            <span class="badge text-bg-warning">{{ str_repeat('★', $item['hotel']['rating']) }}</span>
        </div>

        <div class="table-responsive mt-3">
            <table class="table hi-table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Max guests</th>
                        <th>Price / night</th>
                        <th>Availability</th>
                        <th class="text-end">Total ({{ $item['nights'] }} nights)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($item['rooms'] as $room)
                        @php($units = (int) $room['available_units'])
                        <tr>
                            <td class="fw-medium">{{ $room['name'] }}</td>
                            <td><i class="bi bi-people text-muted me-1"></i>{{ $room['max_occupancy'] }}</td>
                            <td>{{ number_format((float) $room['price_per_night'], 2) }}</td>
                            <td>
                                <span class="badge {{ $units <= 2 ? 'text-bg-warning' : 'text-bg-success' }}">
                                    <i class="bi bi-{{ $units <= 2 ? 'exclamation-circle' : 'check-circle' }} me-1"></i>{{ $units }} left
                                </span>
                            </td>
                            <td class="text-end fw-bold">{{ number_format((float) $room['total_price'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
