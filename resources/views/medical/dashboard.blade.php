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
        * { font-family: 'DM Sans', sans-serif; }
        body { background: #f8f7ff; }

        /* Sidebar */
        .sidebar { width: 260px; min-height: 100vh; background: #1e1035; position: fixed; left: 0; top: 0; z-index: 50; display: flex; flex-direction: column; transition: transform 0.3s ease; }
        .sidebar-logo { padding: 1.5rem 1.5rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.07); }
        .sidebar-nav { flex: 1; padding: 1.25rem 0.75rem; overflow-y: auto; }
        .nav-section-label { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.1em; color: rgba(255,255,255,0.3); padding: 0.5rem 0.75rem; margin-top: 0.5rem; text-transform: uppercase; }
        .nav-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.65rem 0.85rem; border-radius: 10px; color: rgba(255,255,255,0.55); font-size: 0.875rem; font-weight: 500; cursor: pointer; transition: all 0.2s; text-decoration: none; margin-bottom: 2px; }
        .nav-item:hover { background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.9); }
        .nav-item.active { background: linear-gradient(135deg, #7c3aed 0%, #047481 100%); color: white; box-shadow: 0 4px 12px rgba(124,58,237,0.3); }
        .nav-item .icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 0.8rem; background: rgba(255,255,255,0.08); }
        .nav-item.active .icon { background: rgba(255,255,255,0.2); }
        .sidebar-footer { padding: 1rem 0.75rem; border-top: 1px solid rgba(255,255,255,0.07); }
        .sidebar-user { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; border-radius: 10px; background: rgba(255,255,255,0.05); }

        /* Main content */
        .main-content { margin-left: 260px; min-height: 100vh; }
        .topbar { background: white; border-bottom: 1px solid #ede9fe; padding: 0 2rem; height: 64px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 40; }

        /* Gradient */
        .text-gradient { background: linear-gradient(135deg, #6d28d9 0%, #0694a2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .bg-gradient-main { background: linear-gradient(135deg, #6d28d9 0%, #0694a2 100%); }

        /* Stat cards */
        .stat-card { background: white; border-radius: 16px; padding: 1.5rem; border: 1px solid #ede9fe; transition: all 0.25s; position: relative; overflow: hidden; }
        .stat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(135deg, #7c3aed 0%, #0694a2 100%); }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 30px rgba(124,58,237,0.1); }

        /* Patient table */
        .patient-table { width: 100%; border-collapse: collapse; }
        .patient-table th { background: #f5f3ff; color: #6d28d9; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.06em; padding: 0.85rem 1rem; text-align: left; font-weight: 700; }
        .patient-table th:first-child { border-radius: 10px 0 0 10px; }
        .patient-table th:last-child { border-radius: 0 10px 10px 0; }
        .patient-table td { padding: 0.9rem 1rem; border-bottom: 1px solid #f5f3ff; font-size: 0.84rem; color: #374151; vertical-align: middle; }
        .patient-table tr:last-child td { border-bottom: none; }
        .patient-table tr:hover td { background: #fdfcff; }
        .avatar-circle { width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.68rem; font-weight: 700; flex-shrink: 0; }

        /* Status badges */
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 999px; font-size: 0.7rem; font-weight: 600; }
        .badge-admitted { background: #d1fae5; color: #065f46; }
        .badge-observation { background: #fef3c7; color: #92400e; }
        .badge-critical { background: #fee2e2; color: #991b1b; }
        .badge-discharged { background: #f3f4f6; color: #374151; }
        .badge-dot { width: 5px; height: 5px; border-radius: 50%; }

        /* Vital card */
        .vital-card { background: linear-gradient(135deg, rgba(109,40,217,0.04) 0%, rgba(6,148,162,0.04) 100%); border: 1px solid rgba(109,40,217,0.12); border-radius: 12px; padding: 0.9rem 1rem; }

        /* Quick action */
        .quick-action { display: flex; flex-direction: column; align-items: center; gap: 0.5rem; padding: 1rem 0.75rem; background: white; border: 1px solid #ede9fe; border-radius: 14px; font-size: 0.75rem; font-weight: 600; color: #6d28d9; cursor: pointer; transition: all 0.2s; text-decoration: none; }
        .quick-action:hover { background: #f5f3ff; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(124,58,237,0.1); }
        .quick-action .icon-wrap { width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, #7c3aed 0%, #0694a2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 1rem; }

        /* Timeline */
        .timeline-item { position: relative; padding-left: 1.5rem; padding-bottom: 1.25rem; }
        .timeline-item::before { content: ''; position: absolute; left: 5px; top: 22px; bottom: 0; width: 2px; background: #ede9fe; }
        .timeline-item:last-child::before { display: none; }
        .timeline-dot { position: absolute; left: 0; top: 6px; width: 12px; height: 12px; border-radius: 50%; border: 2px solid #7c3aed; background: white; }
        .timeline-dot.filled { background: #7c3aed; }

        /* Alert */
        .alert-critical { background: linear-gradient(135deg, #fee2e2 0%, #fef3c7 100%); border: 1px solid #fca5a5; border-radius: 12px; }

        /* Scroll */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #ddd6fe; border-radius: 99px; }

        /* Mobile */
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 49; }
        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .sidebar-overlay.open { display: block; }
        }

        /* Pulse */
        @keyframes pulse-ring { 0% { transform: scale(0.8); opacity: 1; } 100% { transform: scale(1.5); opacity: 0; } }
        .pulse-ring { animation: pulse-ring 1.8s ease-out infinite; }

        /* Glow */
        .card-glow:hover { box-shadow: 0 0 0 3px rgba(124,58,237,0.15), 0 12px 30px rgba(124,58,237,0.1); }
    </style>
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <a href="{{ url('/') }}" class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-gradient-main flex items-center justify-center">
                <i class="fas fa-heartbeat text-white text-sm"></i>
            </div>
            <span class="text-white font-bold text-lg">{{ config('app.name', 'MediCore') }}</span>
        </a>
        <p class="text-xs text-white/30 mt-1 ml-10">Medical Dashboard</p>
    </div>

    <nav class="sidebar-nav">
        <p class="nav-section-label">Main</p>
        <a href="{{ route('medical.dashboard') }}" class="nav-item active">
            <span class="icon"><i class="fas fa-th-large"></i></span>
            Dashboard
        </a>
        <a href="{{ route('patients.index') }}" class="nav-item">
            <span class="icon"><i class="fas fa-user-injured"></i></span>
            All Patients
        </a>
        <a href="{{ route('patients.admission') }}" class="nav-item">
            <span class="icon"><i class="fas fa-bed"></i></span>
            Admissions
        </a>
        <a href="{{ route('patients.discharge') }}" class="nav-item">
            <span class="icon"><i class="fas fa-sign-out-alt"></i></span>
            Discharges
        </a>

        <p class="nav-section-label">Clinical</p>
        <a href="#" class="nav-item">
            <span class="icon"><i class="fas fa-notes-medical"></i></span>
            Clinical Notes
        </a>
        <a href="#" class="nav-item">
            <span class="icon"><i class="fas fa-file-prescription"></i></span>
            Prescriptions
        </a>
        <a href="#" class="nav-item">
            <span class="icon"><i class="fas fa-flask"></i></span>
            Lab Orders
        </a>

        <p class="nav-section-label">Ward</p>
        <a href="#" class="nav-item">
            <span class="icon"><i class="fas fa-procedures"></i></span>
            Ward Overview
        </a>
        <a href="#" class="nav-item">
            <span class="icon"><i class="fas fa-calendar-alt"></i></span>
            Schedule
        </a>
        <a href="{{ route('patients.create') }}" class="nav-item">
            <span class="icon"><i class="fas fa-user-plus"></i></span>
            Register Patient
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="User" class="w-9 h-9 rounded-full border-2 border-primary-500">
            <div class="flex-1 min-w-0">
                <p class="text-white text-sm font-semibold truncate">{{ auth()->user()->name ?? 'Dr. Omondi' }}</p>
                <p class="text-white/40 text-xs truncate capitalize">{{ auth()->user()->role ?? 'Doctor' }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-white/40 hover:text-white transition-colors" title="Logout">
                    <i class="fas fa-sign-out-alt text-sm"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

<!-- MAIN CONTENT -->
<div class="main-content">

    <!-- Top Bar -->
    <header class="topbar">
        <div class="flex items-center gap-4">
            <button id="sidebarToggle" class="lg:hidden text-gray-500 hover:text-primary-600">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <div>
                <h1 class="font-bold text-gray-800 text-base leading-tight">Good morning, {{ auth()->user()->name ?? 'Doctor' }} 👋</h1>
                <p class="text-xs text-gray-400">{{ now()->format('l, d F Y') }} &nbsp;·&nbsp; Ward 3B</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <!-- Search -->
            <div class="hidden md:flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-full px-4 py-2 w-56">
                <i class="fas fa-search text-gray-400 text-xs"></i>
                <input type="text" placeholder="Search patient…" class="bg-transparent text-sm focus:outline-none w-full text-gray-600 placeholder-gray-400">
            </div>

            <!-- Alert bell -->
            <div class="relative">
                <button class="w-9 h-9 rounded-full bg-gray-50 border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-primary-50 hover:text-primary-600 transition-colors">
                    <i class="fas fa-bell text-sm"></i>
                </button>
                <span class="absolute -top-0.5 -right-0.5 w-4 h-4 rounded-full bg-red-500 text-white text-[9px] font-bold flex items-center justify-center">3</span>
            </div>

            <!-- Avatar with logout dropdown -->
            <div class="relative" id="avatarWrapper">
                <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="User"
                     id="avatarBtn"
                     class="w-9 h-9 rounded-full border-2 border-primary-200 cursor-pointer">

                <div id="avatarDropdown"
                     class="hidden absolute right-0 top-12 w-48 bg-white border border-gray-100 rounded-2xl shadow-xl z-50 overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-50">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ auth()->user()->name ?? 'Dr. Omondi' }}</p>
                        <p class="text-xs text-gray-400 capitalize">{{ auth()->user()->role ?? 'Doctor' }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center gap-2 px-4 py-3 text-sm text-red-600 font-semibold hover:bg-red-50 transition-colors">
                            <i class="fas fa-sign-out-alt text-sm"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Page Content -->
    <main class="p-6 space-y-6">

        <!-- Critical Alert -->
        <div class="alert-critical p-4 flex items-center gap-3">
            <div class="relative">
                <div class="w-8 h-8 rounded-full bg-red-500/20 flex items-center justify-center">
                    <i class="fas fa-exclamation text-red-600 text-xs"></i>
                </div>
                <div class="absolute inset-0 rounded-full bg-red-400/20 pulse-ring"></div>
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold text-red-700">Critical Alert — ICU-2: Fatuma Omar</p>
                <p class="text-xs text-red-600/70">SpO₂ dropped to 88% · 5 minutes ago · Immediate attention required</p>
            </div>
            <a href="#" class="text-xs font-semibold text-red-700 border border-red-300 rounded-full px-3 py-1 hover:bg-red-50 transition-colors whitespace-nowrap">View Patient</a>
        </div>

        <!-- Stat Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                ['icon'=>'fa-procedures','label'=>'Admitted Today','val'=>'12','sub'=>'+3 since morning','color'=>'primary'],
                ['icon'=>'fa-user-clock','label'=>'Under Observation','val'=>'5','sub'=>'2 pending labs','color'=>'yellow'],
                ['icon'=>'fa-heartbeat','label'=>'Critical Patients','val'=>'2','sub'=>'ICU ward','color'=>'red'],
                ['icon'=>'fa-sign-out-alt','label'=>'Discharged Today','val'=>'4','sub'=>'Beds freed','color'=>'secondary'],
            ] as $s)
                <div class="stat-card card-glow">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl
                            @if($s['color']==='primary') bg-primary-100
                            @elseif($s['color']==='yellow') bg-amber-100
                            @elseif($s['color']==='red') bg-red-100
                            @else bg-secondary-100
                            @endif flex items-center justify-center">
                            <i class="fas {{ $s['icon'] }} text-sm
                                @if($s['color']==='primary') text-primary-600
                                @elseif($s['color']==='yellow') text-amber-600
                                @elseif($s['color']==='red') text-red-600
                                @else text-secondary-600
                                @endif"></i>
                        </div>
                        <i class="fas fa-arrow-trend-up text-xs text-gray-300"></i>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">{{ $s['val'] }}</p>
                    <p class="text-xs font-semibold text-gray-600 mt-0.5">{{ $s['label'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $s['sub'] }}</p>
                </div>
            @endforeach
        </div>

        <!-- Quick Actions -->
        <div>
            <h2 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wider">Quick Actions</h2>
            <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
                @foreach([
                    ['icon'=>'fa-user-plus','label'=>'Register Patient','href'=>'patients.create'],
                    ['icon'=>'fa-file-prescription','label'=>'New Prescription','href'=>'#'],
                    ['icon'=>'fa-notes-medical','label'=>'Add Note','href'=>'#'],
                    ['icon'=>'fa-flask','label'=>'Order Lab','href'=>'#'],
                    ['icon'=>'fa-sign-out-alt','label'=>'Discharge','href'=>'#'],
                    ['icon'=>'fa-procedures','label'=>'Admissions','href'=>'patients.admission'],
                ] as $qa)
                    <a href="{{ str_contains($qa['href'],'#') ? '#' : route($qa['href']) }}" class="quick-action">
                        <div class="icon-wrap"><i class="fas {{ $qa['icon'] }}"></i></div>
                        <span class="text-center leading-tight">{{ $qa['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Patient Table + Right Column -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            <div class="xl:col-span-2 bg-white rounded-2xl border border-primary-100 overflow-hidden card-glow">
                <div class="flex items-center justify-between p-5 border-b border-gray-50">
                    <div>
                        <h2 class="font-bold text-gray-800">Today's Patients</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Ward 3B · {{ now()->format('d M Y') }}</p>
                    </div>
                    <a href="{{ route('patients.index') }}" class="text-xs font-semibold text-primary-600 hover:text-primary-700 flex items-center gap-1">
                        View all <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="patient-table">
                        <thead>
                        <tr>
                            <th>Patient</th>
                            <th>MRN</th>
                            <th>Bed</th>
                            <th>Diagnosis</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach([
                            ['initials'=>'AM','name'=>'Agnes Mwangi','bg'=>'bg-primary-100','text'=>'text-primary-700','mrn'=>'MRN-2024051','bed'=>'Bed 4','dx'=>'Hypertension','status'=>'admitted'],
                            ['initials'=>'JK','name'=>'James Kimani','bg'=>'bg-secondary-100','text'=>'text-secondary-700','mrn'=>'MRN-2024052','bed'=>'Bed 7','dx'=>'Pneumonia','status'=>'observation'],
                            ['initials'=>'FO','name'=>'Fatuma Omar','bg'=>'bg-red-100','text'=>'text-red-700','mrn'=>'MRN-2024053','bed'=>'ICU-2','dx'=>'Respiratory Fail.','status'=>'critical'],
                            ['initials'=>'BN','name'=>'Brian Njoroge','bg'=>'bg-gray-100','text'=>'text-gray-600','mrn'=>'MRN-2024054','bed'=>'Bed 1','dx'=>'Appendectomy','status'=>'discharged'],
                            ['initials'=>'SM','name'=>'Sarah Mutua','bg'=>'bg-purple-100','text'=>'text-purple-700','mrn'=>'MRN-2024055','bed'=>'Bed 11','dx'=>'Diabetes T2','status'=>'admitted'],
                        ] as $p)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-2.5">
                                        <div class="avatar-circle {{ $p['bg'] }} {{ $p['text'] }}">{{ $p['initials'] }}</div>
                                        <span class="font-semibold text-gray-800">{{ $p['name'] }}</span>
                                    </div>
                                </td>
                                <td class="text-gray-400 text-xs font-mono">{{ $p['mrn'] }}</td>
                                <td class="font-medium text-gray-600">{{ $p['bed'] }}</td>
                                <td class="text-gray-600 text-xs">{{ $p['dx'] }}</td>
                                <td>
                                    @if($p['status']==='admitted')
                                        <span class="badge badge-admitted"><span class="badge-dot bg-emerald-500"></span>Admitted</span>
                                    @elseif($p['status']==='observation')
                                        <span class="badge badge-observation"><span class="badge-dot bg-amber-500"></span>Obs.</span>
                                    @elseif($p['status']==='critical')
                                        <span class="badge badge-critical"><span class="badge-dot bg-red-500"></span>Critical</span>
                                    @else
                                        <span class="badge badge-discharged"><span class="badge-dot bg-gray-400"></span>Discharged</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="#" class="text-primary-600 hover:text-primary-700 font-semibold text-xs bg-primary-50 px-3 py-1 rounded-full hover:bg-primary-100 transition-colors">View</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-4">

                <!-- Vitals -->
                <div class="bg-white rounded-2xl border border-primary-100 p-5 card-glow">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-gray-800 text-sm">Vitals — Agnes Mwangi</h3>
                        <span class="badge badge-admitted text-xs"><span class="badge-dot bg-emerald-500"></span>Live</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2.5">
                        @foreach([
                            ['label'=>'Blood Pressure','val'=>'142/88','unit'=>'mmHg','status'=>'warn','icon'=>'fa-heart'],
                            ['label'=>'Temperature','val'=>'37.4','unit'=>'°C','status'=>'ok','icon'=>'fa-thermometer-half'],
                            ['label'=>'SpO₂','val'=>'96','unit'=>'%','status'=>'ok','icon'=>'fa-lungs'],
                            ['label'=>'Heart Rate','val'=>'78','unit'=>'bpm','status'=>'ok','icon'=>'fa-heartbeat'],
                        ] as $v)
                            <div class="vital-card">
                                <div class="flex items-center gap-1 mb-1">
                                    <i class="fas {{ $v['icon'] }} text-primary-300 text-xs"></i>
                                    <p class="text-xs text-gray-400">{{ $v['label'] }}</p>
                                </div>
                                <p class="font-bold text-base
                                    @if($v['status']==='warn') text-amber-600
                                    @elseif($v['status']==='crit') text-red-600
                                    @else text-emerald-600
                                    @endif">
                                    {{ $v['val'] }}
                                    <span class="text-xs font-normal text-gray-400">{{ $v['unit'] }}</span>
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white rounded-2xl border border-primary-100 p-5 card-glow">
                    <h3 class="font-bold text-gray-800 text-sm mb-4">Recent Activity</h3>
                    <div>
                        @foreach([
                            ['time'=>'08:42','text'=>'Agnes Mwangi registered & assigned Bed 4','filled'=>true],
                            ['time'=>'09:15','text'=>'Prescription issued — James Kimani (Amoxicillin 500mg)','filled'=>true],
                            ['time'=>'10:00','text'=>'Lab results received — Agnes Mwangi (Blood panel)','filled'=>true],
                            ['time'=>'11:30','text'=>'Brian Njoroge discharged — Bed 1 freed','filled'=>false],
                        ] as $ev)
                            <div class="timeline-item">
                                <div class="timeline-dot {{ $ev['filled'] ? 'filled' : '' }}"></div>
                                <p class="text-xs text-gray-400 font-mono">Today · {{ $ev['time'] }}</p>
                                <p class="text-xs text-gray-700 mt-0.5 leading-relaxed">{{ $ev['text'] }}</p>
                            </div>
                        @endforeach
                    </div>
                    <a href="{{ route('patients.index') }}" class="block text-center text-xs text-primary-600 font-semibold mt-2 hover:text-primary-700">View all activity →</a>
                </div>

            </div>
        </div>

        <!-- Bed Occupancy -->
        <div class="bg-white rounded-2xl border border-primary-100 p-5 card-glow">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-gray-800">Ward 3B — Bed Occupancy</h2>
                <span class="text-xs text-gray-400">12 / 16 beds occupied</span>
            </div>
            <div class="grid grid-cols-8 sm:grid-cols-16 gap-2">
                @for($i = 1; $i <= 16; $i++)
                    @php
                        $occupiedBeds = [1, 2, 3, 4, 5, 7, 8, 10, 11, 12, 14, 15];
                        $criticalBeds = [3];
                        $occupied = in_array($i, $occupiedBeds);
                        $critical = in_array($i, $criticalBeds);
                    @endphp
                    <div class="flex flex-col items-center gap-1">
                        <div class="w-full aspect-square rounded-lg flex items-center justify-center text-xs font-bold
                            @if($critical) bg-red-100 text-red-600 border-2 border-red-300
                            @elseif($occupied) bg-primary-100 text-primary-700 border border-primary-200
                            @else bg-gray-50 text-gray-300 border border-gray-100
                            @endif">
                            <i class="fas fa-bed text-sm"></i>
                        </div>
                        <span class="text-[10px] text-gray-400">{{ $i }}</span>
                    </div>
                @endfor
            </div>
            <div class="flex items-center gap-5 mt-4 text-xs text-gray-500">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-primary-100 border border-primary-200 inline-block"></span> Occupied</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-red-100 border-2 border-red-300 inline-block"></span> Critical</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-gray-50 border border-gray-100 inline-block"></span> Available (4)</span>
            </div>
        </div>

    </main>
</div>

<script>
    // Sidebar toggle (mobile)
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    sidebarToggle?.addEventListener('click', () => {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('open');
    });
    overlay?.addEventListener('click', () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('open');
    });

    // Avatar dropdown toggle
    const avatarBtn = document.getElementById('avatarBtn');
    const avatarDropdown = document.getElementById('avatarDropdown');

    avatarBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        avatarDropdown.classList.toggle('hidden');
    });
    document.addEventListener('click', () => {
        avatarDropdown?.classList.add('hidden');
    });
</script>
</body>
</html>
