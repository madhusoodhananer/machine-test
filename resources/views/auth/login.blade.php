<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in — Hotel Inventory</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    @include('partials.styles')
    <style>
        .hi-auth { min-height: 100vh; display: flex; }
        .hi-auth-brand {
            flex: 1 1 50%;
            background: radial-gradient(1200px 600px at 20% 10%, #6366f1 0%, transparent 60%), linear-gradient(160deg, #0f172a, #312e81);
            color: #fff; padding: 56px; display: none; flex-direction: column; justify-content: space-between;
        }
        .hi-auth-form { flex: 1 1 50%; display: flex; align-items: center; justify-content: center; padding: 32px; }
        .hi-feature { display: flex; gap: 14px; align-items: flex-start; margin-bottom: 22px; }
        .hi-feature .ic { width: 42px; height: 42px; border-radius: 12px; display: grid; place-items: center; font-size: 1.2rem;
            background: rgba(255, 255, 255, .12); flex: 0 0 42px; }
        @media (min-width: 992px) { .hi-auth-brand { display: flex; } }
    </style>
</head>
<body>
    <div class="hi-auth">
        {{-- Brand panel --}}
        <div class="hi-auth-brand">
            <a href="#" class="hi-brand" style="font-size:1.3rem;">
                <span class="hi-logo"><i class="bi bi-buildings-fill"></i></span>
                <span>Hotel<span class="text-info">Hub</span></span>
            </a>
            <div class="text-center">
                <i class="bi bi-buildings-fill" style="font-size:6rem; opacity:.9;"></i>
            </div>
            <div class="opacity-50 small">© {{ date('Y') }} HotelHub. A technical demo.</div>
        </div>

        {{-- Form panel --}}
        <div class="hi-auth-form">
            <div style="width:100%; max-width:400px;">
                <div class="d-lg-none hi-brand justify-content-center mb-2" style="color:#111827;">
                    <span class="hi-logo"><i class="bi bi-buildings-fill"></i></span>
                    <span>Hotel<span class="text-info">Hub</span></span>
                </div>
                <h2 class="fw-bold mb-1">Welcome back</h2>
                <p class="text-muted mb-4">Sign in to your dashboard to continue.</p>

                <form method="POST" action="{{ route('login') }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <div class="position-relative">
                            <i class="bi bi-envelope position-absolute text-muted" style="left:14px;top:50%;transform:translateY(-50%);"></i>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                   placeholder="you@example.com" style="padding-left:40px;"
                                   class="form-control @error('email') is-invalid @enderror">
                        </div>
                        @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="position-relative">
                            <i class="bi bi-lock position-absolute text-muted" style="left:14px;top:50%;transform:translateY(-50%);"></i>
                            <input id="password" type="password" name="password" required
                                   placeholder="••••••••" style="padding-left:40px;"
                                   class="form-control @error('password') is-invalid @enderror">
                        </div>
                        @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label small" for="remember">Remember me</label>
                        </div>
                        <a href="#" class="small text-decoration-none">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2"><i class="bi bi-box-arrow-in-right me-1"></i>Sign in</button>
                </form>

                <div class="alert alert-light border mt-4 mb-0 small d-flex align-items-center">
                    <i class="bi bi-info-circle me-2"></i>
                    <div>Demo login: <code>admin@example.com</code> / <code>password</code></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
