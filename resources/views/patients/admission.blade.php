<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admissions — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50:'#f5f3ff',100:'#ede9fe',200:'#ddd6fe',500:'#8b5cf6',600:'#7c3aed',700:'#6d28d9' },
                        secondary: { 100:'#d5f5f6',500:'#0694a2',600:'#047481' },
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
        .badge-stable { background: #dbeafe; color: #1e40af; }
        .badge-recovering { background: #d1fae5; color: #065f46; }
        .patient-card { background: white; border-radius: 16px; border: 1px solid #ede9fe; transition: all 0.2s; }
        .patient-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(124,58,237,0.1); border-color: #c4b5fd; }
        .stat-pill { background: white; border-radius: 12px; border: 1px solid #ede9fe; padding: 1rem 1.25rem; }
        .input-field { width: 100%; padding: 0.6rem 1rem; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 0.875rem; color: #374151; outline: none; transition: border-color 0.2s, box-shadow 0.2s; background: white; }
        .input-field:focus { border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,0.1); }
        @media (max-width: 1024px) { .sidebar { display: none; } .main-content { margin-left: 0; } }
    </style>
</head>
<body>

<!-- SIDEBAR -->
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
        <a href="{{ route('patients.admission') }}" class="nav-item active"><span class="icon"><i class="fas fa-bed"></i></span> Admissions</a>
        <a href="{{ route('patients.discharge') }}" class="nav-item"><span class="icon"><i class="fas fa-sign-out-alt"></i></span> Discharges</a>
        <p class="nav-section-label">Clinical</p>
        <a href="#" class="nav-item"><span class="icon"><i class="fas fa-notes-medical"></i></span> Clinical Notes</a>
        <a href="#" class="nav-item"><span class="icon"><i class="fas fa-file-prescription"></i></span> Prescriptions</a>
        <a href="#" class="nav-item"><span class="icon"><i class="fas fa-flask"></i></span> Lab Orders</a>
        <p class="nav-section-label">Ward</p>
        <a href="#" class="nav-item"><span class="icon"><i class="fas fa-procedures"></i></span> Ward Overview</a>
        <a href="#" class="nav-item"><span class="icon"><i class="fas fa-calendar-alt"></i></span> Schedule</a>
        <a href="{{ route('patients.create') }}" class="nav-item"><span class="icon"><i class="fas fa-user-plus"></i></span> Register Patient</a>
    </nav>
    <div style="padding: 1rem 0.75rem; border-top: 1px solid rgba(255,255,255,0.07);">
        <div class="flex items-center gap-3" style="padding:0.75rem; border-radius:10px; background:rgba(255,255,255,0.05);">
            <div class="w-9 h-9 rounded-full bg-primary-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
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

<!-- MAIN CONTENT -->
<div class="main-content">

    <!-- Topbar -->
    <header style="background:white; border-bottom:1px solid #ede9fe; padding:0 2rem; height:64px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:40;">
        <div>
            <h1 class="font-bold text-gray-800">Ward Admissions</h1>
            <p class="text-xs text-gray-400">{{ now()->format('l, d F Y') }} · {{ $patients->total() }} admitted patient(s)</p>
        </div>
        <a href="{{ route('patients.create') }}"
           class="flex items-center gap-2 px-4 py-2 rounded-xl text-white text-sm font-semibold bg-gradient-main hover:opacity-90 transition-opacity">
            <i class="fas fa-user-plus"></i> Admit New Patient
        </a>
    </header>

    <main class="p-6 space-y-6">

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

        <!-- Status Summary Pills -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach([
                ['label'=>'Admitted','val'=>$statusCounts['stable'] + ($patients->total() - $statusCounts['stable'] - $statusCounts['critical'] - $statusCounts['recovering'] - $statusCounts['observation']),'color'=>'text-emerald-600','bg'=>'bg-emerald-50','icon'=>'fa-bed','border'=>'border-emerald-200'],
                ['label'=>'Observation','val'=>$statusCounts['observation'],'color'=>'text-amber-600','bg'=>'bg-amber-50','icon'=>'fa-user-clock','border'=>'border-amber-200'],
                ['label'=>'Critical','val'=>$statusCounts['critical'],'color'=>'text-red-600','bg'=>'bg-red-50','icon'=>'fa-heartbeat','border'=>'border-red-200'],
                ['label'=>'Recovering','val'=>$statusCounts['recovering'],'color'=>'text-blue-600','bg'=>'bg-blue-50','icon'=>'fa-heart','border'=>'border-blue-200'],
            ] as $pill)
                <div class="stat-pill border {{ $pill['border'] }} flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl {{ $pill['bg'] }} flex items-center justify-center flex-shrink-0">
                        <i class="fas {{ $pill['icon'] }} {{ $pill['color'] }}"></i>
                    </div>
                    <div>
                        <p class="text-xl font-bold {{ $pill['color'] }}">{{ $pill['val'] }}</p>
                        <p class="text-xs text-gray-500">{{ $pill['label'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Filter Bar -->
        <div class="bg-white rounded-2xl border border-primary-100 p-4">
            <form method="GET" action="{{ route('patients.admission') }}" class="flex flex-wrap gap-3 items-center">
                <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 flex-1 min-w-48">
                    <i class="fas fa-search text-gray-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search patient name or diagnosis…"
                           class="bg-transparent text-sm focus:outline-none w-full text-gray-600">
                </div>
                <select name="status" onchange="this.form.submit()"
                        class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-600 focus:outline-none focus:border-primary-400 bg-white">
                    <option value="">All Statuses</option>
                    @foreach(['admitted','observation','critical','stable','recovering'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <select name="ward" onchange="this.form.submit()"
                        class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-600 focus:outline-none focus:border-primary-400 bg-white">
                    <option value="">All Wards</option>
                    @foreach(['General','ICU','Pediatric','Maternity','Surgical'] as $w)
                        <option value="{{ $w }}" {{ request('ward') === $w ? 'selected' : '' }}>{{ $w }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white text-sm rounded-lg hover:bg-primary-700 transition-colors">
                    Filter
                </button>
                @if(request('search') || request('status') || request('ward'))
                    <a href="{{ route('patients.admission') }}" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm rounded-lg hover:bg-gray-200 transition-colors">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Patient Cards Grid -->
        @if($patients->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($patients as $patient)
                    <div class="patient-card p-5">
                        <!-- Card Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-full bg-gradient-main flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                    {{ strtoupper(substr($patient->name, 0, 1)) }}{{ strtoupper(substr(strstr($patient->name, ' '), 1, 1)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800 text-sm">{{ $patient->name }}</p>
                                    <p class="text-xs text-gray-400">
                                        {{ ucfirst($patient->gender) }}
                                        @if($patient->age) · {{ $patient->age }} yrs @endif
                                        · #{{ $patient->id }}
                                    </p>
                                </div>
                            </div>
                            @php $st = $patient->status; @endphp
                            @if($st==='admitted')
                                <span class="badge badge-admitted"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>Admitted</span>
                            @elseif($st==='observation')
                                <span class="badge badge-observation"><span class="w-1.5 h-1.5 rounded-full bg-amber-500 inline-block"></span>Observation</span>
                            @elseif($st==='critical')
                                <span class="badge badge-critical"><span class="w-1.5 h-1.5 rounded-full bg-red-500 inline-block"></span>Critical</span>
                            @elseif($st==='stable')
                                <span class="badge badge-stable"><span class="w-1.5 h-1.5 rounded-full bg-blue-500 inline-block"></span>Stable</span>
                            @elseif($st==='recovering')
                                <span class="badge badge-recovering"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span>Recovering</span>
                            @endif
                        </div>

                        <!-- Patient Info -->
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center gap-2 text-xs text-gray-600">
                                <div class="w-6 h-6 rounded-lg bg-primary-50 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-bed text-primary-400 text-xs"></i>
                                </div>
                                <span><span class="font-medium">Ward:</span> {{ $patient->ward ?? 'Not assigned' }}</span>
                                @if($patient->bed_number)
                                    <span class="text-gray-400">· Bed {{ $patient->bed_number }}</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-600">
                                <div class="w-6 h-6 rounded-lg bg-primary-50 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-stethoscope text-primary-400 text-xs"></i>
                                </div>
                                <span class="truncate"><span class="font-medium">Diagnosis:</span> {{ $patient->diagnosis ?? 'Not recorded' }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-600">
                                <div class="w-6 h-6 rounded-lg bg-primary-50 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-clock text-primary-400 text-xs"></i>
                                </div>
                                <span>
                            <span class="font-medium">Admitted:</span>
                            {{ $patient->admitted_at ? \Carbon\Carbon::parse($patient->admitted_at)->format('d M Y, H:i') : 'Unknown' }}
                        </span>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-600">
                                <div class="w-6 h-6 rounded-lg bg-primary-50 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-calendar-day text-primary-400 text-xs"></i>
                                </div>
                                <span>
                            <span class="font-medium">Days admitted:</span>
                            {{ $patient->admitted_at ? \Carbon\Carbon::parse($patient->admitted_at)->diffInDays(now()) : 0 }} day(s)
                        </span>
                            </div>
                            @if($patient->phone)
                                <div class="flex items-center gap-2 text-xs text-gray-600">
                                    <div class="w-6 h-6 rounded-lg bg-primary-50 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-phone text-primary-400 text-xs"></i>
                                    </div>
                                    <span>{{ $patient->phone }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Update Status Form -->
                        <div class="border-t border-gray-50 pt-4">
                            <p class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Update Status</p>
                            <form method="POST" action="{{ route('patients.update', $patient) }}" class="flex gap-2">
                                @csrf
                                @method('PUT')
                                <select name="status" class="flex-1 text-xs border border-gray-200 rounded-lg px-2 py-2 focus:outline-none focus:border-primary-400 bg-white text-gray-600">
                                    @foreach(['admitted','observation','critical','stable','recovering'] as $s)
                                        <option value="{{ $s }}" {{ $patient->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                                {{-- keep other fields unchanged --}}
                                <input type="hidden" name="name" value="{{ $patient->name }}">
                                <input type="hidden" name="gender" value="{{ $patient->gender }}">
                                <input type="hidden" name="diagnosis" value="{{ $patient->diagnosis }}">
                                <button type="submit"
                                        class="px-3 py-2 bg-primary-600 text-white text-xs rounded-lg hover:bg-primary-700 transition-colors font-semibold">
                                    Update
                                </button>
                            </form>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-2 mt-3">
                            <a href="{{ route('patients.show', $patient) }}"
                               class="flex-1 text-center py-2 text-xs font-semibold text-primary-600 bg-primary-50 border border-primary-200 rounded-lg hover:bg-primary-100 transition-colors">
                                <i class="fas fa-eye mr-1"></i> View Record
                            </a>
                            <a href="{{ route('patients.discharge.form', $patient) }}"
                               class="flex-1 text-center py-2 text-xs font-semibold text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                                <i class="fas fa-sign-out-alt mr-1"></i> Discharge
                            </a>
                            <a href="{{ route('patients.edit', $patient) }}"
                               class="py-2 px-3 text-xs font-semibold text-gray-600 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($patients->hasPages())
                <div class="flex items-center justify-between bg-white rounded-xl border border-primary-100 px-5 py-3">
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
            <!-- Empty State -->
            <div class="bg-white rounded-2xl border border-primary-100 py-20 text-center">
                <div class="w-20 h-20 rounded-full bg-primary-50 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-bed text-3xl text-primary-300"></i>
                </div>
                <p class="text-gray-600 font-semibold text-lg">No admitted patients</p>
                <p class="text-gray-400 text-sm mt-1">
                    @if(request('search') || request('status') || request('ward'))
                        No patients match your filter. Try adjusting the search.
                    @else
                        Register and admit a patient to get started.
                    @endif
                </p>
                <a href="{{ route('patients.create') }}"
                   class="inline-flex items-center gap-2 mt-5 px-6 py-3 bg-gradient-main text-white text-sm rounded-xl hover:opacity-90 transition-opacity font-semibold">
                    <i class="fas fa-user-plus"></i> Admit New Patient
                </a>
            </div>
        @endif

    </main>
</div>

</body>
</html>
