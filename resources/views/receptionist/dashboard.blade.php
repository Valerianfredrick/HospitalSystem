<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'MediCore HMS') }} — Receptionist Dashboard</title>
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
        .main-content { margin-left: 260px; min-height: 100vh; }
        .topbar { background: white; border-bottom: 1px solid #ede9fe; padding: 0 2rem; height: 64px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 40; }
        .text-gradient { background: linear-gradient(135deg, #6d28d9 0%, #0694a2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .bg-gradient-main { background: linear-gradient(135deg, #6d28d9 0%, #0694a2 100%); }
        .stat-card { background: white; border-radius: 16px; padding: 1.5rem; border: 1px solid #ede9fe; transition: all 0.25s; position: relative; overflow: hidden; }
        .stat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(135deg, #7c3aed 0%, #0694a2 100%); }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 30px rgba(124,58,237,0.1); }
        .patient-table { width: 100%; border-collapse: collapse; }
        .patient-table th { background: #f5f3ff; color: #6d28d9; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.06em; padding: 0.85rem 1rem; text-align: left; font-weight: 700; }
        .patient-table th:first-child { border-radius: 10px 0 0 10px; }
        .patient-table th:last-child { border-radius: 0 10px 10px 0; }
        .patient-table td { padding: 0.9rem 1rem; border-bottom: 1px solid #f5f3ff; font-size: 0.84rem; color: #374151; vertical-align: middle; }
        .patient-table tr:last-child td { border-bottom: none; }
        .patient-table tr:hover td { background: #fdfcff; }
        .avatar-circle { width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.68rem; font-weight: 700; flex-shrink: 0; }
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 999px; font-size: 0.7rem; font-weight: 600; }
        .badge-assigned { background: #d1fae5; color: #065f46; }
        .badge-unassigned { background: #fef3c7; color: #92400e; }
        .badge-dot { width: 5px; height: 5px; border-radius: 50%; }
        .quick-action { display: flex; flex-direction: column; align-items: center; gap: 0.5rem; padding: 1rem 0.75rem; background: white; border: 1px solid #ede9fe; border-radius: 14px; font-size: 0.75rem; font-weight: 600; color: #6d28d9; cursor: pointer; transition: all 0.2s; text-decoration: none; }
        .quick-action:hover { background: #f5f3ff; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(124,58,237,0.1); }
        .quick-action .icon-wrap { width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, #7c3aed 0%, #0694a2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 1rem; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #ddd6fe; border-radius: 99px; }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 49; }
        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .sidebar-overlay.open { display: block; }
        }
        .card-glow:hover { box-shadow: 0 0 0 3px rgba(124,58,237,0.15), 0 12px 30px rgba(124,58,237,0.1); }
    </style>
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <a href="{{ url('/') }}" class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-gradient-main flex items-center justify-center">
                <i class="fas fa-heartbeat text-white text-sm"></i>
            </div>
            <span class="text-white font-bold text-lg">{{ config('app.name', 'MediCore') }}</span>
        </a>
        <p class="text-xs text-white/30 mt-1 ml-10">Front Desk</p>
    </div>

    <nav class="sidebar-nav">
        <p class="nav-section-label">Main</p>
        <a href="{{ route('receptionist.dashboard') }}" class="nav-item active">
            <span class="icon"><i class="fas fa-th-large"></i></span>
            Dashboard
        </a>
        <a href="{{ route('receptionist.patients.index') }}" class="nav-item">
            <span class="icon"><i class="fas fa-user-injured"></i></span>
            All Patients
        </a>
        <a href="{{ route('receptionist.patients.admission') }}" class="nav-item">
            <span class="icon"><i class="fas fa-bed"></i></span>
            Admissions
        </a>

        <p class="nav-section-label">Front Desk</p>
        <a href="{{ route('receptionist.patients.create') }}" class="nav-item">
            <span class="icon"><i class="fas fa-user-plus"></i></span>
            Register Patient
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="w-9 h-9 rounded-full border-2 border-primary-500 bg-primary-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->name ?? 'R', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-white text-sm font-semibold truncate">{{ auth()->user()->name ?? 'Receptionist' }}</p>
                <p class="text-white/40 text-xs truncate capitalize">{{ auth()->user()->role ?? 'Receptionist' }}</p>
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

<div class="main-content">

    <header class="topbar">
        <div class="flex items-center gap-4">
            <button id="sidebarToggle" class="lg:hidden text-gray-500 hover:text-primary-600">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <div>
                <h1 class="font-bold text-gray-800 text-base leading-tight">Welcome, {{ auth()->user()->name ?? 'Receptionist' }} 👋</h1>
                <p class="text-xs text-gray-400">{{ now()->format('l, d F Y') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="relative" id="avatarWrapper">
                <div id="avatarBtn"
                     class="w-9 h-9 rounded-full border-2 border-primary-200 cursor-pointer bg-primary-600 flex items-center justify-center text-white text-xs font-bold">
                    {{ strtoupper(substr(auth()->user()->name ?? 'R', 0, 1)) }}
                </div>
                <div id="avatarDropdown"
                     class="hidden absolute right-0 top-12 w-48 bg-white border border-gray-100 rounded-2xl shadow-xl z-50 overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-50">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ auth()->user()->name ?? 'Receptionist' }}</p>
                        <p class="text-xs text-gray-400 capitalize">{{ auth()->user()->role ?? 'Receptionist' }}</p>
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

    <main class="p-6 space-y-6">

        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-xl p-4 flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl p-4 flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach([
                ['icon'=>'fa-user-plus','label'=>'Registered Today','val'=>$stats['registered_today'],'sub'=>'New walk-ins'],
                ['icon'=>'fa-users','label'=>'Total Patients','val'=>$stats['total_patients'],'sub'=>'All-time records'],
                ['icon'=>'fa-triangle-exclamation','label'=>'Unassigned','val'=>$stats['unassigned'],'sub'=>'Awaiting a doctor'],
            ] as $s)
                <div class="stat-card card-glow">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center">
                            <i class="fas {{ $s['icon'] }} text-sm text-primary-600"></i>
                        </div>
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
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 max-w-xl">
                <a href="{{ route('receptionist.patients.create') }}" class="quick-action">
                    <div class="icon-wrap"><i class="fas fa-user-plus"></i></div>
                    <span class="text-center leading-tight">Register Patient</span>
                </a>
                <a href="{{ route('receptionist.patients.index') }}" class="quick-action">
                    <div class="icon-wrap"><i class="fas fa-user-injured"></i></div>
                    <span class="text-center leading-tight">All Patients</span>
                </a>
                <a href="{{ route('receptionist.patients.admission') }}" class="quick-action">
                    <div class="icon-wrap"><i class="fas fa-bed"></i></div>
                    <span class="text-center leading-tight">Admissions</span>
                </a>
            </div>
        </div>

        <!-- Recent Registrations -->
        <div class="bg-white rounded-2xl border border-primary-100 overflow-hidden card-glow">
            <div class="flex items-center justify-between p-5 border-b border-gray-50">
                <div>
                    <h2 class="font-bold text-gray-800">Recent Registrations</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Patients you've registered, most recent first</p>
                </div>
                <a href="{{ route('receptionist.patients.index') }}" class="text-xs font-semibold text-primary-600 hover:text-primary-700 flex items-center gap-1">
                    View all <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="patient-table">
                    <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Diagnosis</th>
                        <th>Routed To</th>
                        <th>Registered</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($recentRegistrations as $p)
                        <tr>
                            <td>
                                <div class="flex items-center gap-2.5">
                                    <div class="avatar-circle bg-primary-100 text-primary-700">
                                        {{ strtoupper(substr($p->name, 0, 1)) }}
                                    </div>
                                    <span class="font-semibold text-gray-800">{{ $p->name }}</span>
                                </div>
                            </td>
                            <td class="text-gray-600 text-xs">{{ $p->diagnosis ?? '—' }}</td>
                            <td>
                                @if($p->doctor)
                                    <span class="badge badge-assigned"><span class="badge-dot bg-emerald-500"></span>Dr. {{ $p->doctor->name }}</span>
                                @else
                                    <span class="badge badge-unassigned"><span class="badge-dot bg-amber-500"></span>Unassigned</span>
                                @endif
                            </td>
                            <td class="text-gray-400 text-xs">{{ $p->admitted_at?->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                <i class="fas fa-user-injured text-3xl mb-2 block opacity-20"></i>
                                No patients registered yet.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>

<script>
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
