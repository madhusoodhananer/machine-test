{{-- Reusable delete-confirmation modal. The form action, title, message and
     summary are taken from the triggering button's data-* attributes. --}}
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <form id="confirmDeleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="modal-body p-4">
                    <button type="button" class="btn-close position-absolute end-0 top-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="hi-stat-icon hi-grad-rose mx-auto mb-3" style="width:60px;height:60px;border-radius:16px;font-size:1.5rem;">
                        <i class="bi bi-trash"></i>
                    </div>
                    <h5 class="fw-bold mb-1" data-field="title">Delete?</h5>
                    <p class="text-muted mb-2" data-field="message">This action cannot be undone.</p>
                    <p class="fw-medium mb-0" data-field="summary"></p>
                    <div class="d-flex justify-content-center gap-2 mt-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i>Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        const modal = document.getElementById('confirmDeleteModal');
        if (!modal) return;
        const form = document.getElementById('confirmDeleteForm');
        modal.addEventListener('show.bs.modal', function (event) {
            const t = event.relatedTarget;
            if (!t) return;
            form.setAttribute('action', t.dataset.action || '');
            const set = (field, value, fallback) => {
                const el = modal.querySelector('[data-field="' + field + '"]');
                if (el) el.textContent = value || fallback || '';
            };
            set('title', t.dataset.title, 'Delete?');
            set('message', t.dataset.message, 'This action cannot be undone.');
            set('summary', t.dataset.summary, '');
        });
    })();
</script>
@endpush
