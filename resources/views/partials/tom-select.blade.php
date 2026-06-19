{{-- Loads Tom Select (Bootstrap 5 theme) and upgrades every
     <select data-tomselect> on the page into a styled, searchable dropdown.
     Include once per page that has such selects. --}}
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    .ts-control { border-radius: 10px; border-color: var(--hi-border); padding: .5rem .8rem; }
    .ts-control.focus { border-color: var(--hi-primary); box-shadow: 0 0 0 .2rem rgba(79, 70, 229, .12); }
    .ts-dropdown { border-radius: 12px; border-color: var(--hi-border); box-shadow: var(--hi-shadow); overflow: hidden; }
    .ts-dropdown .active { background: rgba(79, 70, 229, .1); color: var(--hi-primary); }
    .ts-dropdown .ts-dropdown-content { max-height: 260px; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/js/tom-select.complete.min.js"></script>
<script>
    (function () {
        if (!window.TomSelect) return;
        document.querySelectorAll('select[data-tomselect]').forEach(function (el) {
            if (el.tomselect) return;
            new TomSelect(el, {
                create: false,
                allowEmptyOption: true,
                searchField: ['text'],
                maxOptions: null,
            });
        });
    })();
</script>
@endpush
