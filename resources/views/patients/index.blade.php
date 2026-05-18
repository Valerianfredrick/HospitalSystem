<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Patients — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50:'#f5f3ff',100:'#ede9fe',200:'#ddd6fe',500:'#8b5cf6',600:'#7c3aed',700:'#6d28d9' },
                        secondary: { 500:'#0694a2',600:'#047481' },
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
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 999px; font-size: 0.7rem; font-weight: 600; }
        .badge-admitted { background: #d1fae5; color: #065f46; }
        .badge-observation { background: #fef3c7; color: #92400e; }
        .badge-critical { background: #fee2e2; color: #991b1b; }
        .badge-discharged { background: #f3f4f6; color: #374151; }
        .badge-stable { background: #dbeafe; color: #1e40af; }
        .badge-recovering { background: #d1fae5; color: #065f46; }
        .patient-table { width: 100%; border-collapse: collapse; }
        .patient-table th { background: #f5f3ff; color: #6d28d9; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.06em; padding: 0.85rem 1rem; text-align: left; font-weight: 700; }
        .patient-table th:first-child { border-radius: 10px 0 0 10px; }
        .patient-table th:last-child { border-radius: 0 10px 10px 0; }
        .patient-table td { padding: 0.9rem 1rem; border-bottom: 1px solid #f5f3ff; font-size: 0.84rem; color: #374151; vertical-align: middle; }
        .patient-table tr:last-child td { border-bottom: none; }
        .patient-table tr:hover td { background: #fdfcff; cursor: pointer; }
        .avatar-circle { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 700; flex-shrink: 0; }
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
        <a href="{{ route('patients.index') }}" class="nav-item active"><span class="icon"><i class="fas fa-user-injured"></i></span> All Patients</a>
        <a href="{{ route('patients.admission') }}" class="nav-item"><span class="icon"><i class="fas fa-bed"></i></span> Admissions</a>
        <a href="{{ route('patients.discharge') }}" class="nav-item"><span class="icon"><i class="fas fa-sign-out-alt"></i></span> Discharges</a>
        <p class="nav-section-label">Clinical</p>
        <a href="#" class="nav-item"><span class="icon"><i class="fas fa-notes-medical"></i></span> Clinical Notes</a>
        <a href="#" class="nav-item"><span class="icon"><i class="fas fa-file-prescription"></i></span> Prescriptions</a>
        <p class="nav-section-label">Ward</p>
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
                <button type="submit" style="color:rgba(255,255,255,0.4)" title="Logout">
                    <i class="fas fa-sign-out-alt text-sm"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

<div class="main-content">
    <header style="background:white; border-bottom:1px solid #ede9fe; padding:0 2rem; height:64px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:40;">
        <div>
            <h1 class="font-bold text-gray-800">All Patients</h1>
            <p class="text-xs text-gray-400">{{ $patients->total() }} patients total</p>
        </div>
        <a href="{{ route('patients.create') }}"
           class="flex items-center gap-2 px-4 py-2 rounded-xl text-white text-sm font-semibold bg-gradient-main hover:opacity-90 transition-opacity">
            <i class="fas fa-user-plus"></i> Register Patient
        </a>
    </header>

    <main class="p-6">

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 flex items-center gap-2">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-white rounded-2xl border border-primary-100 p-4 mb-5 flex flex-wrap gap-3 items-center">
            <form method="GET" action="{{ route('patients.index') }}" class="flex flex-wrap gap-3 flex-1">
                <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 flex-1 min-w-48">
                    <i class="fas fa-search text-gray-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search by name or diagnosis…"
                           class="bg-transparent text-sm focus:outline-none w-full text-gray-600">
                </div>
                <select name="status" onchange="this.form.submit()"
                        class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-600 focus:outline-none focus:border-primary-400 bg-white">
                    <option value="">All Statuses</option>
                    @foreach(['admitted','observation','critical','stable','recovering','discharged'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                            {{ ucfirst($s) }}
                        </option>
                    @endforeach
                </select>
                <button type="submit"
                        class="px-4 py-2 bg-primary-600 text-white text-sm rounded-lg hover:bg-primary-700 transition-colors">
                    Search
                </button>
                @if(request('search') || request('status'))
                    <a href="{{ route('patients.index') }}"
                       class="px-4 py-2 bg-gray-100 text-gray-600 text-sm rounded-lg hover:bg-gray-200 transition-colors">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl border border-primary-100 overflow-hidden">
            @if($patients->count() > 0)
                <div class="overflow-x-auto">
                    <table class="patient-table">
                        <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Age / Gender</th>
                            <th>Ward</th>
                            <th>Diagnosis</th>
                            <th>Admitted</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($patients as $patient)
                            <tr onclick="window.location='{{ route('patients.show', $patient) }}'" style="cursor:pointer;">
                                <td>
                                    <div class="flex items-center gap-2.5">
                                        <div class="avatar-circle bg-primary-100 text-primary-700">
                                            {{ strtoupper(substr($patient->name, 0, 1)) }}{{ strtoupper(substr(strstr($patient->name, ' '), 1, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800">{{ $patient->name }}</p>
                                            <p class="text-xs text-gray-400">#{{ $patient->id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-gray-600">
                                    {{ $patient->age ?? '—' }} yrs · {{ ucfirst($patient->gender) }}
                                </td>
                                <td class="text-gray-600">{{ $patient->ward ?? '—' }}</td>
                                <td class="text-gray-600 text-xs max-w-xs truncate">{{ $patient->diagnosis ?? '—' }}</td>
                                <td class="text-gray-500 text-xs">
                                    {{ $patient->admitted_at ? \Carbon\Carbon::parse($patient->admitted_at)->format('d M Y') : '—' }}
                                </td>
                                <td>
                                    @php $st = $patient->status; @endphp
                                    <span class="badge badge-{{ $st }}">
                                            <span class="w-1.5 h-1.5 rounded-full inline-block
                                                @if($st==='admitted'||$st==='stable'||$st==='recovering') bg-emerald-500
                                                @elseif($st==='critical') bg-red-500
                                                @elseif($st==='observation') bg-amber-500
                                                @else bg-gray-400 @endif"></span>
                                            {{ ucfirst($st) }}
                                        </span>
                                </td>
                                <td onclick="event.stopPropagation()">
                                    <a href="{{ route('patients.show', $patient) }}"
                                       class="text-primary-600 hover:text-primary-700 font-semibold text-xs bg-primary-50 px-3 py-1 rounded-full hover:bg-primary-100 transition-colors">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($patients->hasPages())
                    <div class="p-4 border-t border-gray-50 flex items-center justify-between">
                        <p class="text-xs text-gray-500">
                            Showing {{ $patients->firstItem() }}–{{ $patients->lastItem() }} of {{ $patients->total() }} patients
                        </p>
                        <div class="flex gap-1">
                            @if($patients->onFirstPage())
                                <span class="px-3 py-1 text-xs text-gray-300 border border-gray-100 rounded-lg">← Prev</span>
                            @else
                                <a href="{{ $patients->previousPageUrl() }}" class="px-3 py-1 text-xs text-primary-600 border border-primary-200 rounded-lg hover:bg-primary-50">← Prev</a>
                            @endif
                            @if($patients->hasMorePages())
                                <a href="{{ $patients->nextPageUrl() }}" class="px-3 py-1 text-xs text-primary-600 border border-primary-200 rounded-lg hover:bg-primary-50">Next →</a>
                            @else
                                <span class="px-3 py-1 text-xs text-gray-300 border border-gray-100 rounded-lg">Next →</span>
                            @endif
                        </div>
                    </div>
                @endif

            @else
                <div class="py-20 text-center">
                    <div class="w-16 h-16 rounded-full bg-primary-50 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-injured text-2xl text-primary-300"></i>
                    </div>
                    <p class="text-gray-500 font-medium">No patients found</p>
                    <p class="text-gray-400 text-sm mt-1">
                        @if(request('search') || request('status'))
                            Try adjusting your search or filter.
                        @else
                            Register the first patient to get started.
                        @endif
                    </p>
                    <a href="{{ route('patients.create') }}"
                       class="inline-flex items-center gap-2 mt-4 px-5 py-2 bg-gradient-main text-white text-sm rounded-xl hover:opacity-90 transition-opacity">
                        <i class="fas fa-user-plus"></i> Register Patient
                    </a>
                </div>
            @endif
        </div>

    </main>
</div>

</body>
</html>
