<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'MediCore HMS') }} — Medical Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f5f3ff', 100: '#ede9fe', 200: '#ddd6fe', 300: '#c4b5fd',
                            400: '#a78bfa', 500: '#8b5cf6', 600: '#7c3aed', 700: '#6d28d9',
                            800: '#5b21b6', 900: '#4c1d95',
                        },
                        secondary: {
                            50: '#edfafa', 100: '#d5f5f6', 200: '#afecef', 300: '#7edce2',
                            400: '#16bdca', 500: '#0694a2', 600: '#047481', 700: '#036672',
                            800: '#05505c', 900: '#014451',
                        },
                    },
                    fontFamily: { sans: ['DM Sans', 'sans-serif'] },
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; font-family: 'DM Sans', sans-serif; }

        /* ── ROOT: full-height anchors for fixed sidebar ── */
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;          /* body must have height so vh units work reliably */
        }
        body { background: #f8f7ff; }

        /* ─── SIDEBAR ─── */
        .sidebar {
            width: 260px;
            height: 100vh;          /* fill the full viewport height */
            max-height: 100vh;      /* FIX: cap so flex children never overflow */
            background: #1e1035;
            position: fixed;
            left: 0; top: 0;
            z-index: 50;
            display: flex;
            flex-direction: column;
            overflow: hidden;       /* clip; only nav scrolls internally */
            transition: transform 0.3s ease;
        }

        .sidebar-logo {
            padding: 1.5rem 1.5rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            flex-shrink: 0;         /* never compressed */
        }

        /* ── THE SCROLLABLE ZONE ── */
        .sidebar-nav {
            flex: 1 1 0;            /* FIX: "1 1 0" — grow, shrink, zero base */
            min-height: 0;          /* critical: lets flex child shrink below content height */
            padding: 1.25rem 0.75rem;
            overflow-y: auto;       /* scrolls when nav items exceed available space */
            overflow-x: hidden;
        }

        .sidebar-footer {
            padding: 1rem 0.75rem;
            border-top: 1px solid rgba(255,255,255,0.07);
            flex-shrink: 0;         /* never compressed */
        }

        /* Nav labels & items */
        .nav-section-label {
            font-size: 0.65rem; font-weight: 700; letter-spacing: 0.1em;
            color: rgba(255,255,255,0.3); padding: 0.5rem 0.75rem;
            margin-top: 0.5rem; text-transform: uppercase;
        }
        .nav-item {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.65rem 0.85rem; border-radius: 10px;
            color: rgba(255,255,255,0.55); font-size: 0.875rem; font-weight: 500;
            cursor: pointer; transition: all 0.2s; text-decoration: none; margin-bottom: 2px;
        }
        .nav-item:hover { background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.9); }
        .nav-item.active {
            background: linear-gradient(135deg, #7c3aed 0%, #047481 100%);
            color: white; box-shadow: 0 4px 12px rgba(124,58,237,0.3);
        }
        .nav-item .icon {
            width: 32px; height: 32px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; font-size: 0.8rem; background: rgba(255,255,255,0.08);
        }
        .nav-item.active .icon { background: rgba(255,255,255,0.2); }

        /* User strip */
        .sidebar-user {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.75rem; border-radius: 10px;
            background: rgba(255,255,255,0.05); margin-bottom: 10px;
        }

        /* Logout button */
        .logout-form { display: block; width: 100%; margin: 0; padding: 0; }
        .logout-btn {
            width: 100%;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            padding: 10px 16px; border-radius: 10px;
            background: rgba(239,68,68,0.15); color: #fca5a5;
            border: 1px solid rgba(239,68,68,0.3);
            font-size: 13px; font-weight: 600; cursor: pointer;
            transition: background 0.2s, color 0.2s;
            font-family: 'DM Sans', sans-serif;
            appearance: none; -webkit-appearance: none; line-height: 1;
        }
        .logout-btn:hover { background: rgba(239,68,68,0.35); color: #fff; }

        /* ─── MAIN ─── */
        .main-content { margin-left: 260px; min-height: 100vh; }
        .topbar {
            background: white; border-bottom: 1px solid #ede9fe;
            padding: 0 2rem; height: 64px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 40;
        }

        /* Topbar logout */
        .topbar-logout {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 14px; border-radius: 8px;
            background: #fee2e2; color: #dc2626;
            border: 1px solid #fca5a5;
            font-size: 12px; font-weight: 600; cursor: pointer;
            transition: background 0.2s, color 0.2s;
            font-family: 'DM Sans', sans-serif;
            appearance: none; -webkit-appearance: none; line-height: 1;
        }
        .topbar-logout:hover { background: #dc2626; color: #fff; }

        /* Helpers */
        .grad-bg { background: linear-gradient(135deg, #6d28d9 0%, #0694a2 100%); }

        /* Stat cards */
        .stat-card {
            background: white; border-radius: 16px; padding: 1.5rem;
            border: 1px solid #ede9fe; transition: all 0.25s;
            position: relative; overflow: hidden;
        }
        .stat-card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
            background: linear-gradient(135deg, #7c3aed 0%, #0694a2 100%);
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 30px rgba(124,58,237,0.1); }

        /* Patient table */
        .patient-table { width: 100%; border-collapse: collapse; }
        .patient-table th {
            background: #f5f3ff; color: #6d28d9; font-size: 0.7rem;
            text-transform: uppercase; letter-spacing: 0.06em;
            padding: 0.85rem 1rem; text-align: left; font-weight: 700;
        }
        .patient-table th:first-child { border-radius: 10px 0 0 10px; }
        .patient-table th:last-child  { border-radius: 0 10px 10px 0; }
        .patient-table td {
            padding: 0.9rem 1rem; border-bottom: 1px solid #f5f3ff;
            font-size: 0.84rem; color: #374151; vertical-align: middle;
        }
        .patient-table tr:last-child td { border-bottom: none; }
        .patient-table tr:hover td { background: #fdfcff; }
        .avatar-circle {
            width: 34px; height: 34px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.68rem; font-weight: 700; flex-shrink: 0;
        }

        /* Badges */
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 999px; font-size: 0.7rem; font-weight: 600; }
        .badge-admitted    { background: #d1fae5; color: #065f46; }
        .badge-observation { background: #fef3c7; color: #92400e; }
        .badge-critical    { background: #fee2e2; color: #991b1b; }
        .badge-discharged  { background: #f3f4f6; color: #374151; }
        .badge-stable      { background: #dbeafe; color: #1e40af; }
        .badge-recovering  { background: #d1fae5; color: #065f46; }
        .badge-dot { width: 5px; height: 5px; border-radius: 50%; display: inline-block; }

        /* Vitals */
        .vital-card {
            background: linear-gradient(135deg, rgba(109,40,217,0.04) 0%, rgba(6,148,162,0.04) 100%);
            border: 1px solid rgba(109,40,217,0.12); border-radius: 12px; padding: 0.9rem 1rem;
        }

        /* Quick actions */
        .quick-action {
            display: flex; flex-direction: column; align-items: center; gap: 0.5rem;
            padding: 1rem 0.75rem; background: white; border: 1px solid #ede9fe;
            border-radius: 14px; font-size: 0.75rem; font-weight: 600; color: #6d28d9;
            cursor: pointer; transition: all 0.2s; text-decoration: none;
        }
        .quick-action:hover { background: #f5f3ff; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(124,58,237,0.1); }
        .quick-action .icon-wrap {
            width: 44px; height: 44px; border-radius: 12px;
            background: linear-gradient(135deg, #7c3aed 0%, #0694a2 100%);
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 1rem;
        }

        /* Timeline */
        .timeline-item { position: relative; padding-left: 1.5rem; padding-bottom: 1.25rem; }
        .timeline-item::before { content: ''; position: absolute; left: 5px; top: 22px; bottom: 0; width: 2px; background: #ede9fe; }
        .timeline-item:last-child::before { display: none; }
        .timeline-dot { position: absolute; left: 0; top: 6px; width: 12px; height: 12px; border-radius: 50%; border: 2px solid #7c3aed; background: white; }
        .timeline-dot.filled { background: #7c3aed; }

        /* Alert */
        .alert-critical {
            background: linear-gradient(135deg, #fee2e2 0%, #fef3c7 100%);
            border: 1px solid #fca5a5; border-radius: 12px;
        }

        /* Card glow */
        .card-glow:hover { box-shadow: 0 0 0 3px rgba(124,58,237,0.15), 0 12px 30px rgba(124,58,237,0.1); }

        /* Scrollbars — sidebar nav */
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 99px; }

        /* Scrollbars — page */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #ddd6fe; border-radius: 99px; }

        /* Pulse */
        @keyframes pulse-ring { 0% { transform: scale(0.8); opacity: 1; } 100% { transform: scale(1.5); opacity: 0; } }
        .pulse-ring { animation: pulse-ring 1.8s ease-out infinite; }

        /* Mobile */
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 49; }
        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .sidebar-overlay.open { display: block; }
        }
    </style>
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

{{-- ══════════ SIDEBAR ══════════ --}}
<aside class="sidebar" id="sidebar">

    {{-- Logo — always visible (flex-shrink: 0) --}}
    <div class="sidebar-logo">
        <a href="{{ url('/') }}" style="display:flex;align-items:center;gap:8px;text-decoration:none;">
            <div style="width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;" class="grad-bg">
                <i class="fas fa-heartbeat" style="color:white;font-size:13px;"></i>
            </div>
            <span style="color:white;font-weight:700;font-size:17px;">{{ config('app.name', 'MediCore') }}</span>
        </a>
        <p style="font-size:11px;margin:4px 0 0 40px;color:rgba(255,255,255,0.3);">Medical Dashboard</p>
    </div>

    {{-- Navigation — the scrollable middle zone --}}
    <nav class="sidebar-nav">
        <p class="nav-section-label">Main</p>

        <a href="{{ route('medical.dashboard') }}" class="nav-item active">
            <span class="icon"><i class="fas fa-th-large"></i></span> Dashboard
        </a>
        <a href="{{ route('patients.index') }}" class="nav-item">
            <span class="icon"><i class="fas fa-user-injured"></i></span> All Patients
        </a>
        <a href="{{ route('patients.admission') }}" class="nav-item">
            <span class="icon"><i class="fas fa-bed"></i></span> Admissions
        </a>
        <a href="{{ route('patients.discharge') }}" class="nav-item">
            <span class="icon"><i class="fas fa-sign-out-alt"></i></span> Discharges
        </a>

        <p class="nav-section-label">Clinical</p>
        <a href="#" class="nav-item">
            <span class="icon"><i class="fas fa-notes-medical"></i></span> Clinical Notes
        </a>
        <a href="#" class="nav-item">
            <span class="icon"><i class="fas fa-file-prescription"></i></span> Prescriptions
        </a>
        <a href="#" class="nav-item">
            <span class="icon"><i class="fas fa-flask"></i></span> Lab Orders
        </a>
        <a href="#" class="nav-item">
            <span class="icon"><i class="fas fa-vials"></i></span> Lab Results
        </a>
        <a href="#" class="nav-item">
            <span class="icon"><i class="fas fa-x-ray"></i></span> Radiology
        </a>

        <p class="nav-section-label">Ward</p>
        <a href="#" class="nav-item">
            <span class="icon"><i class="fas fa-procedures"></i></span> Ward Overview
        </a>
        <a href="#" class="nav-item">
            <span class="icon"><i class="fas fa-calendar-alt"></i></span> Schedule
        </a>
        <a href="{{ route('patients.create') }}" class="nav-item">
            <span class="icon"><i class="fas fa-user-plus"></i></span> Register Patient
        </a>

        <p class="nav-section-label">Reports</p>
        <a href="#" class="nav-item">
            <span class="icon"><i class="fas fa-chart-bar"></i></span> Analytics
        </a>
        <a href="#" class="nav-item">
            <span class="icon"><i class="fas fa-file-medical-alt"></i></span> Reports
        </a>
        <a href="#" class="nav-item">
            <span class="icon"><i class="fas fa-cog"></i></span> Settings
        </a>
    </nav>

    {{-- Footer — always visible (flex-shrink: 0) --}}
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div style="width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-weight:700;font-size:13px;color:white;" class="grad-bg">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div style="flex:1;min-width:0;">
                <p style="color:white;font-size:13px;font-weight:600;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</p>
                <p style="color:rgba(255,255,255,0.4);font-size:11px;margin:0;text-transform:capitalize;">{{ auth()->user()->role }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="logout-form">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>

</aside>

{{-- ══════════ MAIN CONTENT ══════════ --}}
<div class="main-content">

    {{-- Topbar --}}
    <header class="topbar">
        <div class="flex items-center gap-4">
            <button id="sidebarToggle" class="lg:hidden text-gray-500 hover:text-primary-600">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <div>
                <h1 class="font-bold text-gray-800 text-base leading-tight">
                    Good morning, {{ auth()->user()->name }} 👋
                </h1>
                <p class="text-xs text-gray-400">{{ now()->format('l, d F Y') }} &nbsp;·&nbsp; Ward 3B</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="hidden md:flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-full px-4 py-2 w-56">
                <i class="fas fa-search text-gray-400 text-xs"></i>
                <input type="text" placeholder="Search patient…"
                       class="bg-transparent text-sm focus:outline-none w-full text-gray-600 placeholder-gray-400">
            </div>

            <div class="relative">
                <button class="w-9 h-9 rounded-full bg-gray-50 border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-primary-50 hover:text-primary-600 transition-colors">
                    <i class="fas fa-bell text-sm"></i>
                </button>
                @if($statusCounts['critical'] > 0)
                    <span class="absolute -top-0.5 -right-0.5 w-4 h-4 rounded-full bg-red-500 text-white text-[9px] font-bold flex items-center justify-center">
                        {{ $statusCounts['critical'] }}
                    </span>
                @endif
            </div>

            <div style="width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:13px;flex-shrink:0;" class="grad-bg">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>

            <form method="POST" action="{{ route('logout') }}" style="margin:0;padding:0;display:inline-flex;align-items:center;">
                @csrf
                <button type="submit" class="topbar-logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </header>

    {{-- Page content --}}
    <main class="p-6 space-y-6">

        {{-- Critical Alert --}}
        @if($statusCounts['critical'] > 0)
            <div class="alert-critical p-4 flex items-center gap-3">
                <div class="relative">
                    <div class="w-8 h-8 rounded-full bg-red-500/20 flex items-center justify-center">
                        <i class="fas fa-exclamation text-red-600 text-xs"></i>
                    </div>
                    <div class="absolute inset-0 rounded-full bg-red-400/20 pulse-ring"></div>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-red-700">
                        {{ $statusCounts['critical'] }} Critical Patient{{ $statusCounts['critical'] > 1 ? 's' : '' }} — Immediate Attention Required
                    </p>
                    <p class="text-xs text-red-600/70">Check admissions for critical status patients</p>
                </div>
                <a href="{{ route('patients.admission') }}?status=critical"
                   class="text-xs font-semibold text-red-700 border border-red-300 rounded-full px-3 py-1 hover:bg-red-50 transition-colors whitespace-nowrap">
                    View Critical
                </a>
            </div>
        @endif

        {{-- Stat Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="stat-card card-glow">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center">
                        <i class="fas fa-procedures text-sm text-primary-600"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['inpatients'] }}</p>
                <p class="text-xs font-semibold text-gray-600 mt-0.5">Current Inpatients</p>
                <p class="text-xs text-gray-400 mt-1">All admitted patients</p>
            </div>
            <div class="stat-card card-glow">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                        <i class="fas fa-user-clock text-sm text-amber-600"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ $statusCounts['observation'] }}</p>
                <p class="text-xs font-semibold text-gray-600 mt-0.5">Under Observation</p>
                <p class="text-xs text-gray-400 mt-1">Needs monitoring</p>
            </div>
            <div class="stat-card card-glow">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
                        <i class="fas fa-heartbeat text-sm text-red-600"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ $statusCounts['critical'] }}</p>
                <p class="text-xs font-semibold text-gray-600 mt-0.5">Critical Patients</p>
                <p class="text-xs text-gray-400 mt-1">ICU ward</p>
            </div>
            <div class="stat-card card-glow">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-secondary-100 flex items-center justify-center">
                        <i class="fas fa-sign-out-alt text-sm text-secondary-600"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['discharged_today'] }}</p>
                <p class="text-xs font-semibold text-gray-600 mt-0.5">Discharged Today</p>
                <p class="text-xs text-gray-400 mt-1">Beds freed</p>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div>
            <h2 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wider">Quick Actions</h2>
            <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
                @foreach([
                    ['icon'=>'fa-user-plus',        'label'=>'Register Patient', 'href'=>'patients.create'],
                    ['icon'=>'fa-file-prescription', 'label'=>'New Prescription','href'=>'#'],
                    ['icon'=>'fa-notes-medical',     'label'=>'Add Note',        'href'=>'#'],
                    ['icon'=>'fa-flask',             'label'=>'Order Lab',       'href'=>'#'],
                    ['icon'=>'fa-sign-out-alt',      'label'=>'Discharge',       'href'=>'#'],
                    ['icon'=>'fa-procedures',        'label'=>'Admissions',      'href'=>'patients.admission'],
                ] as $qa)
                    <a href="{{ $qa['href'] === '#' ? '#' : route($qa['href']) }}" class="quick-action">
                        <div class="icon-wrap"><i class="fas {{ $qa['icon'] }}"></i></div>
                        <span class="text-center leading-tight">{{ $qa['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Patient Table + Right column --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            <div class="xl:col-span-2 bg-white rounded-2xl border border-primary-100 overflow-hidden card-glow">
                <div class="flex items-center justify-between p-5 border-b border-gray-50">
                    <div>
                        <h2 class="font-bold text-gray-800">Today's Patients</h2>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ now()->format('d M Y') }} · {{ $todaysPatients->count() }} patient(s)
                        </p>
                    </div>
                    <a href="{{ route('patients.index') }}"
                       class="text-xs font-semibold text-primary-600 hover:text-primary-700 flex items-center gap-1">
                        View all <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="patient-table">
                        <thead>
                        <tr>
                            <th>Patient</th>
                            <th>ID</th>
                            <th>Ward</th>
                            <th>Diagnosis</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($todaysPatients as $patient)
                            <tr onclick="window.location='{{ route('patients.show', $patient) }}'" style="cursor:pointer;">
                                <td>
                                    <div class="flex items-center gap-2.5">
                                        <div class="avatar-circle bg-primary-100 text-primary-700">
                                            {{ strtoupper(substr($patient->name, 0, 1)) }}{{ strtoupper(substr(strstr($patient->name, ' '), 1, 1)) }}
                                        </div>
                                        <span class="font-semibold text-gray-800">{{ $patient->name }}</span>
                                    </div>
                                </td>
                                <td class="text-gray-400 text-xs font-mono">#{{ $patient->id }}</td>
                                <td class="font-medium text-gray-600">{{ $patient->ward ?? '—' }}</td>
                                <td class="text-gray-600 text-xs">{{ Str::limit($patient->diagnosis, 28) }}</td>
                                <td>
                                    @php $st = $patient->status; @endphp
                                    @if($st === 'admitted')
                                        <span class="badge badge-admitted"><span class="badge-dot" style="background:#10b981;"></span>Admitted</span>
                                    @elseif($st === 'observation')
                                        <span class="badge badge-observation"><span class="badge-dot" style="background:#f59e0b;"></span>Obs.</span>
                                    @elseif($st === 'critical')
                                        <span class="badge badge-critical"><span class="badge-dot" style="background:#ef4444;"></span>Critical</span>
                                    @elseif($st === 'stable')
                                        <span class="badge badge-stable"><span class="badge-dot" style="background:#3b82f6;"></span>Stable</span>
                                    @elseif($st === 'recovering')
                                        <span class="badge badge-recovering"><span class="badge-dot" style="background:#34d399;"></span>Recovering</span>
                                    @else
                                        <span class="badge badge-discharged"><span class="badge-dot" style="background:#9ca3af;"></span>{{ ucfirst($st) }}</span>
                                    @endif
                                </td>
                                <td onclick="event.stopPropagation()">
                                    <a href="{{ route('patients.show', $patient) }}"
                                       class="text-primary-600 hover:text-primary-700 font-semibold text-xs bg-primary-50 px-3 py-1 rounded-full hover:bg-primary-100 transition-colors">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-12">
                                    <i class="fas fa-user-injured text-3xl mb-3 block text-gray-200"></i>
                                    <p class="text-gray-400 text-sm">No patients attended today yet.</p>
                                    <a href="{{ route('patients.create') }}"
                                       class="inline-flex items-center gap-1 mt-3 text-xs text-primary-600 font-semibold hover:text-primary-700">
                                        <i class="fas fa-plus"></i> Register first patient
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Right column --}}
            <div class="space-y-4">

                <div class="bg-white rounded-2xl border border-primary-100 p-5 card-glow">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-gray-800 text-sm">Vitals — Agnes Mwangi</h3>
                        <span class="badge badge-admitted"><span class="badge-dot" style="background:#10b981;"></span>Live</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2.5">
                        @foreach([
                            ['label'=>'Blood Pressure','val'=>'142/88','unit'=>'mmHg','status'=>'warn','icon'=>'fa-heart'],
                            ['label'=>'Temperature',   'val'=>'37.4',  'unit'=>'°C',   'status'=>'ok',  'icon'=>'fa-thermometer-half'],
                            ['label'=>'SpO₂',          'val'=>'96',    'unit'=>'%',    'status'=>'ok',  'icon'=>'fa-lungs'],
                            ['label'=>'Heart Rate',    'val'=>'78',    'unit'=>'bpm',  'status'=>'ok',  'icon'=>'fa-heartbeat'],
                        ] as $v)
                            <div class="vital-card">
                                <div class="flex items-center gap-1 mb-1">
                                    <i class="fas {{ $v['icon'] }} text-xs" style="color:#c4b5fd;"></i>
                                    <p class="text-xs text-gray-400">{{ $v['label'] }}</p>
                                </div>
                                <p class="font-bold text-base {{ $v['status'] === 'warn' ? 'text-amber-600' : 'text-emerald-600' }}">
                                    {{ $v['val'] }}
                                    <span class="text-xs font-normal text-gray-400">{{ $v['unit'] }}</span>
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-primary-100 p-5 card-glow">
                    <h3 class="font-bold text-gray-800 text-sm mb-4">Recent Activity</h3>
                    @forelse($recentPatients as $rp)
                        <div class="timeline-item">
                            <div class="timeline-dot filled"></div>
                            <p class="text-xs text-gray-400 font-mono">
                                Today · {{ \Carbon\Carbon::parse($rp->admitted_at)->format('H:i') }}
                            </p>
                            <p class="text-xs text-gray-700 mt-0.5 leading-relaxed">
                                {{ $rp->name }} admitted to {{ $rp->ward ?? 'ward' }}
                            </p>
                        </div>
                    @empty
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <p class="text-xs text-gray-400">No recent activity today.</p>
                        </div>
                    @endforelse
                    <a href="{{ route('patients.index') }}"
                       class="block text-center text-xs text-primary-600 font-semibold mt-2 hover:text-primary-700">
                        View all activity →
                    </a>
                </div>

            </div>
        </div>

        {{-- Bed Occupancy --}}
        <div class="bg-white rounded-2xl border border-primary-100 p-5 card-glow">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-gray-800">Ward 3B — Bed Occupancy</h2>
                <span class="text-xs text-gray-400">12 / 16 beds occupied</span>
            </div>
            <div class="grid grid-cols-8 gap-2">
                @for($i = 1; $i <= 16; $i++)
                    @php
                        $occupiedBeds = [1,2,3,4,5,7,8,10,11,12,14,15];
                        $criticalBeds = [3];
                        $occupied = in_array($i, $occupiedBeds);
                        $critical = in_array($i, $criticalBeds);
                    @endphp
                    <div class="flex flex-col items-center gap-1">
                        <div class="w-full aspect-square rounded-lg flex items-center justify-center text-xs font-bold
                            {{ $critical ? 'bg-red-100 text-red-600 border-2 border-red-300' : ($occupied ? 'bg-primary-100 text-primary-700 border border-primary-200' : 'bg-gray-50 text-gray-300 border border-gray-100') }}">
                            <i class="fas fa-bed text-sm"></i>
                        </div>
                        <span class="text-gray-400" style="font-size:10px;">{{ $i }}</span>
                    </div>
                @endfor
            </div>
            <div class="flex items-center gap-5 mt-4 text-xs text-gray-500">
                <span class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded bg-primary-100 border border-primary-200 inline-block"></span> Occupied
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded bg-red-100 border-2 border-red-300 inline-block"></span> Critical
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded bg-gray-50 border border-gray-100 inline-block"></span> Available (4)
                </span>
            </div>
        </div>

    </main>
</div>

<script>
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar       = document.getElementById('sidebar');
    const overlay       = document.getElementById('sidebarOverlay');

    sidebarToggle?.addEventListener('click', () => {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('open');
    });
    overlay?.addEventListener('click', () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('open');
    });
</script>
</body>
</html>
