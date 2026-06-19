{{-- Shared delete-confirmation modal. The form action + summary come from the
     triggering row's data-* attributes. --}}
<div class="modal fade" id="deleteBookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <form id="deleteBookingForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="modal-body p-4">
                    <button type="button" class="btn-close position-absolute end-0 top-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="hi-stat-icon hi-grad-rose mx-auto mb-3" style="width:60px;height:60px;border-radius:16px;font-size:1.5rem;">
                        <i class="bi bi-trash"></i>
                    </div>
                    <h5 class="fw-bold mb-1">Delete booking?</h5>
                    <p class="text-muted mb-2">This removes the booking and frees the room for those dates.</p>
                    <p class="fw-medium mb-0" data-field="summary"></p>
                    <div class="d-flex justify-content-center gap-2 mt-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i>Delete booking</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        const modal = document.getElementById('deleteBookingModal');
        if (!modal) return;
        const form = document.getElementById('deleteBookingForm');
        modal.addEventListener('show.bs.modal', function (event) {
            const t = event.relatedTarget;
            if (!t) return;
            form.setAttribute('action', t.dataset.action || '');
            const summary = modal.querySelector('[data-field="summary"]');
            if (summary) summary.textContent = t.dataset.summary || '';
        });
    })();
</script>
@endpush
