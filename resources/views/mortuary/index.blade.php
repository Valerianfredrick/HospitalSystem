<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mortuary — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        body { background: #f8f7ff; }
        .sidebar { width: 260px; min-height: 100vh; background: #1e1035; position: fixed; left: 0; top: 0; z-index: 50; display: flex; flex-direction: column; }
        .main-content { margin-left: 260px; min-height: 100vh; }
        .nav-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.65rem 0.85rem; border-radius: 10px; color: rgba(255,255,255,0.55); font-size: 0.875rem; font-weight: 500; text-decoration: none; margin-bottom: 2px; }
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
        <p class="text-xs mt-1 ml-10" style="color:rgba(255,255,255,0.3)">Mortuary Module</p>
    </div>
    <nav style="flex:1; padding:1.25rem 0.75rem;">
        <a href="{{ route('mortuary.index') }}" class="nav-item active"><span class="icon"><i class="fas fa-procedures"></i></span> All Records</a>
    </nav>
    <div style="padding:1rem 0.75rem; border-top:1px solid rgba(255,255,255,0.07);">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item w-full" style="background:none;border:none;cursor:pointer;">
                <span class="icon"><i class="fas fa-sign-out-alt"></i></span> Logout
            </button>
        </form>
    </div>
</aside>

<div class="main-content">
    <header style="background:white; border-bottom:1px solid #ede9fe; padding:0 2rem; height:64px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:40;">
        <h1 class="font-bold text-gray-800">Mortuary Records</h1>
    </header>

    <main class="p-6 space-y-6">

        @if(session('success'))
            <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            </div>
        @endif

        {{-- Pending alert --}}
        @if($stats['pending'] > 0)
            <div class="bg-red-50 border border-red-200 rounded-2xl px-6 py-4 flex items-center gap-4">
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-red-800">
                        {{ $stats['pending'] }} body/bodies pending reception
                    </p>
                    <p class="text-xs text-red-600 mt-0.5">Please confirm receipt from the ward.</p>
                </div>
            </div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-3 gap-4">
            @foreach([
                ['label' => 'Pending Reception', 'value' => $stats['pending'],  'color' => 'bg-red-50 text-red-600',    'ring' => 'ring-red-200'],
                ['label' => 'Received',           'value' => $stats['received'], 'color' => 'bg-blue-50 text-blue-600',  'ring' => 'ring-blue-200'],
                ['label' => 'Released',           'value' => $stats['released'], 'color' => 'bg-green-50 text-green-600','ring' => 'ring-green-200'],
            ] as $card)
                <div class="bg-white rounded-2xl p-5 shadow-sm ring-1 {{ $card['ring'] }}">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">{{ $card['label'] }}</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $card['value'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Records table --}}
        <div class="bg-white rounded-2xl border border-purple-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                <tr class="border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wide">
                    <th class="text-left px-6 py-4 font-semibold">Deceased</th>
                    <th class="text-left px-6 py-4 font-semibold">Cause of Death</th>
                    <th class="text-left px-6 py-4 font-semibold">Referred By</th>
                    <th class="text-left px-6 py-4 font-semibold">Date</th>
                    <th class="text-left px-6 py-4 font-semibold">Status</th>
                    <th class="text-left px-6 py-4 font-semibold">Action</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                @forelse($records as $record)
                    <tr class="hover:bg-gray-50 {{ $record->status === 'pending' ? 'bg-red-50/20' : '' }}">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-800">{{ $record->patient->name }}</p>
                            <p class="text-xs text-gray-400">
                                {{ ucfirst($record->patient->gender) }} · {{ $record->patient->age }} yrs
                                · {{ $record->patient->ward ?? '—' }} Ward
                            </p>
                        </td>
                        <td class="px-6 py-4 text-gray-600 text-sm">
                            {{ $record->cause_of_death ?? '—' }}
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-500">
                            Dr. {{ $record->referredBy->name ?? '—' }}
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-400">
                            {{ $record->created_at->format('d M Y, H:i') }}
                        </td>
                        <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ $record->status === 'released' ? 'bg-green-100 text-green-700' :
                                       ($record->status === 'received' ? 'bg-blue-100 text-blue-700'  :
                                                                         'bg-red-100 text-red-700') }}">
                                    {{ ucfirst($record->status) }}
                                </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('mortuary.show', $record) }}"
                               class="px-3 py-1.5 text-xs font-semibold text-purple-600 border border-purple-200 rounded-lg hover:bg-purple-50">
                                View / Process
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-gray-400">
                            <i class="fas fa-procedures text-4xl mb-3 block opacity-20"></i>
                            No mortuary records.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            @if($records->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">{{ $records->links() }}</div>
            @endif
        </div>
    </main>
</div>
</body>
</html>
