<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription Queue — Pharmacy</title>
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
        .pulse { animation: pulse 2s infinite; }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.5} }
    </style>
</head>
<body>

{{-- SIDEBAR --}}
<aside class="sidebar">
    <div style="padding:1.5rem 1.5rem 1rem; border-bottom:1px solid rgba(255,255,255,0.07);">
        <a href="{{ url('/') }}" class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-gradient-main flex items-center justify-center">
                <i class="fas fa-heartbeat text-white text-sm"></i>
            </div>
            <span class="text-white font-bold text-lg">{{ config('app.name', 'MediCore') }}</span>
        </a>
        <p class="text-xs mt-1 ml-10" style="color:rgba(255,255,255,0.3)">Pharmacy</p>
    </div>
    <nav style="flex:1; padding:1.25rem 0.75rem; overflow-y:auto;">
        <a href="{{ route('pharmacy.index') }}" class="nav-item">
            <span class="icon"><i class="fas fa-pills"></i></span> Stock Inventory
        </a>
        <a href="{{ route('pharmacy.prescriptions') }}" class="nav-item active">
            <span class="icon"><i class="fas fa-file-prescription"></i></span>
            Prescriptions
            @if($pendingRx->total() > 0)
                <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full pulse">
                    {{ $pendingRx->total() }}
                </span>
            @endif
        </a>
        <a href="{{ route('pharmacy.create') }}" class="nav-item">
            <span class="icon"><i class="fas fa-plus"></i></span> Add Medicine
        </a>
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

{{-- MAIN --}}
<div class="main-content">
    <header style="background:white; border-bottom:1px solid #ede9fe; padding:0 2rem; height:64px; display:flex; align-items:center; gap:1rem; position:sticky; top:0; z-index:40;">
        <div>
            <h1 class="font-bold text-gray-800">Prescription Queue</h1>
            <p class="text-xs text-gray-400">Dispense medicines to patients</p>
        </div>
    </header>

    <main class="p-6 space-y-8">

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 flex items-center gap-2">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        {{-- ── PENDING PRESCRIPTIONS ── --}}
        <section>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-3 h-3 bg-red-500 rounded-full pulse"></div>
                <h2 class="font-bold text-gray-800 text-lg">
                    Pending Dispensing
                    <span class="ml-2 bg-red-100 text-red-700 text-sm font-bold px-2.5 py-0.5 rounded-full">
                        {{ $pendingRx->total() }}
                    </span>
                </h2>
            </div>

            @if($pendingRx->isEmpty())
                <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check text-green-500 text-2xl"></i>
                    </div>
                    <p class="text-gray-500 font-medium">All prescriptions dispensed!</p>
                    <p class="text-gray-400 text-sm mt-1">No pending prescriptions at the moment.</p>
                </div>
            @else
                <div class="grid gap-4">
                    @foreach($pendingRx as $rx)
                        <div class="bg-white rounded-2xl border-2 border-orange-100 p-5 flex flex-col sm:flex-row sm:items-center gap-4">

                            {{-- Patient avatar --}}
                            <div class="w-12 h-12 rounded-full bg-gradient-main flex items-center justify-center text-white font-bold text-lg shrink-0">
                                {{ strtoupper(substr($rx->patient->name ?? '?', 0, 1)) }}
                            </div>

                            {{-- Details --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <span class="font-bold text-gray-800">{{ $rx->patient->name ?? 'Unknown' }}</span>
                                    @if($rx->patient->ward)
                                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">
                                            {{ $rx->patient->ward }} Ward
                                        </span>
                                    @endif
                                    <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full font-semibold">
                                        PENDING
                                    </span>
                                </div>

                                {{-- Medicine info --}}
                                <div class="flex flex-wrap gap-4 text-sm text-gray-600 mt-1">
                                    <span class="flex items-center gap-1.5">
                                        <i class="fas fa-pills text-purple-400"></i>
                                        <strong class="text-gray-800">{{ $rx->medication_name }}</strong>
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <i class="fas fa-syringe text-blue-400"></i>
                                        {{ $rx->dosage }}
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <i class="fas fa-clock text-green-400"></i>
                                        {{ $rx->frequency }}
                                    </span>
                                    @if($rx->duration_days)
                                        <span class="flex items-center gap-1.5">
                                            <i class="fas fa-calendar text-orange-400"></i>
                                            {{ $rx->duration_days }} days
                                        </span>
                                    @endif
                                </div>

                                @if($rx->instructions)
                                    <p class="text-xs text-gray-400 mt-1.5 italic">
                                        <i class="fas fa-info-circle mr-1"></i>{{ $rx->instructions }}
                                    </p>
                                @endif

                                <p class="text-xs text-gray-400 mt-1">
                                    Prescribed by <strong>{{ $rx->doctor->name ?? 'Unknown' }}</strong>
                                    · {{ $rx->created_at->diffForHumans() }}
                                </p>
                            </div>

                            {{-- Dispense button --}}
                            <form method="POST"
                                  action="{{ route('pharmacy.dispense', $rx) }}"
                                  onsubmit="return confirm('Confirm dispensing {{ $rx->medication_name }} to {{ $rx->patient->name ?? 'this patient' }}?')">
                                @csrf
                                @method('PUT')
                                <button type="submit"
                                        class="whitespace-nowrap px-5 py-2.5 bg-gradient-main text-white text-sm font-semibold rounded-xl hover:opacity-90 transition-opacity flex items-center gap-2">
                                    <i class="fas fa-check-circle"></i> Mark as Dispensed
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">{{ $pendingRx->links() }}</div>
            @endif
        </section>

        {{-- ── DISPENSED HISTORY ── --}}
        <section>
            <h2 class="font-bold text-gray-800 text-lg mb-4 flex items-center gap-2">
                <i class="fas fa-history text-gray-400"></i>
                Dispensed History
            </h2>

            @if($dispensedRx->isEmpty())
                <div class="bg-white rounded-2xl border border-gray-100 p-8 text-center">
                    <p class="text-gray-400 text-sm">No dispensed prescriptions yet.</p>
                </div>
            @else
                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="border-b border-gray-100 text-left">
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Patient</th>
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Medication</th>
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Dosage</th>
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Dispensed By</th>
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Dispensed At</th>
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                        @foreach($dispensedRx as $rx)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3">
                                    <div class="font-semibold text-gray-800">{{ $rx->patient->name ?? '—' }}</div>
                                    @if($rx->patient->ward)
                                        <div class="text-xs text-gray-400">{{ $rx->patient->ward }} Ward</div>
                                    @endif
                                </td>
                                <td class="px-5 py-3 font-medium text-purple-700">{{ $rx->medication_name }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $rx->dosage }} · {{ $rx->frequency }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $rx->dispensedBy->name ?? '—' }}</td>
                                <td class="px-5 py-3 text-gray-500">
                                    {{ $rx->dispensed_at?->format('d M Y, H:i') ?? '—' }}
                                </td>
                                <td class="px-5 py-3">
                                        <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                            <i class="fas fa-check-circle"></i> Dispensed
                                        </span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="p-4 border-t border-gray-100">{{ $dispensedRx->links() }}</div>
                </div>
            @endif
        </section>

    </main>
</div>

</body>
</html>
