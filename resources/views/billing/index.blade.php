<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        body { background: #f8f7ff; }
        .sidebar { width: 260px; min-height: 100vh; background: #1e1035; position: fixed; left: 0; top: 0; z-index: 50; display: flex; flex-direction: column; }
        .main-content { margin-left: 260px; min-height: 100vh; }
        .nav-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.65rem 0.85rem; border-radius: 10px; color: rgba(255,255,255,0.55); font-size: 0.875rem; font-weight: 500; text-decoration: none; margin-bottom: 2px; transition: all 0.2s; }
        .nav-item:hover, .nav-item.active { background: rgba(255,255,255,0.12); color: white; }
        .nav-item .icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.08); font-size: 0.8rem; }
        .bg-gradient-main { background: linear-gradient(135deg, #6d28d9 0%, #0694a2 100%); }
    </style>
</head>
<body>

<aside class="sidebar">
    <div style="padding:1.5rem 1.5rem 1rem; border-bottom:1px solid rgba(255,255,255,0.07);">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-gradient-main flex items-center justify-center">
                <i class="fas fa-heartbeat text-white text-sm"></i>
            </div>
            <span class="text-white font-bold text-lg">{{ config('app.name') }}</span>
        </div>
        <p class="text-xs mt-1 ml-10" style="color:rgba(255,255,255,0.3)">Billing Module</p>
    </div>
    <nav style="flex:1; padding:1.25rem 0.75rem; overflow-y:auto;">
        <a href="{{ route('billing.index') }}" class="nav-item active"><span class="icon"><i class="fas fa-receipt"></i></span> All Bills</a>
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
    <header style="background:white; border-bottom:1px solid #ede9fe; padding:0 2rem; height:64px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:40;">
        <h1 class="font-bold text-gray-800">Billing & Payments</h1>
    </header>

    <main class="p-6 space-y-6">

        @if(session('success'))
            <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            </div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                ['label' => 'Unpaid Bills',      'value' => $stats['unpaid'],      'icon' => 'fa-exclamation-circle', 'color' => 'bg-red-50 text-red-600',    'ring' => 'ring-red-200'],
                ['label' => 'Paid Today',         'value' => $stats['paid_today'],  'icon' => 'fa-check-circle',       'color' => 'bg-green-50 text-green-600','ring' => 'ring-green-200'],
                ['label' => 'Revenue Today (TZS)','value' => number_format($stats['total_today']), 'icon' => 'fa-coins', 'color' => 'bg-purple-50 text-purple-600','ring' => 'ring-purple-200'],
                ['label' => 'Outstanding (TZS)',  'value' => number_format($stats['outstanding']), 'icon' => 'fa-clock', 'color' => 'bg-amber-50 text-amber-600', 'ring' => 'ring-amber-200'],
            ] as $card)
                <div class="bg-white rounded-2xl p-5 shadow-sm ring-1 {{ $card['ring'] }}">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ $card['label'] }}</span>
                        <div class="w-9 h-9 rounded-xl {{ $card['color'] }} flex items-center justify-center">
                            <i class="fas {{ $card['icon'] }}"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">{{ $card['value'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Bills table --}}
        <div class="bg-white rounded-2xl border border-purple-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                <tr class="border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wide">
                    <th class="text-left px-6 py-4 font-semibold">Patient</th>
                    <th class="text-left px-6 py-4 font-semibold">Discharged</th>
                    <th class="text-left px-6 py-4 font-semibold">Total (TZS)</th>
                    <th class="text-left px-6 py-4 font-semibold">Balance (TZS)</th>
                    <th class="text-left px-6 py-4 font-semibold">Status</th>
                    <th class="text-left px-6 py-4 font-semibold">Action</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                @forelse($bills as $bill)
                    <tr class="hover:bg-gray-50 {{ $bill->status === 'unpaid' ? 'bg-red-50/20' : '' }}">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-800">{{ $bill->patient->name }}</p>
                            <p class="text-xs text-gray-400">
                                {{ ucfirst($bill->patient->gender) }} · {{ $bill->patient->age }} yrs
                                · {{ $bill->patient->ward ?? '—' }} Ward
                            </p>
                            <p class="text-xs text-gray-400">
                                Condition: <span class="capitalize font-medium">{{ $bill->patient->discharge_condition }}</span>
                            </p>
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-500">
                            {{ $bill->patient->discharged_at?->format('d M Y') ?? '—' }}<br>
                            <span class="text-gray-400">{{ $bill->bed_days }} day(s)</span>
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-800">
                            {{ number_format($bill->grand_total) }}
                        </td>
                        <td class="px-6 py-4 font-semibold {{ $bill->balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                            {{ number_format($bill->balance) }}
                        </td>
                        <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ $bill->status === 'paid'    ? 'bg-green-100 text-green-700' :
                                       ($bill->status === 'partial' ? 'bg-blue-100 text-blue-700'  :
                                       ($bill->status === 'waived'  ? 'bg-gray-100 text-gray-600'  :
                                                                       'bg-red-100 text-red-700')) }}">
                                    {{ ucfirst($bill->status) }}
                                </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('billing.show', $bill) }}"
                               class="px-3 py-1.5 text-xs font-semibold text-purple-600 border border-purple-200 rounded-lg hover:bg-purple-50 transition">
                                {{ in_array($bill->status, ['unpaid','partial']) ? 'Process Payment' : 'View' }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-gray-400">
                            <i class="fas fa-receipt text-4xl mb-3 block opacity-20"></i>
                            No bills yet.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            @if($bills->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">{{ $bills->links() }}</div>
            @endif
        </div>
    </main>
</div>
</body>
</html>
