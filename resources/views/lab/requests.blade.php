<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Requests — {{ config('app.name') }}</title>
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
    <div style="padding: 1.5rem 1.5rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.07);">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-gradient-main flex items-center justify-center">
                <i class="fas fa-heartbeat text-white text-sm"></i>
            </div>
            <span class="text-white font-bold text-lg">{{ config('app.name', 'MediCore') }}</span>
        </div>
        <p class="text-xs mt-1 ml-10" style="color:rgba(255,255,255,0.3)">Laboratory Module</p>
    </div>
    <nav style="flex:1; padding: 1.25rem 0.75rem; overflow-y:auto;">
        <a href="{{ route('lab.dashboard') }}" class="nav-item"><span class="icon"><i class="fas fa-th-large"></i></span> Dashboard</a>
        <a href="{{ route('lab.requests') }}" class="nav-item active"><span class="icon"><i class="fas fa-flask"></i></span> Test Requests</a>
    </nav>
    <div style="padding: 1rem 0.75rem; border-top: 1px solid rgba(255,255,255,0.07);">
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
        <h1 class="font-bold text-gray-800">Test Requests</h1>
        {{-- Search & Filter --}}
        <form method="GET" action="{{ route('lab.requests') }}" class="flex items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search patient..."
                   class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:border-purple-400 w-48">
            <select name="status" class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:border-purple-400">
                <option value="">All Status</option>
                <option value="pending"     {{ request('status') === 'pending'     ? 'selected' : '' }}>Pending</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed"   {{ request('status') === 'completed'   ? 'selected' : '' }}>Completed</option>
            </select>
            <button type="submit" class="px-3 py-1.5 text-sm font-semibold text-white rounded-lg bg-gradient-main">Filter</button>
        </form>
    </header>

    <main class="p-6">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-purple-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                <tr class="border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wide">
                    <th class="text-left px-6 py-4 font-semibold">Patient</th>
                    <th class="text-left px-6 py-4 font-semibold">Test</th>
                    <th class="text-left px-6 py-4 font-semibold">Requested By</th>
                    <th class="text-left px-6 py-4 font-semibold">Date</th>
                    <th class="text-left px-6 py-4 font-semibold">Status</th>
                    <th class="text-left px-6 py-4 font-semibold">Action</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                @forelse($requests as $req)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-800">{{ $req->patient->name }}</p>
                            <p class="text-xs text-gray-400">{{ $req->patient->ward ?? '—' }} Ward</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-700">{{ $req->test_name }}</p>
                            @if($req->clinical_notes)
                                <p class="text-xs text-gray-400 truncate max-w-xs">{{ $req->clinical_notes }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            Dr. {{ $req->requestedBy->name ?? '—' }}
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-xs">
                            {{ $req->created_at->format('d M Y, H:i') }}
                        </td>
                        <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ $req->status === 'completed'   ? 'bg-green-100 text-green-700' :
                                       ($req->status === 'in_progress' ? 'bg-blue-100 text-blue-700' :
                                                                          'bg-amber-100 text-amber-700') }}">
                                    {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                                </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('lab.show', $req) }}"
                               class="px-3 py-1.5 text-xs font-semibold text-purple-600 border border-purple-200 rounded-lg hover:bg-purple-50 transition-colors">
                                View / Process
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-gray-400">
                            <i class="fas fa-flask text-4xl mb-3 block opacity-30"></i>
                            No lab requests found.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            @if($requests->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </main>
</div>
</body>
</html>
