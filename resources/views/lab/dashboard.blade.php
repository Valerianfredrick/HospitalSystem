<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lab Dashboard — {{ config('app.name', 'MediCore HMS') }}</title>
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
        body { font-family: 'Inter', sans-serif; }
        .bg-gradient-main { background: linear-gradient(135deg, #6d28d9 0%, #0694a2 100%); }
        .text-gradient  { background: linear-gradient(135deg, #6d28d9 0%, #0694a2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .card-hover     { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .card-hover:hover { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(0,0,0,0.10); }
        .sidebar-link   { transition: background 0.15s, color 0.15s; }
        .sidebar-link:hover, .sidebar-link.active { background: rgba(255,255,255,0.15); border-radius: 0.75rem; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex">

{{-- ── SIDEBAR ──────────────────────────────────────────────── --}}
<aside class="hidden lg:flex flex-col w-64 min-h-screen bg-gradient-main text-white fixed top-0 left-0 z-40">

    <div class="px-6 py-6 border-b border-white/20">
        <span class="text-xl font-bold tracking-tight">{{ config('app.name', 'MediCore HMS') }}</span>
        <p class="text-white/60 text-xs mt-0.5">Laboratory Module</p>
    </div>

    <div class="px-6 py-4 border-b border-white/20 flex items-center gap-3">
        <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold uppercase">
            {{ substr($user->name, 0, 1) }}
        </div>
        <div class="overflow-hidden">
            <p class="text-sm font-semibold truncate">{{ $user->name }}</p>
            <p class="text-white/60 text-xs">Lab Attendant</p>
        </div>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-1 text-sm font-medium">
        <a href="{{ route('lab.dashboard') }}" class="sidebar-link active flex items-center gap-3 px-3 py-2.5 text-white">
            <i class="fas fa-th-large w-4 text-center"></i> Dashboard
        </a>
        <a href="{{ route('lab.requests') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 text-white/80">
            <i class="fas fa-flask w-4 text-center"></i> Test Requests
            @if($stats['pending_tests'] > 0)
                <span class="ml-auto bg-amber-400 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                    {{ $stats['pending_tests'] }}
                </span>
            @endif
        </a>
        <a href="{{ route('lab.requests') }}?status=in_progress" class="sidebar-link flex items-center gap-3 px-3 py-2.5 text-white/80">
            <i class="fas fa-vials w-4 text-center"></i> In Progress
        </a>
        <a href="{{ route('lab.requests') }}?status=completed" class="sidebar-link flex items-center gap-3 px-3 py-2.5 text-white/80">
            <i class="fas fa-file-medical-alt w-4 text-center"></i> Completed
        </a>

        <div class="pt-4 mt-4 border-t border-white/20">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-link w-full text-left flex items-center gap-3 px-3 py-2.5 text-white/80">
                    <i class="fas fa-sign-out-alt w-4 text-center"></i> Logout
                </button>
            </form>
        </div>
    </nav>
</aside>

{{-- ── MAIN CONTENT ──────────────────────────────────────────── --}}
<div class="flex-1 lg:ml-64 flex flex-col min-h-screen">

    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between sticky top-0 z-30">
        <div>
            <h1 class="text-lg font-bold text-gray-800">Lab Dashboard</h1>
            <p class="text-xs text-gray-400">{{ now()->format('l, d F Y') }}</p>
        </div>
        <div class="flex items-center gap-3">
            @if($stats['pending_tests'] > 0)
                <a href="{{ route('lab.requests') }}?status=pending"
                   class="relative p-2 rounded-xl text-amber-500 hover:bg-amber-50 transition">
                    <i class="fas fa-bell"></i>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                </a>
            @endif
            <div class="w-9 h-9 rounded-full bg-gradient-main flex items-center justify-center text-white text-sm font-bold uppercase">
                {{ substr($user->name, 0, 1) }}
            </div>
        </div>
    </header>

    <main class="flex-1 p-6 space-y-6">

        {{-- Welcome banner --}}
        <div class="bg-gradient-main rounded-2xl p-6 text-white flex items-center justify-between">
            <div>
                <p class="text-white/70 text-sm">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},</p>
                <h2 class="text-2xl font-bold">{{ $user->name }}</h2>
                <p class="text-white/70 text-sm mt-1">Here's your lab overview for today.</p>
            </div>
            <div class="hidden sm:flex w-16 h-16 bg-white/20 rounded-2xl items-center justify-center">
                <i class="fas fa-microscope text-white text-2xl"></i>
            </div>
        </div>

        {{-- Stat cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $cards = [
                    ['label' => 'Pending Tests',    'value' => $stats['pending_tests'],    'icon' => 'fa-hourglass-half',      'color' => 'bg-amber-50 text-amber-600',   'ring' => 'ring-amber-200',   'link' => route('lab.requests').'?status=pending'],
                    ['label' => 'Completed Today',  'value' => $stats['completed_today'],  'icon' => 'fa-check-circle',        'color' => 'bg-green-50 text-green-600',   'ring' => 'ring-green-200',   'link' => route('lab.requests').'?status=completed'],
                    ['label' => 'Critical Results', 'value' => $stats['critical_results'], 'icon' => 'fa-exclamation-triangle','color' => 'bg-red-50 text-red-600',       'ring' => 'ring-red-200',     'link' => route('lab.requests').'?status=completed'],
                    ['label' => 'Total Patients',   'value' => $stats['total_patients'],   'icon' => 'fa-user-injured',        'color' => 'bg-primary-50 text-primary-600','ring' => 'ring-primary-200', 'link' => route('lab.requests')],
                ];
            @endphp

            @foreach($cards as $card)
                <a href="{{ $card['link'] }}" class="bg-white rounded-2xl p-5 shadow-sm ring-1 {{ $card['ring'] }} card-hover block">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ $card['label'] }}</span>
                        <div class="w-9 h-9 rounded-xl {{ $card['color'] }} flex items-center justify-center text-sm">
                            <i class="fas {{ $card['icon'] }}"></i>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-gray-800">{{ $card['value'] }}</p>
                </a>
            @endforeach
        </div>

        {{-- Pending requests alert --}}
        @if($stats['pending_tests'] > 0)
            <div class="bg-amber-50 border border-amber-200 rounded-2xl px-6 py-4 flex items-center gap-4">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-hourglass-half text-amber-600"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-amber-800">
                        {{ $stats['pending_tests'] }} pending test{{ $stats['pending_tests'] > 1 ? 's' : '' }} waiting to be processed
                    </p>
                    <p class="text-xs text-amber-600 mt-0.5">Patients are waiting for their results.</p>
                </div>
                <a href="{{ route('lab.requests') }}?status=pending"
                   class="px-4 py-2 bg-amber-500 text-white text-xs font-semibold rounded-xl hover:bg-amber-600 transition flex-shrink-0">
                    Process Now
                </a>
            </div>
        @endif

        {{-- Two-column section --}}
        <div class="grid lg:grid-cols-3 gap-6">

            {{-- Recent test requests table --}}
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 text-sm">Recent Test Requests</h3>
                    <a href="{{ route('lab.requests') }}" class="text-xs text-primary-600 font-medium hover:underline">View all</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                            <th class="px-6 py-3 text-left font-semibold">Patient</th>
                            <th class="px-6 py-3 text-left font-semibold">Test</th>
                            <th class="px-6 py-3 text-left font-semibold">Doctor</th>
                            <th class="px-6 py-3 text-left font-semibold">Requested</th>
                            <th class="px-6 py-3 text-left font-semibold">Status</th>
                            <th class="px-6 py-3 text-left font-semibold"></th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                        @forelse($recentRequests as $req)
                            <tr class="hover:bg-gray-50 transition-colors
                                    {{ $req->status === 'pending' ? 'bg-amber-50/30' : '' }}">

                                {{-- Patient --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gradient-main flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                            {{ strtoupper(substr($req->patient->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800 text-sm">{{ $req->patient->name }}</p>
                                            <p class="text-xs text-gray-400">
                                                {{ ucfirst($req->patient->gender) }}
                                                · {{ $req->patient->age }} yrs
                                                @if($req->patient->ward) · {{ $req->patient->ward }} Ward @endif
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Test --}}
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-700 text-sm">{{ $req->test_name }}</p>
                                    @if($req->clinical_notes)
                                        <p class="text-xs text-gray-400 mt-0.5 truncate max-w-[160px]" title="{{ $req->clinical_notes }}">
                                            {{ $req->clinical_notes }}
                                        </p>
                                    @endif
                                </td>

                                {{-- Doctor --}}
                                <td class="px-6 py-4 text-xs text-gray-500">
                                    Dr. {{ $req->requestedBy->name ?? '—' }}
                                </td>

                                {{-- Time --}}
                                <td class="px-6 py-4 text-xs text-gray-400 whitespace-nowrap">
                                    {{ $req->created_at->diffForHumans() }}
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                            {{ $req->status === 'completed'   ? 'bg-green-100 text-green-700' :
                                               ($req->status === 'in_progress' ? 'bg-blue-100 text-blue-700' :
                                                                                  'bg-amber-100 text-amber-700') }}">
                                            {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                                        </span>
                                    @if($req->result_flag === 'critical')
                                        <span class="ml-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                                Critical
                                            </span>
                                    @endif
                                </td>

                                {{-- Action --}}
                                <td class="px-6 py-4">
                                    <a href="{{ route('lab.show', $req) }}"
                                       class="px-3 py-1.5 text-xs font-semibold rounded-lg transition
                                               {{ $req->status === 'pending'
                                                    ? 'bg-amber-500 text-white hover:bg-amber-600'
                                                    : ($req->status === 'in_progress'
                                                        ? 'bg-blue-500 text-white hover:bg-blue-600'
                                                        : 'border border-gray-200 text-gray-600 hover:bg-gray-50') }}">
                                        {{ $req->status === 'completed' ? 'View' : 'Process' }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">
                                    <i class="fas fa-flask text-3xl mb-3 block opacity-20"></i>
                                    No test requests yet
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Quick actions --}}
            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 text-sm mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('lab.requests') }}?status=pending"
                       class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-50 transition text-left group">
                        <div class="w-9 h-9 rounded-xl text-amber-600 bg-amber-50 flex items-center justify-center text-sm flex-shrink-0">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div class="flex-1">
                            <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">Pending Tests</span>
                            @if($stats['pending_tests'] > 0)
                                <span class="ml-2 text-xs bg-amber-100 text-amber-700 font-semibold px-1.5 py-0.5 rounded-full">
                                    {{ $stats['pending_tests'] }}
                                </span>
                            @endif
                        </div>
                        <i class="fas fa-chevron-right text-xs text-gray-300 group-hover:text-gray-500"></i>
                    </a>

                    <a href="{{ route('lab.requests') }}?status=in_progress"
                       class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-50 transition text-left group">
                        <div class="w-9 h-9 rounded-xl text-blue-600 bg-blue-50 flex items-center justify-center text-sm flex-shrink-0">
                            <i class="fas fa-vials"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">In Progress</span>
                        <i class="fas fa-chevron-right text-xs text-gray-300 ml-auto group-hover:text-gray-500"></i>
                    </a>

                    <a href="{{ route('lab.requests') }}?status=completed"
                       class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-50 transition text-left group">
                        <div class="w-9 h-9 rounded-xl text-green-600 bg-green-50 flex items-center justify-center text-sm flex-shrink-0">
                            <i class="fas fa-file-medical-alt"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">Completed Results</span>
                        <i class="fas fa-chevron-right text-xs text-gray-300 ml-auto group-hover:text-gray-500"></i>
                    </a>

                    <a href="{{ route('lab.requests') }}"
                       class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-50 transition text-left group">
                        <div class="w-9 h-9 rounded-xl text-primary-600 bg-primary-50 flex items-center justify-center text-sm flex-shrink-0">
                            <i class="fas fa-list"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">All Requests</span>
                        <i class="fas fa-chevron-right text-xs text-gray-300 ml-auto group-hover:text-gray-500"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Test status breakdown (live counts) --}}
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 text-sm mb-4">Test Status Breakdown</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @php
                    use App\Models\LabRequest;
                    $breakdown = [
                        ['label' => 'Pending',     'count' => LabRequest::pending()->count(),     'color' => 'bg-amber-400', 'link' => route('lab.requests').'?status=pending'],
                        ['label' => 'In Progress', 'count' => LabRequest::inProgress()->count(),  'color' => 'bg-blue-400',  'link' => route('lab.requests').'?status=in_progress'],
                        ['label' => 'Completed',   'count' => LabRequest::completed()->count(),   'color' => 'bg-green-400', 'link' => route('lab.requests').'?status=completed'],
                        ['label' => 'Critical',    'count' => LabRequest::where('result_flag','critical')->count(), 'color' => 'bg-red-400', 'link' => route('lab.requests').'?status=completed'],
                    ];
                @endphp
                @foreach($breakdown as $s)
                    <a href="{{ $s['link'] }}"
                       class="flex items-center gap-3 p-4 rounded-xl bg-gray-50 hover:bg-gray-100 transition">
                        <div class="w-3 h-3 rounded-full {{ $s['color'] }} flex-shrink-0"></div>
                        <div>
                            <p class="text-xs text-gray-500">{{ $s['label'] }}</p>
                            <p class="text-xl font-bold text-gray-800">{{ $s['count'] }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

    </main>

    <footer class="px-6 py-4 text-xs text-gray-400 border-t border-gray-100 text-center">
        {{ config('app.name', 'MediCore HMS') }} &copy; {{ date('Y') }} — Laboratory Module
    </footer>
</div>

</body>
</html>
