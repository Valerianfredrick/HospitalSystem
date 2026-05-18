<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'MediCore HMS')) — {{ config('app.name') }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">

    <style>
        /* ── Reset ───────────────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        /* ── Design tokens ───────────────────────────────────────────── */
        :root {
            --primary:      #6d28d9;
            --primary-lt:   #ede9fe;
            --violet-lt:    #7c3aed;
            --teal:         #0694a2;
            --teal-lt:      #d5f5f6;
            --red:          #ef4444;
            --red-lt:       #fee2e2;
            --amber:        #f59e0b;
            --amber-lt:     #fef3c7;
            --green:        #10b981;
            --green-lt:     #d1fae5;
            --blue:         #3b82f6;
            --blue-lt:      #dbeafe;
            --gray:         #6b7280;
            --gray-lt:      #f3f4f6;

            --grad:         linear-gradient(135deg, #6d28d9 0%, #0694a2 100%);
            --grad-soft:    linear-gradient(135deg, #f5f3ff 0%, #edfafa 100%);

            --bg:           #f5f7fa;
            --surface:      #ffffff;
            --border:       #e5e7eb;
            --text:         #111827;
            --text-muted:   #6b7280;

            --sidebar-w:    240px;
            --topbar-h:     64px;
            --radius:       14px;
            --shadow:       0 4px 20px rgba(0,0,0,.07);
        }

        html, body {
            height: 100%;
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            font-size: 14px;
        }

        /* ── App shell ───────────────────────────────────────────────── */
        .app-shell  { display: flex; min-height: 100vh; }
        .main-wrap  { margin-left: var(--sidebar-w); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
        .page-content { flex: 1; padding: 28px 28px 48px; }

        /* ── Sidebar ─────────────────────────────────────────────────── */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0;
            height: 100vh;
            z-index: 200;
            overflow-y: auto;
        }
        .sidebar-brand { padding: 20px 20px 16px; border-bottom: 1px solid var(--border); }
        .sidebar-brand h1 {
            font-family: 'Syne', sans-serif;
            font-size: 20px; font-weight: 800;
            background: var(--grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .sidebar-brand p { font-size: 11px; color: var(--text-muted); margin-top: 2px; }

        .sidebar-nav  { flex: 1; padding: 16px 12px; }
        .nav-section  { font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .08em; padding: 12px 8px 6px; }
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 10px;
            color: var(--text-muted); text-decoration: none;
            font-size: 13px; font-weight: 500;
            transition: all .18s; margin-bottom: 2px;
            background: none; border: none; width: 100%; cursor: pointer; text-align: left;
        }
        .nav-item i   { width: 18px; text-align: center; font-size: 14px; }
        .nav-item:hover, .nav-item.active { background: var(--primary-lt); color: var(--primary); }
        .nav-item.active { font-weight: 700; }
        .nav-item.danger:hover { background: var(--red-lt); color: var(--red); }

        /* ── Topbar ──────────────────────────────────────────────────── */
        .topbar {
            height: var(--topbar-h);
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center;
            padding: 0 24px; gap: 16px;
            position: sticky; top: 0; z-index: 100;
        }
        .topbar-title h2 { font-size: 17px; font-weight: 700; }
        .topbar-title p  { font-size: 12px; color: var(--text-muted); }
        .topbar-right    { margin-left: auto; display: flex; align-items: center; gap: 12px; }
        .topbar-avatar   {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--grad);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; color: #fff; font-size: 13px;
        }

        /* ── Grid helpers ────────────────────────────────────────────── */
        .grid   { display: grid; gap: 20px; }
        .grid-2 { grid-template-columns: repeat(2, 1fr); }
        .grid-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-4 { grid-template-columns: repeat(4, 1fr); }
        @media(max-width:1100px) { .grid-4 { grid-template-columns: repeat(2,1fr); } }
        @media(max-width:720px)  { .grid-4,.grid-3,.grid-2 { grid-template-columns: 1fr; } }

        /* ── Card ────────────────────────────────────────────────────── */
        .card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden; }
        .card-header { padding: 18px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
        .card-body   { padding: 20px; }

        /* ── Stat cards ──────────────────────────────────────────────── */
        .stat-card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius); box-shadow: var(--shadow);
            padding: 20px; display: flex; flex-direction: column; gap: 8px;
            position: relative; overflow: hidden;
        }
        .stat-card::before {
            content:''; position:absolute; top:-20px; right:-20px;
            width:80px; height:80px; border-radius:50%; opacity:.08;
        }
        .stat-card.teal::before   { background: var(--teal); }
        .stat-card.violet::before { background: var(--primary); }
        .stat-card.amber::before  { background: var(--amber); }
        .stat-card.green::before  { background: var(--green); }
        .stat-card.red::before    { background: var(--red); }
        .stat-card.blue::before   { background: var(--blue); }

        .stat-icon { width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:16px; }
        .stat-icon.teal   { background:var(--teal-lt);    color:var(--teal); }
        .stat-icon.violet { background:var(--primary-lt); color:var(--primary); }
        .stat-icon.amber  { background:var(--amber-lt);   color:var(--amber); }
        .stat-icon.green  { background:var(--green-lt);   color:var(--green); }
        .stat-icon.red    { background:var(--red-lt);     color:var(--red); }
        .stat-icon.blue   { background:var(--blue-lt);    color:var(--blue); }

        .stat-value { font-family:'Syne',sans-serif; font-size:28px; font-weight:800; color:var(--text); }
        .stat-label { font-size:12px; color:var(--text-muted); font-weight:500; }

        /* ── Table ───────────────────────────────────────────────────── */
        .table-wrap { overflow-x: auto; }
        table   { width:100%; border-collapse:collapse; }
        thead th {
            background: var(--grad-soft); color: var(--primary);
            font-size:11px; text-transform:uppercase; letter-spacing:.05em;
            padding:12px 16px; text-align:left; white-space:nowrap; font-weight:700;
        }
        tbody td { padding:13px 16px; border-bottom:1px solid var(--border); vertical-align:middle; }
        tbody tr:last-child td { border-bottom:none; }
        tbody tr:hover td { background:#faf9ff; }

        /* ── Buttons ─────────────────────────────────────────────────── */
        .btn {
            display:inline-flex; align-items:center; gap:7px;
            padding:9px 18px; border-radius:10px;
            font-size:13px; font-weight:600; font-family:'Inter',sans-serif;
            cursor:pointer; border:none; text-decoration:none;
            transition:all .18s; white-space:nowrap;
        }
        .btn-primary { background:var(--grad); color:#fff; }
        .btn-primary:hover { opacity:.88; }
        .btn-ghost  { background:var(--gray-lt); color:var(--gray); }
        .btn-ghost:hover { background:var(--primary-lt); color:var(--primary); }
        .btn-danger { background:var(--red-lt); color:var(--red); }
        .btn-danger:hover { background:var(--red); color:#fff; }
        .btn-sm { padding:6px 12px; font-size:12px; border-radius:8px; }

        /* ── Pills ───────────────────────────────────────────────────── */
        .pill { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; white-space:nowrap; }
        .pill.green  { background:var(--green-lt);   color:#065f46; }
        .pill.red    { background:var(--red-lt);     color:#991b1b; }
        .pill.amber  { background:var(--amber-lt);   color:#92400e; }
        .pill.teal   { background:var(--teal-lt);    color:#065f46; }
        .pill.violet { background:var(--primary-lt); color:var(--primary); }
        .pill.blue   { background:var(--blue-lt);    color:#1e40af; }
        .pill.gray   { background:var(--gray-lt);    color:#374151; }

        /* ── Forms ───────────────────────────────────────────────────── */
        .form-control {
            width:100%; padding:9px 14px;
            border:1px solid var(--border); border-radius:10px;
            font-size:13px; font-family:'Inter',sans-serif;
            background:var(--surface); color:var(--text);
            outline:none; transition:border-color .18s, box-shadow .18s;
        }
        .form-control:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(109,40,217,.12); }
        select.form-control { cursor:pointer; }

        .form-group { margin-bottom:18px; }
        .form-group label { display:block; font-size:12px; font-weight:600; color:var(--text-muted); margin-bottom:6px; text-transform:uppercase; letter-spacing:.04em; }
        .form-row { display:grid; gap:16px; }
        .form-row.cols-2 { grid-template-columns:1fr 1fr; }
        .form-row.cols-3 { grid-template-columns:1fr 1fr 1fr; }
        @media(max-width:640px) { .form-row.cols-2,.form-row.cols-3 { grid-template-columns:1fr; } }

        /* ── Search wrap ─────────────────────────────────────────────── */
        .search-wrap { position:relative; }
        .search-wrap > i { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:13px; pointer-events:none; }
        .search-wrap .form-control { padding-left:36px; }

        /* ── Alerts ──────────────────────────────────────────────────── */
        .alert { padding:14px 18px; border-radius:10px; font-size:13px; font-weight:500; margin-bottom:20px; display:flex; align-items:center; gap:10px; }
        .alert-success { background:var(--green-lt); color:#065f46; border:1px solid #6ee7b7; }
        .alert-error   { background:var(--red-lt);   color:#991b1b; border:1px solid #fca5a5; }
        .alert-warning { background:var(--amber-lt);  color:#92400e; border:1px solid #fcd34d; }
        .alert-info    { background:var(--blue-lt);   color:#1e40af; border:1px solid #93c5fd; }

        /* ── Scrollbar ───────────────────────────────────────────────── */
        ::-webkit-scrollbar { width:6px; height:6px; }
        ::-webkit-scrollbar-track { background:transparent; }
        ::-webkit-scrollbar-thumb { background:var(--border); border-radius:99px; }
    </style>

    @stack('styles')
</head>
<body>
<div class="app-shell">

    {{-- Sidebar --}}
    @include('partials.sidebar')

    {{-- Main --}}
    <div class="main-wrap">

        {{-- Topbar --}}
        <div class="topbar">
            <div class="topbar-title">
                <h2>@yield('page-title', 'Dashboard')</h2>
                <p>@yield('page-subtitle', '')</p>
            </div>
            <div class="topbar-right">
                {{-- Flash messages --}}
                @if(session('success'))
                    <div class="alert alert-success" style="margin:0;padding:8px 14px;">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-error" style="margin:0;padding:8px 14px;">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    </div>
                @endif

                <div style="text-align:right;line-height:1.3;">
                    <div style="font-size:13px;font-weight:600;">{{ auth()->user()->name ?? 'User' }}</div>
                    <div style="font-size:11px;color:var(--text-muted);">{{ ucfirst(auth()->user()->role ?? 'staff') }}</div>
                </div>
                <div class="topbar-avatar">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="btn btn-ghost btn-sm" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>

        {{-- Page content --}}
        <div class="page-content">

            {{-- Validation errors --}}
            @if($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            @yield('content')
        </div>

    </div>
</div>

@stack('scripts')
</body>
</html>
