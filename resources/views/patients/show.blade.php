<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $patient->name }} — {{ config('app.name') }}</title>
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
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 12px; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
        .badge-admitted { background: #d1fae5; color: #065f46; }
        .badge-observation { background: #fef3c7; color: #92400e; }
        .badge-critical { background: #fee2e2; color: #991b1b; }
        .badge-discharged { background: #f3f4f6; color: #374151; }
        .badge-stable { background: #dbeafe; color: #1e40af; }
        .badge-recovering { background: #d1fae5; color: #065f46; }
        .section-card { background: white; border-radius: 16px; border: 1px solid #ede9fe; padding: 1.5rem; }
        .info-row { display: flex; justify-content: space-between; align-items: flex-start; padding: 0.6rem 0; border-bottom: 1px solid #f5f3ff; font-size: 0.875rem; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #9ca3af; font-weight: 500; font-size: 0.75rem; }
        .info-value { color: #1f2937; font-weight: 600; text-align: right; max-width: 60%; }
        .timeline-item { position: relative; padding-left: 1.5rem; padding-bottom: 1.25rem; }
        .timeline-item::before { content: ''; position: absolute; left: 5px; top: 22px; bottom: 0; width: 2px; background: #ede9fe; }
        .timeline-item:last-child::before { display: none; }
        .timeline-dot { position: absolute; left: 0; top: 6px; width: 12px; height: 12px; border-radius: 50%; border: 2px solid #7c3aed; background: white; }
        .timeline-dot.filled { background: #7c3aed; }
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
                <button type="submit" class="text-sm" style="color:rgba(255,255,255,0.4)" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

<div class="main-content">
    <!-- Topbar -->
    <header style="background:white; border-bottom:1px solid #ede9fe; padding:0 2rem; height:64px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:40;">
        <div class="flex items-center gap-3">
            <a href="{{ route('patients.index') }}" class="text-gray-400 hover:text-primary-600">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="font-bold text-gray-800">{{ $patient->name }}</h1>
                <p class="text-xs text-gray-400">Patient Record · Admitted {{ \Carbon\Carbon::parse($patient->admitted_at)->format('d M Y') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if($patient->status !== 'discharged')
                <a href="{{ route('patients.discharge.form', $patient) }}"
                   class="px-4 py-2 text-xs font-semibold text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                    <i class="fas fa-sign-out-alt mr-1"></i> Discharge
                </a>
                <a href="{{ route('patients.edit', $patient) }}"
                   class="px-4 py-2 text-xs font-semibold text-primary-600 border border-primary-200 rounded-lg hover:bg-primary-50 transition-colors">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
            @endif
        </div>
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

        <!-- Patient Header Card -->
        <div class="bg-gradient-main rounded-2xl p-6 text-white">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-2xl font-bold">
                        {{ strtoupper(substr($patient->name, 0, 1)) }}{{ strtoupper(substr(strstr($patient->name, ' '), 1, 1)) }}
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">{{ $patient->name }}</h2>
                        <p class="text-white/70 text-sm">
                            {{ ucfirst($patient->gender) }} · {{ $patient->age }} yrs
                            @if($patient->ward) · {{ $patient->ward }} Ward @endif
                        </p>
                        <div class="mt-1">
                            @php $status = $patient->status; @endphp
                            <span class="badge badge-{{ $status }}">
                                <span class="w-1.5 h-1.5 rounded-full
                                    @if($status==='admitted') bg-emerald-500
                                    @elseif($status==='critical') bg-red-500
                                    @elseif($status==='observation') bg-amber-500
                                    @else bg-gray-400 @endif"></span>
                                {{ ucfirst($status) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 text-center">
                    <div class="bg-white/10 rounded-xl p-3">
                        <p class="text-white/60 text-xs">Admitted</p>
                        <p class="font-bold text-sm">{{ \Carbon\Carbon::parse($patient->admitted_at)->format('d M Y') }}</p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-3">
                        <p class="text-white/60 text-xs">Days Admitted</p>
                        <p class="font-bold text-sm">{{ \Carbon\Carbon::parse($patient->admitted_at)->diffInDays(now()) }} days</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left: Patient Details -->
            <div class="space-y-5">
                <!-- Demographics -->
                <div class="section-card">
                    <h3 class="font-bold text-gray-800 text-sm mb-4 flex items-center gap-2">
                        <i class="fas fa-id-card text-primary-500"></i> Patient Details
                    </h3>
                    <div>
                        <div class="info-row"><span class="info-label">Phone</span><span class="info-value">{{ $patient->phone ?? '—' }}</span></div>
                        <div class="info-row"><span class="info-label">Address</span><span class="info-value">{{ $patient->address ?? '—' }}</span></div>
                        <div class="info-row"><span class="info-label">Emergency Contact</span><span class="info-value">{{ $patient->emergency_contact ?? '—' }}</span></div>
                        <div class="info-row"><span class="info-label">Ward</span><span class="info-value">{{ $patient->ward ?? '—' }}</span></div>
                        <div class="info-row"><span class="info-label">Status</span><span class="info-value capitalize">{{ $patient->status }}</span></div>
                        @if($patient->status === 'discharged')
                            <div class="info-row"><span class="info-label">Discharged</span><span class="info-value">{{ \Carbon\Carbon::parse($patient->discharged_at)->format('d M Y') }}</span></div>
                            <div class="info-row"><span class="info-label">Condition</span><span class="info-value capitalize">{{ $patient->discharge_condition ?? '—' }}</span></div>
                        @endif
                    </div>
                </div>

                <!-- Diagnosis -->
                <div class="section-card">
                    <h3 class="font-bold text-gray-800 text-sm mb-3 flex items-center gap-2">
                        <i class="fas fa-stethoscope text-primary-500"></i> Diagnosis
                    </h3>
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $patient->diagnosis ?? 'No diagnosis recorded.' }}</p>
                </div>

                @if($patient->status === 'discharged' && $patient->discharge_notes)
                    <div class="section-card">
                        <h3 class="font-bold text-gray-800 text-sm mb-3 flex items-center gap-2">
                            <i class="fas fa-file-medical text-primary-500"></i> Discharge Notes
                        </h3>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $patient->discharge_notes }}</p>
                        @if($patient->followup_date)
                            <p class="text-xs text-primary-600 font-semibold mt-2">
                                <i class="fas fa-calendar mr-1"></i> Follow-up: {{ \Carbon\Carbon::parse($patient->followup_date)->format('d M Y') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Middle: Clinical Notes -->
            <div class="space-y-5">
                <div class="section-card">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                            <i class="fas fa-notes-medical text-primary-500"></i> Clinical Notes
                        </h3>
                        <span class="text-xs text-gray-400">{{ $patient->clinicalNotes->count() }} notes</span>
                    </div>

                    @if($patient->clinicalNotes->count() > 0)
                        <div class="space-y-0 mb-4">
                            @foreach($patient->clinicalNotes->sortByDesc('created_at') as $note)
                                <div class="timeline-item">
                                    <div class="timeline-dot filled"></div>
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <span class="text-xs font-semibold text-primary-600 capitalize">{{ $note->type }}</span>
                                        <span class="text-xs text-gray-400">· {{ \Carbon\Carbon::parse($note->created_at)->format('d M, H:i') }}</span>
                                    </div>
                                    <p class="text-xs text-gray-700 leading-relaxed">{{ $note->content }}</p>
                                    @if($note->user)
                                        <p class="text-xs text-gray-400 mt-0.5">— {{ $note->user->name }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-gray-400 mb-4">No clinical notes yet.</p>
                    @endif

                    @if($patient->status !== 'discharged')
                        <form method="POST" action="{{ route('patients.notes.store', $patient) }}">
                            @csrf
                            <select name="type" class="w-full text-xs border border-gray-200 rounded-lg px-3 py-2 mb-2 focus:outline-none focus:border-primary-400">
                                <option value="progress">Progress Note</option>
                                <option value="assessment">Assessment</option>
                                <option value="observation">Observation</option>
                                <option value="procedure">Procedure</option>
                            </select>
                            <textarea name="content" rows="3" placeholder="Write clinical note..."
                                      class="w-full text-xs border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:border-primary-400 resize-none" required></textarea>
                            <button type="submit"
                                    class="mt-2 w-full py-2 text-xs font-semibold text-white rounded-lg bg-gradient-main hover:opacity-90 transition-opacity">
                                Add Note
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Right: Prescriptions -->
            <div class="space-y-5">
                <div class="section-card">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                            <i class="fas fa-file-prescription text-primary-500"></i> Prescriptions
                        </h3>
                        <span class="text-xs text-gray-400">{{ $patient->prescriptions->count() }} issued</span>
                    </div>

                    @if($patient->prescriptions->count() > 0)
                        <div class="space-y-3 mb-4">
                            @foreach($patient->prescriptions->sortByDesc('created_at') as $rx)
                                <div class="p-3 bg-primary-50 rounded-xl border border-primary-100">
                                    <div class="flex items-center justify-between mb-1">
                                        <p class="text-xs font-bold text-primary-700">
                                            {{ $rx->stockItem->name ?? 'Unknown Drug' }}
                                        </p>
                                        @if($rx->dispensed_at)
                                            <span class="text-xs text-emerald-600 font-semibold">✓ Dispensed</span>
                                        @else
                                            <span class="text-xs text-amber-600 font-semibold">Pending</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-600">{{ $rx->dosage }} · {{ $rx->frequency }}</p>
                                    @if($rx->duration)<p class="text-xs text-gray-400">Duration: {{ $rx->duration }}</p>@endif
                                    @if($rx->instructions)<p class="text-xs text-gray-400 italic">{{ $rx->instructions }}</p>@endif
                                    <p class="text-xs text-gray-400 mt-1">
                                        By {{ $rx->prescribedBy->name ?? 'Unknown' }} · {{ \Carbon\Carbon::parse($rx->created_at)->format('d M, H:i') }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-gray-400 mb-4">No prescriptions issued yet.</p>
                    @endif

                    @if($patient->status !== 'discharged')
                        <a href="{{ route('patients.prescriptions.create', $patient) }}"
                           class="block w-full py-2 text-xs font-semibold text-center text-primary-600 border border-primary-200 rounded-lg hover:bg-primary-50 transition-colors">
                            <i class="fas fa-plus mr-1"></i> New Prescription
                        </a>
                    @endif
                </div>
            </div>

        </div>
    </main>
</div>

</body>
</html>
