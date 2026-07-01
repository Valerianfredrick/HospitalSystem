<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $ward->name }} — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50:'#f5f3ff',100:'#ede9fe',200:'#ddd6fe',500:'#8b5cf6',600:'#7c3aed',700:'#6d28d9' },
                        secondary: { 50:'#edfafa',100:'#d5f5f6',500:'#0694a2',600:'#047481' },
                    }
                }
            }
        }
    </script>
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        body { background: #f8f7ff; }
        .sidebar { width: 260px; min-height: 100vh; background: #1e1035; position: fixed; left: 0; top: 0; z-index: 50; display: flex; flex-direction: column; }
        .main-content { margin-left: 260px; min-height: 100vh; }
        .nav-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.65rem 0.85rem; border-radius: 10px; color: rgba(255,255,255,0.55); font-size: 0.875rem; font-weight: 500; text-decoration: none; margin-bottom: 2px; transition: all 0.2s; }
        .nav-item:hover { background: rgba(255,255,255,0.08); color: white; }
        .nav-item.active { background: linear-gradient(135deg, #7c3aed 0%, #047481 100%); color: white; }
        .nav-item .icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.08); font-size: 0.8rem; }
        .nav-section-label { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.1em; color: rgba(255,255,255,0.3); padding: 0.5rem 0.75rem; margin-top: 0.5rem; text-transform: uppercase; }
        .bg-gradient-main { background: linear-gradient(135deg, #6d28d9 0%, #0694a2 100%); }
        .stat-card { background: white; border-radius: 16px; padding: 1.25rem; border: 1px solid #ede9fe; position: relative; overflow: hidden; }
        .stat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(135deg, #7c3aed 0%, #0694a2 100%); }
        .bed-card { background: white; border: 1px solid #ede9fe; border-radius: 14px; padding: 1.1rem; transition: all 0.2s; }
        .bed-card.available { border-color: #a7f3d0; }
        .bed-card.occupied { border-color: #ddd6fe; }
        .bed-card.maintenance { border-color: #fde68a; }
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 999px; font-size: 0.7rem; font-weight: 600; }
        .badge-available { background: #d1fae5; color: #065f46; }
        .badge-occupied { background: #ede9fe; color: #5b21b6; }
        .badge-maintenance { background: #fef3c7; color: #92400e; }
        .badge-dot { width: 5px; height: 5px; border-radius: 50%; }
    </style>
</head>
<body>

<aside class="sidebar">
    <div style="padding: 1.5rem 1.5rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.07);">
        <a href="{{ url('/') }}" class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-gradient-main flex items-center justify-center">
                <i class="fas fa-heartbeat text-white text-sm"></i>
            </div>
            <span class="text-white font-bold text-lg">{{ config('app.name', 'MediCore') }}</span>
        </a>
        <p class="text-xs mt-1 ml-10" style="color:rgba(255,255,255,0.3)">Medical Dashboard</p>
    </div>
    <nav style="flex:1; padding: 1.25rem 0.75rem; overflow-y:auto;">
        <p class="nav-section-label">Main</p>
        <a href="{{ route('medical.dashboard') }}" class="nav-item"><span class="icon"><i class="fas fa-th-large"></i></span> Dashboard</a>
        <a href="{{ route('patients.index') }}" class="nav-item"><span class="icon"><i class="fas fa-user-injured"></i></span> All Patients</a>
        <a href="{{ route('patients.admission') }}" class="nav-item"><span class="icon"><i class="fas fa-bed"></i></span> Admissions</a>
        <a href="{{ route('patients.discharge') }}" class="nav-item"><span class="icon"><i class="fas fa-sign-out-alt"></i></span> Discharges</a>
        <p class="nav-section-label">Clinical</p>
        <a href="#" class="nav-item"><span class="icon"><i class="fas fa-notes-medical"></i></span> Clinical Notes</a>
        <a href="#" class="nav-item"><span class="icon"><i class="fas fa-file-prescription"></i></span> Prescriptions</a>
        <p class="nav-section-label">Ward</p>
        <a href="{{ route('wards.index') }}" class="nav-item active"><span class="icon"><i class="fas fa-procedures"></i></span> Ward Overview</a>
        <a href="{{ route('patients.create') }}" class="nav-item"><span class="icon"><i class="fas fa-user-plus"></i></span> Register Patient</a>
    </nav>
    <div style="padding: 1rem 0.75rem; border-top: 1px solid rgba(255,255,255,0.07);">
        <div class="flex items-center gap-3" style="padding:0.75rem; border-radius:10px; background:rgba(255,255,255,0.05);">
            <div class="flex-1 min-w-0">
                <p class="text-white text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs truncate capitalize" style="color:rgba(255,255,255,0.4)">{{ auth()->user()->role }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm" style="color:rgba(255,255,255,0.4)" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

<div class="main-content">
    <header style="background:white; border-bottom:1px solid #ede9fe; padding:0 2rem; height:64px; display:flex; align-items:center; gap:1rem; position:sticky; top:0; z-index:40;">
        <a href="{{ route('wards.index') }}" class="text-gray-400 hover:text-primary-600">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="font-bold text-gray-800">{{ $ward->name }}</h1>
            @if($ward->description)
                <p class="text-xs text-gray-400">{{ $ward->description }}</p>
            @endif
        </div>
    </header>

    <main class="p-6 space-y-6">

        @if(session('success'))
            <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
            </div>
        @endif

        @php
            $total       = $ward->beds->count();
            $occupied    = $ward->beds->where('status', 'occupied')->count();
            $available   = $ward->beds->where('status', 'available')->count();
            $maintenance = $ward->beds->where('status', 'maintenance')->count();
        @endphp

        {{-- Ward summary --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                ['label' => 'Total Beds',  'value' => $total,       'icon' => 'fa-bed',          'color' => 'text-primary-600 bg-primary-100'],
                ['label' => 'Occupied',    'value' => $occupied,    'icon' => 'fa-user-injured', 'color' => 'text-purple-600 bg-purple-100'],
                ['label' => 'Available',   'value' => $available,   'icon' => 'fa-circle-check', 'color' => 'text-emerald-600 bg-emerald-100'],
                ['label' => 'Maintenance', 'value' => $maintenance, 'icon' => 'fa-screwdriver-wrench', 'color' => 'text-amber-600 bg-amber-100'],
            ] as $card)
                <div class="stat-card">
                    <div class="w-9 h-9 rounded-xl {{ $card['color'] }} flex items-center justify-center mb-3">
                        <i class="fas {{ $card['icon'] }} text-sm"></i>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">{{ $card['value'] }}</p>
                    <p class="text-xs font-semibold text-gray-500 mt-0.5">{{ $card['label'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Bed grid --}}
        <div>
            <h2 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wider">Beds</h2>

            @if($ward->beds->isEmpty())
                <div class="bg-white rounded-2xl border border-primary-100 p-12 text-center text-gray-400">
                    <i class="fas fa-bed text-4xl mb-3 block opacity-20"></i>
                    No beds set up in this ward yet.
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($ward->beds->sortBy('bed_number') as $bed)
                        <div class="bed-card {{ $bed->status }}">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <p class="font-bold text-gray-800">Bed {{ $bed->bed_number }}</p>
                                    @if($bed->status === 'available')
                                        <span class="badge badge-available mt-1"><span class="badge-dot bg-emerald-500"></span>Available</span>
                                    @elseif($bed->status === 'occupied')
                                        <span class="badge badge-occupied mt-1"><span class="badge-dot bg-purple-500"></span>Occupied</span>
                                    @else
                                        <span class="badge badge-maintenance mt-1"><span class="badge-dot bg-amber-500"></span>Maintenance</span>
                                    @endif
                                </div>
                                <i class="fas fa-bed text-gray-200 text-xl"></i>
                            </div>

                            @if($bed->patient)
                                <a href="{{ route('patients.show', $bed->patient) }}"
                                   class="block text-sm text-primary-600 hover:text-primary-700 font-semibold mb-3">
                                    {{ $bed->patient->name }}
                                </a>
                            @else
                                <p class="text-sm text-gray-300 mb-3">No patient assigned</p>
                            @endif

                            <div class="flex gap-2">
                                @if($bed->status === 'maintenance')
                                    <form method="POST" action="{{ route('wards.beds.clear_maintenance', $bed) }}" class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="w-full text-xs font-semibold text-emerald-600 border border-emerald-200 rounded-lg px-3 py-2 hover:bg-emerald-50 transition-colors">
                                            Clear maintenance
                                        </button>
                                    </form>
                                @elseif($bed->status === 'available')
                                    <form method="POST" action="{{ route('wards.beds.maintenance', $bed) }}" class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="w-full text-xs font-semibold text-amber-600 border border-amber-200 rounded-lg px-3 py-2 hover:bg-amber-50 transition-colors">
                                            Set maintenance
                                        </button>
                                    </form>
                                @else
                                    <p class="text-xs text-gray-300 flex-1">Discharge patient to free this bed.</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </main>
</div>
</body>
</html>
