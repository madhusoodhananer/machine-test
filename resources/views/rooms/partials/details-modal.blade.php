{{-- Shared Room details modal. Populated from the triggering button's data-* attributes. --}}
<div class="modal fade" id="roomDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-3">
                    <span class="hi-avatar-sq hi-grad-indigo"><i class="bi bi-door-open"></i></span>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" data-field="name">Room</h5>
                        <div class="text-muted small"><i class="bi bi-building me-1"></i><span data-field="hotel"></span></div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="hi-stat-label">Price / night</div>
                        <div class="fs-5 fw-semibold" data-field="price"></div>
                    </div>
                    <div class="col-6">
                        <div class="hi-stat-label">Max guests</div>
                        <div class="fs-5" data-field="occupancy"></div>
                    </div>
                    <div class="col-6">
                        <div class="hi-stat-label">Inventory</div>
                        <div class="fs-5" data-field="total"></div>
                    </div>
                    <div class="col-12">
                        <div class="hi-stat-label">Identifier</div>
                        <code class="small" data-field="id"></code>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <a href="#" class="btn btn-primary" data-field="hotel-link"><i class="bi bi-building me-1"></i>Rooms at this hotel</a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        const modal = document.getElementById('roomDetailsModal');
        if (!modal) return;
        modal.addEventListener('show.bs.modal', function (event) {
            const t = event.relatedTarget;
            if (!t) return;
            const d = t.dataset;
            const set = (field, value) => { const el = modal.querySelector('[data-field="' + field + '"]'); if (el) el.textContent = value; };
            set('name', d.name || '');
            set('hotel', d.hotel || '');
            set('price', (d.price || '0') + ' / night');
            set('occupancy', (d.occupancy || '0') + ' guests');
            set('total', (d.total || '0') + ' units');
            set('id', d.id || '');
            const link = modal.querySelector('[data-field="hotel-link"]');
            if (link && d.hotelUrl) link.setAttribute('href', d.hotelUrl);
        });
    })();
</script>
@endpush
