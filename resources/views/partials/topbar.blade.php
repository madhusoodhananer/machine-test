<header class="hi-topbar">
    <button class="hi-icon-btn hi-burger" id="hiBurger" type="button" aria-label="Toggle menu">
        <i class="bi bi-list"></i>
    </button>

    <div class="d-none d-md-block">
        <div class="hi-breadcrumb"><i class="bi bi-house-door me-1"></i>@yield('breadcrumb', 'Overview')</div>
        <h1 class="hi-page-title">@yield('title', 'Dashboard')</h1>
    </div>

    <div class="hi-search d-none d-sm-block ms-auto">
        <i class="bi bi-search"></i>
        <input class="form-control" type="search" placeholder="Search hotels, rooms…" aria-label="Search"
               onkeydown="if(event.key==='Enter'){window.location='{{ route('search.index') }}?city='+encodeURIComponent(this.value);}">
    </div>

    <button class="hi-icon-btn" type="button" title="Notifications" data-bs-toggle="modal" data-bs-target="#comingSoonModal">
        <i class="bi bi-bell"></i><span class="hi-dot"></span>
    </button>

    <div class="dropdown">
        <button class="hi-icon-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Account">
            <i class="bi bi-person-circle"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="border-radius:14px;">
            <li><h6 class="dropdown-header">{{ auth()->user()?->name }}</h6></li>
            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#comingSoonModal"><i class="bi bi-person me-2"></i>Profile</a></li>
            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#comingSoonModal"><i class="bi bi-gear me-2"></i>Settings</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                </form>
            </li>
        </ul>
    </div>
</header>
