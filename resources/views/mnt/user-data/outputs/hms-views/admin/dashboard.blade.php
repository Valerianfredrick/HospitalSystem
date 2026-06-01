<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard — {{ config('app.name', 'MediCore HMS') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50:'#f5f3ff',100:'#ede9fe',200:'#ddd6fe',300:'#c4b5fd',
                            400:'#a78bfa',500:'#8b5cf6',600:'#7c3aed',700:'#6d28d9',
                            800:'#5b21b6',900:'#4c1d95',
                        },
                        secondary: {
                            50:'#edfafa',100:'#d5f5f6',200:'#afecef',300:'#7edce2',
                            400:'#16bdca',500:'#0694a2',600:'#047481',700:'#036672',
                            800:'#05505c',900:'#014451',
                        },
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        * { font-family: 'Inter', sans-serif; }
        .bg-gradient-main { background: linear-gradient(135deg, #6d28d9 0%, #0694a2 100%); }
        .text-gradient { background: linear-gradient(135deg, #6d28d9, #0694a2); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .sidebar { transition: transform 0.3s ease; }
        .nav-link { transition: all 0.15s ease; }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.12); }
        .nav-link.active { border-left: 3px solid white; }
        .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 12px 30px -8px rgba(0,0,0,0.12); }
        .bar { transition: height 0.6s cubic-bezier(0.34,1.56,0.64,1); }
        .progress-fill { transition: width 1s ease; }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #c4b5fd; border-radius: 99px; }
        .badge { display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 9999px; font-size: 0.7rem; font-weight: 600; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

{{-- ─── LAYOUT ────────────────────────────────────────── --}}
<div class="flex min-h-screen">

    {{-- ─── SIDEBAR ────────────────────────────────────── --}}
    <aside id="sidebar" class="sidebar fixed inset-y-0 left-0 z-40 w-64 bg-gradient-main flex flex-col shadow-2xl lg:relative lg:translate-x-0 -translate-x-full">

        {{-- Logo --}}
        <div class="px-6 py-5 border-b border-white/10">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-hospital text-white text-sm"></i>
                </div>
                <span class="text-white font-bold text-lg leading-tight">{{ config('app.name', 'MediCore') }}</span>
            </a>
        </div>

        {{-- Admin badge --}}
        <div class="px-6 py-4 border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-white font-semibold text-sm truncate">{{ auth()->user()->name }}</p>
                    <p class="text-white/60 text-xs">Administrator</p>
                </div>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
            <p class="text-white/40 text-xs font-semibold uppercase tracking-widest px-3 mb-2">Main</p>

            <a href="{{ route('admin.dashboard') }}" class="nav-link active flex items-center gap-3 px-3 py-2.5 rounded-xl text-white text-sm font-medium">
                <i class="fas fa-th-large w-4 text-center"></i> Dashboard
            </a>
            <a href="{{ route('admin.users') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-white/70 hover:text-white text-sm font-medium">
                <i class="fas fa-users w-4 text-center"></i> Users
            </a>

            <p class="text-white/40 text-xs font-semibold uppercase tracking-widest px-3 mt-4 mb-2">Clinical</p>
            <a href="#" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-white/70 hover:text-white text-sm font-medium">
                <i class="fas fa-procedures w-4 text-center"></i> Patients
            </a>
            <a href="#" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-white/70 hover:text-white text-sm font-medium">
                <i class="fas fa-bed w-4 text-center"></i> Wards
            </a>
            <a href="{{ route('medical.dashboard') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-white/70 hover:text-white text-sm font-medium">
                <i class="fas fa-stethoscope w-4 text-center"></i> Medical
            </a>

            <p class="text-white/40 text-xs font-semibold uppercase tracking-widest px-3 mt-4 mb-2">Operations</p>
            <a href="{{ route('pharmacy.index') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-white/70 hover:text-white text-sm font-medium">
                <i class="fas fa-pills w-4 text-center"></i> Pharmacy
                @if(($stats['low_stock_items'] ?? 0) > 0)
                    <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 leading-none">{{ $stats['low_stock_items'] }}</span>
                @endif
            </a>
            <a href="#" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-white/70 hover:text-white text-sm font-medium">
                <i class="fas fa-chart-bar w-4 text-center"></i> Reports
            </a>
            <a href="#" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-white/70 hover:text-white text-sm font-medium">
                <i class="fas fa-cog w-4 text-center"></i> Settings
            </a>
        </nav>

        {{-- Logout --}}
        <div class="p-4 border-t border-white/10">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-white/70 hover:text-white hover:bg-white/10 text-sm font-medium transition-colors">
                    <i class="fas fa-sign-out-alt w-4 text-center"></i> Log out
                </button>
            </form>
        </div>
    </aside>

    {{-- ─── MAIN CONTENT ────────────────────────────────── --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- Top bar --}}
        <header class="bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-4">
                {{-- Mobile sidebar toggle --}}
                <button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')"
                        class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Admin Dashboard</h1>
                    <p class="text-xs text-gray-400">{{ now()->format('l, F j Y') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                {{-- Low stock alert bell --}}
                @if(($stats['low_stock_items'] ?? 0) > 0)
                    <div class="relative">
                        <button class="p-2 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition-colors">
                            <i class="fas fa-bell"></i>
                        </button>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center leading-none">{{ $stats['low_stock_items'] }}</span>
                    </div>
                @endif
                <div class="hidden sm:flex items-center gap-2 bg-gray-50 rounded-xl px-3 py-2 text-sm text-gray-500 border border-gray-100">
                    <i class="fas fa-search text-xs"></i>
                    <span>Search…</span>
                </div>
                <a href="{{ route('admin.users') }}" class="hidden sm:flex items-center gap-2 px-4 py-2 bg-gradient-main text-white text-sm font-medium rounded-xl hover:opacity-90 transition-opacity">
                    <i class="fas fa-user-plus text-xs"></i> Add User
                </a>
            </div>
        </header>

        {{-- Scrollable page content --}}
        <main class="flex-1 overflow-y-auto p-6 space-y-6">

            {{-- ── STAT CARDS ──────────────────────────────── --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4">

                {{-- Total patients --}}
                <div class="stat-card bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                            <i class="fas fa-procedures text-blue-500 text-sm"></i>
                        </div>
                        <span class="badge bg-blue-50 text-blue-600">Total</span>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_patients']) }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Total patients</p>
                    <p class="text-xs text-green-600 mt-2 font-medium"><i class="fas fa-arrow-up text-xs mr-1"></i>{{ $stats['new_this_month'] }} this month</p>
                </div>

                {{-- Inpatients --}}
                <div class="stat-card bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center">
                            <i class="fas fa-bed text-violet-500 text-sm"></i>
                        </div>
                        <span class="badge bg-violet-50 text-violet-600">Live</span>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['inpatients'] }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Admitted now</p>
                    <div class="mt-2 flex items-center gap-1.5">
                        <div class="flex-1 bg-gray-100 rounded-full h-1.5">
                            <div class="progress-fill bg-violet-500 h-1.5 rounded-full" style="width:{{ $stats['bed_occupancy'] }}%"></div>
                        </div>
                        <span class="text-xs text-gray-500">{{ $stats['bed_occupancy'] }}%</span>
                    </div>
                </div>

                {{-- Doctors --}}
                <div class="stat-card bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <i class="fas fa-user-md text-emerald-500 text-sm"></i>
                        </div>
                        <span class="badge bg-emerald-50 text-emerald-600">Active</span>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['doctors'] }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Doctors on staff</p>
                    <p class="text-xs text-gray-400 mt-2">{{ $stats['staff'] }} total active staff</p>
                </div>

                {{-- Low stock --}}
                <div class="stat-card bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl {{ $stats['low_stock_items'] > 0 ? 'bg-red-50' : 'bg-gray-50' }} flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle {{ $stats['low_stock_items'] > 0 ? 'text-red-500' : 'text-gray-400' }} text-sm"></i>
                        </div>
                        @if($stats['low_stock_items'] > 0)
                            <span class="badge bg-red-50 text-red-600">Alert</span>
                        @else
                            <span class="badge bg-green-50 text-green-600">OK</span>
                        @endif
                    </div>
                    <p class="text-2xl font-bold {{ $stats['low_stock_items'] > 0 ? 'text-red-600' : 'text-gray-800' }}">{{ $stats['low_stock_items'] }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Low stock items</p>
                    <a href="{{ route('pharmacy.index') }}" class="text-xs text-primary-600 mt-2 inline-flex items-center gap-1 hover:underline">
                        View pharmacy <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>

            {{-- ── ROW 2: Chart + Ward Occupancy ───────────── --}}
            <div class="grid grid-cols-1 xl:grid-cols-5 gap-4">

                {{-- Patient flow bar chart (7 days) --}}
                <div class="xl:col-span-3 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h2 class="font-bold text-gray-800">Patient Flow — Last 7 Days</h2>
                            <p class="text-xs text-gray-400 mt-0.5">Admissions vs Discharges</p>
                        </div>
                        <div class="flex items-center gap-4 text-xs text-gray-500">
                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-primary-400 inline-block"></span>Admitted</span>
                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-secondary-400 inline-block"></span>Discharged</span>
                        </div>
                    </div>

                    <div class="flex items-end gap-2 h-36">
                        @foreach($daily_flow as $day)
                            @php
                                $admH = $max_daily > 0 ? round(($day->admitted / $max_daily) * 100) : 0;
                                $disH = $max_daily > 0 ? round(($day->discharged / $max_daily) * 100) : 0;
                            @endphp
                            <div class="flex-1 flex flex-col items-center gap-1">
                                <div class="w-full flex items-end gap-0.5 justify-center" style="height:120px">
                                    <div class="bar flex-1 rounded-t-sm bg-primary-400 opacity-90"
                                         style="height:{{ $admH }}%; min-height:2px"
                                         title="Admitted: {{ $day->admitted }}"></div>
                                    <div class="bar flex-1 rounded-t-sm bg-secondary-400 opacity-90"
                                         style="height:{{ $disH }}%; min-height:2px"
                                         title="Discharged: {{ $day->discharged }}"></div>
                                </div>
                                <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($day->date)->format('D') }}</span>
                            </div>
                        @endforeach

                        @if($daily_flow->isEmpty())
                            <div class="w-full flex items-center justify-center h-full text-gray-300 text-sm">
                                <i class="fas fa-chart-bar mr-2"></i> No data yet
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Ward occupancy --}}
                <div class="xl:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h2 class="font-bold text-gray-800">Ward Occupancy</h2>
                            <p class="text-xs text-gray-400 mt-0.5">Beds: occupied / total</p>
                        </div>
                        <span class="badge bg-violet-50 text-violet-600">{{ $stats['bed_occupancy'] }}% overall</span>
                    </div>
                    <div class="space-y-3.5">
                        @foreach($ward_stats as $ward)
                            @php $pct = $ward->capacity > 0 ? round(($ward->occupied / $ward->capacity) * 100) : 0; @endphp
                            <div>
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="font-medium text-gray-700">{{ $ward->name }}</span>
                                    <span class="text-gray-400">{{ $ward->occupied }}/{{ $ward->capacity }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="progress-fill h-2 rounded-full
                                        {{ $pct >= 90 ? 'bg-red-500' : ($pct >= 70 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                         style="width:{{ $pct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ── ROW 3: Critical Stock + Recent Activity ──── --}}
            <div class="grid grid-cols-1 xl:grid-cols-5 gap-4">

                {{-- Critical stock --}}
                <div class="xl:col-span-3 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                        <div>
                            <h2 class="font-bold text-gray-800">Critical Stock</h2>
                            <p class="text-xs text-gray-400 mt-0.5">Low quantity or expiring within 30 days</p>
                        </div>
                        <a href="{{ route('pharmacy.index') }}" class="text-xs text-primary-600 hover:underline font-medium">View all</a>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @forelse($critical_stock as $item)
                            <div class="px-6 py-3 flex items-center gap-4 hover:bg-gray-50 transition-colors">
                                <div class="w-9 h-9 rounded-xl {{ $item->quantity <= 0 ? 'bg-red-50' : 'bg-amber-50' }} flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-pills {{ $item->quantity <= 0 ? 'text-red-500' : 'text-amber-500' }} text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">{{ $item->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $item->category ?? 'Medicine' }}</p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-sm font-bold {{ $item->quantity <= 0 ? 'text-red-600' : 'text-amber-600' }}">
                                        {{ $item->quantity }} {{ $item->unit }}
                                    </p>
                                    @if($item->expires_at ?? false)
                                        <p class="text-xs text-gray-400">Exp {{ \Carbon\Carbon::parse($item->expires_at)->format('M d') }}</p>
                                    @endif
                                </div>
                                @if($item->quantity <= 0)
                                    <span class="badge bg-red-50 text-red-600">Out</span>
                                @else
                                    <span class="badge bg-amber-50 text-amber-600">Low</span>
                                @endif
                            </div>
                        @empty
                            <div class="px-6 py-8 text-center text-gray-300">
                                <i class="fas fa-check-circle text-2xl mb-2 text-green-400 block"></i>
                                <p class="text-sm">All stock levels are healthy</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Recent activity --}}
                <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-50">
                        <h2 class="font-bold text-gray-800">Recent Activity</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Today's events</p>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @forelse($recent_activity as $event)
                            <div class="px-5 py-3.5 flex items-start gap-3 hover:bg-gray-50 transition-colors">
                                <div class="w-7 h-7 rounded-full {{ $event['color'] }} flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <i class="fas {{ $event['icon'] }} text-white text-xs"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-700 leading-snug">{{ $event['message'] }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $event['time'] }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-8 text-center text-gray-300">
                                <i class="fas fa-clock text-2xl mb-2 block"></i>
                                <p class="text-sm">No activity today yet</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ── ROW 4: Quick Actions ─────────────────────── --}}
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h2 class="font-bold text-gray-800 mb-4">Quick Actions</h2>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <a href="{{ route('admin.users') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-dashed border-gray-200 hover:border-primary-300 hover:bg-primary-50 transition-all group">
                        <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center group-hover:bg-primary-200 transition-colors">
                            <i class="fas fa-user-plus text-primary-600 text-sm"></i>
                        </div>
                        <span class="text-xs font-medium text-gray-600 group-hover:text-primary-700 text-center">Manage Users</span>
                    </a>
                    <a href="{{ route('pharmacy.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-dashed border-gray-200 hover:border-secondary-300 hover:bg-secondary-50 transition-all group">
                        <div class="w-10 h-10 rounded-xl bg-secondary-100 flex items-center justify-center group-hover:bg-secondary-200 transition-colors">
                            <i class="fas fa-pills text-secondary-600 text-sm"></i>
                        </div>
                        <span class="text-xs font-medium text-gray-600 group-hover:text-secondary-700 text-center">Pharmacy</span>
                    </a>
                    <a href="{{ route('medical.dashboard') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-dashed border-gray-200 hover:border-emerald-300 hover:bg-emerald-50 transition-all group">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center group-hover:bg-emerald-200 transition-colors">
                            <i class="fas fa-stethoscope text-emerald-600 text-sm"></i>
                        </div>
                        <span class="text-xs font-medium text-gray-600 group-hover:text-emerald-700 text-center">Medical</span>
                    </a>
                    <a href="#" class="flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-dashed border-gray-200 hover:border-amber-300 hover:bg-amber-50 transition-all group">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center group-hover:bg-amber-200 transition-colors">
                            <i class="fas fa-chart-line text-amber-600 text-sm"></i>
                        </div>
                        <span class="text-xs font-medium text-gray-600 group-hover:text-amber-700 text-center">Reports</span>
                    </a>
                </div>
            </div>

        </main>
    </div>
</div>

{{-- Mobile sidebar overlay --}}
<div id="overlay" class="lg:hidden fixed inset-0 bg-black/40 z-30 hidden" onclick="closeSidebar()"></div>

<script>
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('overlay');

    document.querySelector('[onclick*="classList.toggle"]').addEventListener('click', function () {
        const hidden = sidebar.classList.contains('-translate-x-full');
        if (hidden) {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        } else {
            closeSidebar();
        }
    });

    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }

    // Animate progress bars after paint
    window.addEventListener('load', () => {
        document.querySelectorAll('.progress-fill').forEach(el => {
            const w = el.style.width;
            el.style.width = '0';
            requestAnimationFrame(() => { el.style.width = w; });
        });
    });
</script>
</body>
</html>
