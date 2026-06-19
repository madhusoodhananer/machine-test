<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Hotel Inventory</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    @include('partials.styles')
</head>
<body>
    <div class="hi-shell">
        @include('partials.sidebar')
        <div class="hi-backdrop" id="hiBackdrop"></div>

        <div class="hi-main">
            @include('partials.topbar')

            <main class="hi-content">
                @include('partials.flash')
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            const sidebar = document.getElementById('hiSidebar');
            const backdrop = document.getElementById('hiBackdrop');
            const burger = document.getElementById('hiBurger');
            const close = () => { sidebar?.classList.remove('show'); backdrop?.classList.remove('show'); };
            burger?.addEventListener('click', () => { sidebar?.classList.toggle('show'); backdrop?.classList.toggle('show'); });
            backdrop?.addEventListener('click', close);
        })();
    </script>
    @stack('scripts')
</body>
</html>
