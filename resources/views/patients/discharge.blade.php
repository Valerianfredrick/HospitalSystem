<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discharged Patients</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        body { background: #f8f7ff; }
        .sidebar { width: 260px; min-height: 100vh; background: #1e1035; position: fixed; left: 0; top: 0; z-index: 50; display: flex; flex-direction: column; }
        .main-content { margin-left: 260px; min-height: 100vh; }
        .nav-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.65rem 0.85rem; border-radius: 10px; color: rgba(255,255,255,0.55); font-size: 0.875rem; font-weight: 500; text-decoration: none; margin-bottom: 2px; transition: all 0.2s; }
        .nav-item:hover { background: rgba(255,255,255,0.08); color: white; }
        .nav-item.active { background: linear-gradient(135deg, #7c3aed 0%, #047481 100%); color: white; }
        .nav-item .icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.08); font-size: 0.8rem; }
        .bg-gradient-main { background: linear-gradient(135deg, #6d28d9 0%, #0694a2 100%); }
    </style>
</head>
<body>

<aside class="sidebar">
    <div style="padding:1.5rem 1.5rem 1rem; border-bottom:1px solid rgba(255,255,255,0.07);">
        <a href="{{ url('/') }}" class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-gradient-main flex items-center justify-center">
                <i class="fas fa-heartbeat text-white text-sm"></i>
            </div>
            <span class="text-white font-bold text-lg">{{ config('app.name', 'MediCore') }}</span>
        </a>
    </div>
    <nav style="flex:1; padding:1.25rem 0.75rem; overflow-y:auto;">
        <a href="{{ route('medical.dashboard') }}" class="nav-item"><span class="icon"><i class="fas fa-th-large"></i></span> Dashboard</a>
        <a href="{{ route('patients.index') }}" class="nav-item"><span class="icon"><i class="fas fa-user-injured"></i></span> All Patients</a>
        <a href="{{ route('patients.admission') }}" class="nav-item"><span class="icon"><i class="fas fa-bed"></i></span> Admissions</a>
        <a href="{{ route('patients.discharge') }}" class="nav-item active"><span class="icon"><i class="fas fa-sign-out-alt"></i></span> Discharges</a>
        <a href="{{ route('patients.create') }}" class="nav-item"><span class="icon"><i class="fas fa-user-plus"></i></span> Register Patient</a>
    </nav>
    <div style="padding:1rem 0.75rem; border-top:1px solid rgba(255,255,255,0.07);">
        <div class="flex items-center gap-3" style="padding:0.75rem; border-radius:10px; background:rgba(255,255,255,0.05);">
            <div class="flex-1 min-w-0">
                <p class="text-white text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs truncate capitalize" style="color:rgba(255,255,255,0.4)">{{ auth()->user()->role }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="color:rgba(255,255,255,0.4)"><i class="fas fa-sign-out-alt"></i></button>
            </form>
        </div>
    </div>
</aside>

<div class="main-content">
    <header style="background:white; border-bottom:1px solid #ede9fe; padding:0 2rem; height:64px; display:flex; align-items:center; position:sticky; top:0; z-index:40;">
        <div>
            <h1 class="font-bold text-gray-800">Discharged Patients</h1>
            <p class="text-xs text-gray-400">{{ $patients->total() }} total discharged</p>
        </div>
    </header>

    <main class="p-6">

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 flex items-center gap-2">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if($patients->isEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-sign-out-alt text-gray-400 text-2xl"></i>
                </div>
                <p class="text-gray-500 font-medium">No discharged patients yet</p>
            </div>
        @else
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Patient</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Condition</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Ward</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Discharged At</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($patients as $patient)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gradient-main flex items-center justify-center text-white text-xs font-bold">
                                            {{ strtoupper(substr($patient->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-800">{{ $patient->name }}</div>
                                            <div class="text-xs text-gray-400">{{ ucfirst($patient->gender) }} · {{ $patient->age }} yrs</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3">
                                    @php
                                        $colors = [
                                            'recovered'      => 'bg-green-100 text-green-700',
                                            'improved'       => 'bg-blue-100 text-blue-700',
                                            'transferred'    => 'bg-yellow-100 text-yellow-700',
                                            'self-discharge' => 'bg-orange-100 text-orange-700',
                                            'deceased'       => 'bg-red-100 text-red-700',
                                        ];
                                        $color = $colors[$patient->discharge_condition ?? ''] ?? 'bg-gray-100 text-gray-700';
                                    @endphp
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $color }}">
                                        {{ ucfirst(str_replace('-', ' ', $patient->discharge_condition ?? '—')) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-gray-600">{{ $patient->ward ?? '—' }}</td>
                                <td class="px-5 py-3 text-gray-500">
                                    {{ $patient->discharged_at?->format('d M Y, H:i') ?? '—' }}
                                </td>
                                <td class="px-5 py-3">
                                    <a href="{{ route('patients.show', $patient) }}"
                                       class="text-purple-600 hover:text-purple-800 font-medium text-xs">
                                        View Record →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-4 border-t border-gray-100">
                    {{ $patients->links() }}
                </div>
            </div>
        @endif
    </main>
</div>

</body>
</html>
