{{-- Design system: CSS custom properties + component styles (CDN Bootstrap, no build step). --}}
<style>
    :root {
        --hi-bg: #f3f5fb;
        --hi-primary: #4f46e5;
        --hi-primary-2: #6366f1;
        --hi-accent: #22d3ee;
        --hi-muted: #6b7280;
        --hi-border: #e7ebf3;
        --hi-shadow: 0 1px 2px rgba(16, 24, 40, .04), 0 6px 20px rgba(16, 24, 40, .06);
        --hi-sidebar-w: 264px;
    }

    * { -webkit-font-smoothing: antialiased; }

    body {
        font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        background: var(--hi-bg);
        color: #111827;
    }

    /* ---- shell ---- */
    .hi-shell { display: flex; min-height: 100vh; }
    .hi-main { flex: 1 1 auto; margin-left: var(--hi-sidebar-w); min-width: 0; display: flex; flex-direction: column; }

    /* ---- sidebar ---- */
    .hi-sidebar {
        width: var(--hi-sidebar-w); flex: 0 0 var(--hi-sidebar-w);
        background: linear-gradient(185deg, #0f172a 0%, #14213d 100%);
        color: #cbd5e1; position: fixed; inset: 0 auto 0 0; z-index: 1040;
        display: flex; flex-direction: column; padding: 18px 14px; transition: transform .25s ease;
    }
    .hi-brand { display: flex; align-items: center; gap: 11px; padding: 6px 10px 16px; color: #fff; font-weight: 700; font-size: 1.15rem; text-decoration: none; }
    .hi-logo { width: 40px; height: 40px; border-radius: 12px; display: grid; place-items: center; font-size: 1.15rem; color: #fff;
        background: linear-gradient(135deg, var(--hi-primary), var(--hi-accent)); box-shadow: 0 8px 18px rgba(79, 70, 229, .45); }
    .hi-nav-label { text-transform: uppercase; font-size: .68rem; letter-spacing: .09em; color: #5b6b85; padding: 16px 12px 6px; }
    .hi-nav a { display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 11px; color: #b6c2d6;
        text-decoration: none; font-weight: 500; font-size: .92rem; margin-bottom: 2px; transition: background .15s, color .15s; }
    .hi-nav a i { font-size: 1.12rem; width: 22px; text-align: center; }
    .hi-nav a:hover { background: rgba(255, 255, 255, .06); color: #fff; }
    .hi-nav a.active { background: linear-gradient(135deg, var(--hi-primary), var(--hi-primary-2)); color: #fff; box-shadow: 0 10px 22px rgba(79, 70, 229, .38); }
    .hi-sidebar-foot { margin-top: auto; }
    .hi-user-chip { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 13px; background: rgba(255, 255, 255, .05); }
    .hi-avatar { width: 36px; height: 36px; border-radius: 50%; display: grid; place-items: center; font-weight: 700; color: #fff; flex: 0 0 36px;
        background: linear-gradient(135deg, #f97316, #ef4444); }

    /* ---- topbar ---- */
    .hi-topbar { position: sticky; top: 0; z-index: 1030; background: rgba(255, 255, 255, .82); backdrop-filter: blur(10px);
        border-bottom: 1px solid var(--hi-border); padding: 13px 26px; display: flex; align-items: center; gap: 14px; }
    .hi-page-title { font-size: 1.4rem; font-weight: 700; margin: 0; line-height: 1.1; }
    .hi-breadcrumb { color: var(--hi-muted); font-size: .8rem; }
    .hi-search { position: relative; max-width: 340px; flex: 1 1 auto; }
    .hi-search i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--hi-muted); }
    .hi-search .form-control { border-radius: 999px; padding-left: 40px; background: #eef2f9; border: 1px solid transparent; }
    .hi-search .form-control:focus { background: #fff; border-color: var(--hi-primary); box-shadow: 0 0 0 .2rem rgba(79, 70, 229, .12); }
    .hi-icon-btn { width: 42px; height: 42px; border-radius: 12px; border: 1px solid var(--hi-border); background: #fff;
        display: grid; place-items: center; color: #334155; position: relative; cursor: pointer; }
    .hi-icon-btn:hover { background: #f8fafc; }
    .hi-dot { position: absolute; top: 9px; right: 10px; width: 8px; height: 8px; border-radius: 50%; background: #ef4444; border: 2px solid #fff; }

    /* ---- content ---- */
    .hi-content { padding: 26px; }

    /* ---- cards / components ---- */
    .card { border: 1px solid var(--hi-border); border-radius: 16px; box-shadow: var(--hi-shadow); }
    .hi-stat-icon { width: 52px; height: 52px; border-radius: 14px; display: grid; place-items: center; font-size: 1.4rem; color: #fff; }
    .hi-stat-value { font-size: 2rem; font-weight: 700; line-height: 1; }
    .hi-stat-label { color: var(--hi-muted); font-size: .76rem; text-transform: uppercase; letter-spacing: .05em; }
    .hi-trend-up { color: #16a34a; font-weight: 600; font-size: .8rem; }
    .hi-trend-down { color: #dc2626; font-weight: 600; font-size: .8rem; }

    .hi-grad-indigo { background: linear-gradient(135deg, #6366f1, #4f46e5); }
    .hi-grad-emerald { background: linear-gradient(135deg, #34d399, #059669); }
    .hi-grad-sky { background: linear-gradient(135deg, #38bdf8, #0284c7); }
    .hi-grad-amber { background: linear-gradient(135deg, #fbbf24, #d97706); }
    .hi-grad-rose { background: linear-gradient(135deg, #fb7185, #e11d48); }

    .btn { border-radius: 10px; font-weight: 500; }
    .btn-primary { background: linear-gradient(135deg, var(--hi-primary), var(--hi-primary-2)); border: none; box-shadow: 0 6px 16px rgba(79, 70, 229, .3); }
    .btn-primary:hover { filter: brightness(1.06); }

    .hi-table thead th { text-transform: uppercase; font-size: .72rem; letter-spacing: .05em; color: var(--hi-muted); border-bottom: 1px solid var(--hi-border); }
    .hi-table > :not(caption) > * > * { padding: .85rem .9rem; }
    .hi-table tbody tr { transition: background .12s; }
    .hi-table tbody tr:hover { background: #f8fafc; }
    .hi-avatar-sq { width: 38px; height: 38px; border-radius: 10px; display: grid; place-items: center; font-weight: 700; color: #fff; flex: 0 0 38px; }

    .badge { font-weight: 600; border-radius: 8px; padding: .4em .6em; }
    .form-control, .form-select { border-radius: 10px; border-color: var(--hi-border); padding: .55rem .8rem; }
    .form-control:focus, .form-select:focus { border-color: var(--hi-primary); box-shadow: 0 0 0 .2rem rgba(79, 70, 229, .12); }
    .form-label { font-weight: 500; font-size: .85rem; color: #374151; }
    .modal-content { border: none; border-radius: 18px; }

    .hi-empty { text-align: center; padding: 48px 20px; color: var(--hi-muted); }
    .hi-empty i { font-size: 2.4rem; opacity: .55; }

    /* ---- responsive sidebar ---- */
    .hi-backdrop { display: none; }
    .hi-burger { display: none; }
    @media (max-width: 991.98px) {
        .hi-sidebar { transform: translateX(-100%); }
        .hi-sidebar.show { transform: translateX(0); }
        .hi-main { margin-left: 0; }
        .hi-backdrop.show { display: block; position: fixed; inset: 0; background: rgba(15, 23, 42, .5); z-index: 1035; }
        .hi-burger { display: grid; }
    }
</style>
