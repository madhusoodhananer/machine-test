@if (session('status'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center border-0 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <div>{{ session('status') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center border-0 shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <div>Please fix the {{ $errors->count() }} highlighted field(s) below.</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
