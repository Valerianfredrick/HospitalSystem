<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HMS') — MediCore Hospital</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --violet:    #6d28d9;
            --violet-lt: #8b5cf6;
            --violet-dk: #4c1d95;
            --teal:      #0694a2;
            --teal-lt:   #16bdca;
            --teal-dk:   #047481;
            --bg:        #0f0f1a;
            --surface:   #16162a;
            --surface2:  #1e1e38;
            --border:    rgba(109,40,217,.25);
            --text:      #e2e8f0;
            --text-muted:#94a3b8;
            --grad: linear-gradient(135deg, var(--violet) 0%, var(--teal) 100%);
            --grad-soft: linear-gradient(135deg, rgba(109,40,217,.15) 0%, rgba(6,148,162,.15) 100%);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
        }

        /* ── Sidebar ── */
        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            transition: width .3s ease;
        }

        .sidebar-logo {
            padding: 24px 20px 20px;
            border-bottom: 1px solid var(--border);
        }

        .sidebar-logo .logo-mark {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-icon {
            width: 40px; height: 40px;
            background: var(--grad);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            box-shadow: 0 0 20px rgba(109,40,217,.4);
        }

        .logo-text {
            font-family: 'Syne', sans-serif;
            font-size: 20px;
            font-weight: 800;
            background: var(--grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .logo-sub {
            font-size: 10px;
            color: var(--text-muted);
            letter-spacing: .1em;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .sidebar-nav {
            flex: 1;
            padding: 16px 12px;
            overflow-y: auto;
        }

        .nav-section-label {
            font-size: 10px;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--text-muted);
            padding: 12px 10px 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 12px;
            border-radius: 10px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all .2s ease;
            margin-bottom: 2px;
        }

        .nav-item i { width: 18px; text-align: center; font-size: 15px; }

        .nav-item:hover {
            background: var(--grad-soft);
            color: var(--text);
        }

        .nav-item.active {
            background: var(--grad);
            color: #fff;
            box-shadow: 0 4px 15px rgba(109,40,217,.35);
        }

        .nav-item .badge {
            margin-left: auto;
            background: rgba(255,255,255,.15);
            color: #fff;
            font-size: 10px;
            padding: 2px 7px;
            border-radius: 20px;
        }

        .nav-item.active .badge { background: rgba(255,255,255,.25); }

        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid var(--border);
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            background: var(--surface2);
        }

        .user-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: var(--grad);
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 700; color: #fff;
            flex-shrink: 0;
        }

        .user-name { font-size: 13px; font-weight: 600; }
        .user-role { font-size: 11px; color: var(--text-muted); }

        .logout-btn {
            margin-left: auto;
            color: var(--text-muted);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
            transition: color .2s;
        }
        .logout-btn:hover { color: #f87171; }

        /* ── Main ── */
        .main {
            margin-left: 260px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ── Topbar ── */
        .topbar {
            height: 64px;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 28px;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .page-title {
            font-family: 'Syne', sans-serif;
            font-size: 18px;
            font-weight: 700;
        }

        .page-subtitle {
            font-size: 12px;
            color: var(--text-muted);
        }

        .topbar-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-btn {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: var(--surface2);
            border: 1px solid var(--border);
            color: var(--text-muted);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            font-size: 14px;
            transition: all .2s;
            position: relative;
            text-decoration: none;
        }

        .topbar-btn:hover { color: var(--text); border-color: var(--violet); }

        .notif-dot {
            position: absolute;
            top: 6px; right: 6px;
            width: 8px; height: 8px;
            border-radius: 50%;
            background: var(--teal-lt);
            border: 2px solid var(--surface);
        }

        /* ── Content ── */
        .content { padding: 28px; flex: 1; }

        /* ── Cards ── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
        }

        .card-header {
            padding: 18px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-title {
            font-family: 'Syne', sans-serif;
            font-size: 15px;
            font-weight: 700;
        }

        .card-body { padding: 20px; }

        /* ── Stat Cards ── */
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            position: relative;
            overflow: hidden;
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
        }

        .stat-card.violet::before { background: linear-gradient(90deg, var(--violet), var(--violet-lt)); }
        .stat-card.teal::before   { background: linear-gradient(90deg, var(--teal), var(--teal-lt)); }
        .stat-card.green::before  { background: linear-gradient(90deg, #059669, #34d399); }
        .stat-card.amber::before  { background: linear-gradient(90deg, #d97706, #fbbf24); }

        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 30px rgba(0,0,0,.3); }

        .stat-icon {
            width: 44px; height: 44px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            margin-bottom: 16px;
        }

        .stat-icon.violet { background: rgba(109,40,217,.2); color: var(--violet-lt); }
        .stat-icon.teal   { background: rgba(6,148,162,.2);  color: var(--teal-lt); }
        .stat-icon.green  { background: rgba(5,150,105,.2);  color: #34d399; }
        .stat-icon.amber  { background: rgba(217,119,6,.2);  color: #fbbf24; }

        .stat-value {
            font-family: 'Syne', sans-serif;
            font-size: 30px;
            font-weight: 800;
            line-height: 1;
        }

        .stat-label { font-size: 13px; color: var(--text-muted); margin-top: 4px; }

        .stat-change {
            font-size: 12px;
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .stat-change.up   { color: #34d399; }
        .stat-change.down { color: #f87171; }

        /* ── Buttons ── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all .2s ease;
            text-decoration: none;
            font-family: 'DM Sans', sans-serif;
        }

        .btn-primary {
            background: var(--grad);
            color: #fff;
            box-shadow: 0 4px 15px rgba(109,40,217,.35);
        }

        .btn-primary:hover { opacity: .88; transform: translateY(-1px); }

        .btn-ghost {
            background: var(--surface2);
            color: var(--text-muted);
            border: 1px solid var(--border);
        }

        .btn-ghost:hover { color: var(--text); border-color: var(--violet); }

        .btn-danger {
            background: rgba(239,68,68,.15);
            color: #f87171;
            border: 1px solid rgba(239,68,68,.3);
        }

        .btn-danger:hover { background: rgba(239,68,68,.25); }

        .btn-sm { padding: 6px 12px; font-size: 12px; border-radius: 8px; }

        /* ── Table ── */
        .table-wrap { overflow-x: auto; }

        table { width: 100%; border-collapse: collapse; }

        thead th {
            padding: 12px 16px;
            text-align: left;
            font-size: 11px;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border);
        }

        tbody tr {
            border-bottom: 1px solid rgba(109,40,217,.1);
            transition: background .15s;
        }

        tbody tr:hover { background: var(--grad-soft); }
        tbody tr:last-child { border-bottom: none; }

        tbody td {
            padding: 14px 16px;
            font-size: 13px;
            vertical-align: middle;
        }

        /* ── Badge / Pill ── */
        .pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .pill.blue     { background: rgba(59,130,246,.15); color: #60a5fa; }
        .pill.green    { background: rgba(5,150,105,.15);  color: #34d399; }
        .pill.red      { background: rgba(239,68,68,.15);  color: #f87171; }
        .pill.amber    { background: rgba(245,158,11,.15); color: #fbbf24; }
        .pill.teal     { background: rgba(6,148,162,.15);  color: var(--teal-lt); }
        .pill.violet   { background: rgba(109,40,217,.15); color: var(--violet-lt); }
        .pill.gray     { background: rgba(148,163,184,.1); color: var(--text-muted); }

        /* ── Form ── */
        .form-group { margin-bottom: 18px; }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            letter-spacing: .05em;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 11px 14px;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            transition: border-color .2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--violet-lt);
            box-shadow: 0 0 0 3px rgba(109,40,217,.15);
        }

        select.form-control option { background: var(--surface2); }

        textarea.form-control { resize: vertical; min-height: 100px; }

        /* ── Grid helpers ── */
        .grid { display: grid; gap: 20px; }
        .grid-4 { grid-template-columns: repeat(4, 1fr); }
        .grid-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-2 { grid-template-columns: repeat(2, 1fr); }

        @media (max-width:1200px) {
            .grid-4 { grid-template-columns: repeat(2,1fr); }
        }
        @media (max-width:900px) {
            .sidebar { width: 70px; }
            .sidebar .logo-text, .sidebar .logo-sub,
            .sidebar .nav-item span, .sidebar .nav-section-label,
            .sidebar .user-name, .sidebar .user-role { display: none; }
            .main { margin-left: 70px; }
            .grid-4, .grid-3, .grid-2 { grid-template-columns: 1fr; }
        }

        /* ── Alert ── */
        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success { background: rgba(5,150,105,.15); border: 1px solid rgba(5,150,105,.3); color: #34d399; }
        .alert-error   { background: rgba(239,68,68,.15);  border: 1px solid rgba(239,68,68,.3);  color: #f87171; }

        /* ── Glow orbs (decorative) ── */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
            opacity: .35;
        }

        .orb-1 { width: 300px; height: 300px; background: var(--violet); top: -80px; right: 10%; }
        .orb-2 { width: 250px; height: 250px; background: var(--teal);   bottom: 5%;  left:  5%; }

        /* ── Search ── */
        .search-wrap { position: relative; }
        .search-wrap i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 13px; }
        .search-wrap input { padding-left: 36px; }

        /* Page fade-in */
        .content { animation: fadeUp .4s ease both; }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>

    @stack('styles')
</head>
<body>

<div class="orb orb-1"></div>
<div class="orb orb-2"></div>

<!-- ── Sidebar ── -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-mark">
            <div class="logo-icon">🏥</div>
            <div>
                <div class="logo-text">MediCore</div>
                <div class="logo-sub">Hospital System</div>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Main</div>

        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-th-large"></i>
            <span>Dashboard</span>
        </a>

        <div class="nav-section-label">Patients</div>

        <a href="{{ route('patients.index') }}" class="nav-item {{ request()->routeIs('patients.index') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>All Patients</span>
        </a>

        <a href="{{ route('patients.admission') }}" class="nav-item {{ request()->routeIs('patients.admission') ? 'active' : '' }}">
            <i class="fas fa-bed"></i>
            <span>Admissions</span>
        </a>

        <a href="{{ route('patients.discharge') }}" class="nav-item {{ request()->routeIs('patients.discharge') ? 'active' : '' }}">
            <i class="fas fa-sign-out-alt"></i>
            <span>Discharges</span>
        </a>

        <a href="{{ route('patients.create') }}" class="nav-item {{ request()->routeIs('patients.create') ? 'active' : '' }}">
            <i class="fas fa-user-plus"></i>
            <span>Admit Patient</span>
        </a>

        <div class="nav-section-label">Pharmacy</div>

        <a href="{{ route('pharmacy.index') }}" class="nav-item {{ request()->routeIs('pharmacy.*') ? 'active' : '' }}">
            <i class="fas fa-pills"></i>
            <span>Stock &amp; Meds</span>
        </a>

        @if(auth()->user()?->role === 'admin')
        <div class="nav-section-label">Admin</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.*') ? 'active' : '' }}">
            <i class="fas fa-shield-alt"></i>
            <span>Admin Panel</span>
        </a>
        @endif
    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()?->name ?? 'D', 0, 1)) }}</div>
            <div>
                <div class="user-name">{{ auth()->user()?->name ?? 'Doctor' }}</div>
                <div class="user-role">{{ ucfirst(auth()->user()?->role ?? 'Doctor') }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin-left:auto">
                @csrf
                <button type="submit" class="logout-btn" title="Logout"><i class="fas fa-power-off"></i></button>
            </form>
        </div>
    </div>
</aside>

<!-- ── Main wrapper ── -->
<div class="main" style="position:relative; z-index:1;">
    <!-- Topbar -->
    <header class="topbar">
        <div>
            <div class="page-title">@yield('page-title', 'Dashboard')</div>
            <div class="page-subtitle">@yield('page-subtitle', '')</div>
        </div>
        <div class="topbar-right">
            <a href="#" class="topbar-btn">
                <i class="fas fa-bell"></i>
                <span class="notif-dot"></span>
            </a>
            <a href="#" class="topbar-btn">
                <i class="fas fa-cog"></i>
            </a>
        </div>
    </header>

    <!-- Flash messages -->
    <div style="padding: 0 28px; margin-top: 16px;">
        @if(session('success'))
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
        @endif
    </div>

    <!-- Page Content -->
    <main class="content">
        @yield('content')
    </main>
</div>

@stack('scripts')
</body>
</html>
