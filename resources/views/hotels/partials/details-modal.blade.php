{{-- Shared Hotel details modal. Populated from the triggering button's data-* attributes. --}}
<div class="modal fade" id="hotelDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-3">
                    <span class="hi-avatar-sq hi-grad-indigo" data-field="initial">H</span>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" data-field="name">Hotel</h5>
                        <div class="text-muted small"><i class="bi bi-geo-alt me-1"></i><span data-field="location"></span></div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="hi-stat-label">Rating</div>
                        <div class="fs-5" data-field="rating"></div>
                    </div>
                    <div class="col-6">
                        <div class="hi-stat-label">Rooms</div>
                        <div class="fs-5 fw-semibold" data-field="rooms"></div>
                    </div>
                    <div class="col-12">
                        <div class="hi-stat-label">Identifier</div>
                        <code class="small" data-field="id"></code>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <a href="#" class="btn btn-primary" data-field="rooms-link"><i class="bi bi-door-open me-1"></i>View rooms</a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        const modal = document.getElementById('hotelDetailsModal');
        if (!modal) return;
        modal.addEventListener('show.bs.modal', function (event) {
            const t = event.relatedTarget;
            if (!t) return;
            const d = t.dataset;
            const rating = parseInt(d.rating || '0', 10);
            const set = (field, value) => { const el = modal.querySelector('[data-field="' + field + '"]'); if (el) el.textContent = value; };
            set('initial', (d.name || 'H').charAt(0).toUpperCase());
            set('name', d.name || '');
            set('location', [d.city, d.country].filter(Boolean).join(', '));
            set('rooms', (d.rooms || '0') + ' rooms');
            set('id', d.id || '');
            const ratingEl = modal.querySelector('[data-field="rating"]');
            if (ratingEl) {
                ratingEl.innerHTML = '<span class="text-warning">' + '★'.repeat(rating) +
                    '</span><span class="text-secondary opacity-50">' + '★'.repeat(5 - rating) + '</span>';
            }
            const link = modal.querySelector('[data-field="rooms-link"]');
            if (link && d.roomsUrl) link.setAttribute('href', d.roomsUrl);
        });
    })();
</script>
@endpush
