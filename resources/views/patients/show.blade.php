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
        .badge-admitted    { background: #d1fae5; color: #065f46; }
        .badge-observation { background: #fef3c7; color: #92400e; }
        .badge-critical    { background: #fee2e2; color: #991b1b; }
        .badge-discharged  { background: #f3f4f6; color: #374151; }
        .badge-stable      { background: #dbeafe; color: #1e40af; }
        .badge-recovering  { background: #d1fae5; color: #065f46; }
        .badge-deceased    { background: #1f2937; color: #e5e7eb; }
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
        .modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 100; display: flex; align-items: center; justify-content: center; padding: 1rem; }
        .modal-backdrop.hidden { display: none; }
        .modal-box { background: white; border-radius: 1.25rem; width: 100%; max-width: 460px; padding: 2rem; box-shadow: 0 25px 50px rgba(0,0,0,0.2); }
    </style>
</head>
<body>

{{-- ── Mortuary Transfer Modal ──────────────────────────────────────────── --}}
<div class="modal-backdrop hidden" id="mortuaryModal">
    <div class="modal-box">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-cross text-gray-600 text-lg"></i>
            </div>
            <div>
                <h2 class="font-bold text-gray-800 text-base">Transfer to Mortuary</h2>
                <p class="text-xs text-gray-400">{{ $patient->name }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('patients.mortuary.transfer', $patient) }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                    Time of Death <span class="text-red-500">*</span>
                </label>
                <input type="datetime-local" name="time_of_death" id="timeOfDeath" required
                       class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-purple-400">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                    Cause of Death <span class="text-red-500">*</span>
                </label>
                <input type="text" name="cause_of_death" required
                       placeholder="e.g. Cardiac arrest, Sepsis…"
                       class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-purple-400">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                    Body Tag / Reference No. <span class="text-red-500">*</span>
                </label>
                <input type="text" name="body_tag" required
                       placeholder="e.g. MTY-2024-001"
                       class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-purple-400">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Notes (optional)</label>
                <textarea name="notes" rows="2"
                          placeholder="Any additional notes for mortuary staff…"
                          class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-purple-400 resize-none"></textarea>
            </div>

            <div class="flex gap-3 pt-1">
                <button type="button" onclick="closeMortuaryModal()"
                        class="flex-1 py-2.5 text-sm font-semibold text-gray-500 border border-gray-200 rounded-xl hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 text-sm font-semibold text-white rounded-xl bg-gray-800 hover:bg-gray-900 transition-colors flex items-center justify-center gap-2">
                    <i class="fas fa-check"></i> Confirm Transfer
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── SIDEBAR ──────────────────────────────────────────────────────────── --}}
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

{{-- ── MAIN CONTENT ─────────────────────────────────────────────────────── --}}
<div class="main-content">

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
            @if(!in_array($patient->status, ['discharged', 'deceased']))
                <a href="{{ route('patients.lab.create', $patient) }}"
                   class="px-4 py-2 text-xs font-semibold text-secondary-600 border border-secondary-200 rounded-lg hover:bg-teal-50 transition-colors">
                    <i class="fas fa-flask mr-1"></i> Send to Lab
                </a>
                <a href="{{ route('patients.discharge.form', $patient) }}"
                   class="px-4 py-2 text-xs font-semibold text-emerald-600 border border-emerald-200 rounded-lg hover:bg-emerald-50 transition-colors">
                    <i class="fas fa-sign-out-alt mr-1"></i> Discharge
                </a>
                <button type="button" onclick="openMortuaryModal()"
                        class="px-4 py-2 text-xs font-semibold text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors flex items-center gap-1">
                    <i class="fas fa-cross"></i> Mortuary
                </button>
                <a href="{{ route('patients.edit', $patient) }}"
                   class="px-4 py-2 text-xs font-semibold text-primary-600 border border-primary-200 rounded-lg hover:bg-primary-50 transition-colors">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
            @elseif($patient->status === 'deceased')
                <span class="px-4 py-2 text-xs font-semibold text-gray-400 border border-gray-200 rounded-lg bg-gray-50 flex items-center gap-1">
                    <i class="fas fa-cross"></i> In Mortuary
                </span>
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

        {{-- Patient Header Card --}}
        <div class="{{ $patient->status === 'deceased' ? 'bg-gray-700' : 'bg-gradient-main' }} rounded-2xl p-6 text-white">
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
                                    @if($status==='admitted'||$status==='stable'||$status==='recovering') bg-emerald-500
                                    @elseif($status==='critical') bg-red-500
                                    @elseif($status==='observation') bg-amber-500
                                    @elseif($status==='deceased') bg-gray-400
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
                        @if($patient->status === 'deceased' && $patient->mortuaryRecord)
                            <p class="text-white/60 text-xs">Time of Death</p>
                            <p class="font-bold text-sm">{{ \Carbon\Carbon::parse($patient->mortuaryRecord->time_of_death)->format('d M Y, H:i') }}</p>
                        @else
                            <p class="text-white/60 text-xs">Days Admitted</p>
                            <p class="font-bold text-sm">{{ \Carbon\Carbon::parse($patient->admitted_at)->diffInDays(now()) }} days</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Deceased / Mortuary info banner --}}
        @if($patient->status === 'deceased' && $patient->mortuaryRecord)
            <div class="bg-gray-800 rounded-2xl p-5 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-cross text-white/70"></i>
                    </div>
                    <div>
                        <p class="font-bold text-sm">Transferred to Mortuary</p>
                        <p class="text-white/60 text-xs mt-0.5">
                            Body Tag: <span class="font-semibold text-white/90">{{ $patient->mortuaryRecord->body_tag ?? '—' }}</span>
                            &nbsp;·&nbsp;
                            Cause: <span class="font-semibold text-white/90">{{ $patient->mortuaryRecord->cause_of_death ?? '—' }}</span>
                        </p>
                        @if($patient->mortuaryRecord->notes)
                            <p class="text-white/50 text-xs mt-0.5">{{ $patient->mortuaryRecord->notes }}</p>
                        @endif
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold self-start sm:self-auto
                    {{ $patient->mortuaryRecord->status === 'released' ? 'bg-green-700 text-green-100' : 'bg-white/10 text-white/70' }}">
                    {{ ucfirst($patient->mortuaryRecord->status ?? 'pending') }}
                </span>
            </div>
        @endif

        {{-- ── 3-COLUMN GRID ──────────────────────────────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- LEFT: Patient Details --}}
            <div class="space-y-5">
                <div class="section-card">
                    <h3 class="font-bold text-gray-800 text-sm mb-4 flex items-center gap-2">
                        <i class="fas fa-id-card text-primary-500"></i> Patient Details
                    </h3>
                    <div>
                        <div class="info-row"><span class="info-label">Phone</span><span class="info-value">{{ $patient->phone ?? '—' }}</span></div>
                        <div class="info-row"><span class="info-label">Address</span><span class="info-value">{{ $patient->address ?? '—' }}</span></div>
                        <div class="info-row"><span class="info-label">Emergency Contact</span><span class="info-value">{{ $patient->emergency_contact_name ?? '—' }}</span></div>
                        <div class="info-row"><span class="info-label">Emergency Phone</span><span class="info-value">{{ $patient->emergency_contact_phone ?? '—' }}</span></div>
                        <div class="info-row"><span class="info-label">Ward</span><span class="info-value">{{ $patient->ward ?? '—' }}</span></div>
                        <div class="info-row"><span class="info-label">Status</span><span class="info-value capitalize">{{ $patient->status }}</span></div>
                        @if($patient->status === 'discharged')
                            <div class="info-row"><span class="info-label">Discharged</span><span class="info-value">{{ \Carbon\Carbon::parse($patient->discharged_at)->format('d M Y') }}</span></div>
                            <div class="info-row"><span class="info-label">Condition</span><span class="info-value capitalize">{{ $patient->discharge_condition ?? '—' }}</span></div>
                        @endif
                        @if($patient->status === 'deceased' && $patient->mortuaryRecord)
                            <div class="info-row"><span class="info-label">Body Tag</span><span class="info-value">{{ $patient->mortuaryRecord->body_tag ?? '—' }}</span></div>
                            <div class="info-row"><span class="info-label">Cause of Death</span><span class="info-value">{{ $patient->mortuaryRecord->cause_of_death ?? '—' }}</span></div>
                        @endif
                    </div>
                </div>

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

                {{-- Lab Results --}}
                @if($patient->labRequests->isNotEmpty())
                    <div class="section-card">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                                <i class="fas fa-flask text-secondary-500"></i> Lab Results
                            </h3>
                            <span class="text-xs text-gray-400">{{ $patient->labRequests->count() }} requests</span>
                        </div>
                        <div class="space-y-3">
                            @foreach($patient->labRequests->sortByDesc('created_at') as $req)
                                <div class="p-3 rounded-xl border
                                    {{ $req->result_flag === 'critical' ? 'bg-red-50 border-red-200' :
                                       ($req->result_flag === 'abnormal' ? 'bg-amber-50 border-amber-200' :
                                        ($req->status === 'completed' ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200')) }}">
                                    <div class="flex items-center justify-between mb-1 flex-wrap gap-1">
                                        <span class="text-xs font-bold text-gray-800">{{ $req->test_name }}</span>
                                        <div class="flex items-center gap-1">
                                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                                                {{ $req->status === 'completed'   ? 'bg-green-100 text-green-700' :
                                                   ($req->status === 'in_progress' ? 'bg-blue-100 text-blue-700' :
                                                                                      'bg-amber-100 text-amber-700') }}">
                                                {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                                            </span>
                                            @if($req->result_flag)
                                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                                                    {{ $req->result_flag === 'normal'   ? 'bg-green-100 text-green-700' :
                                                       ($req->result_flag === 'abnormal' ? 'bg-amber-100 text-amber-700' :
                                                                                            'bg-red-100 text-red-700') }}">
                                                    {{ ucfirst($req->result_flag) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($req->results)
                                        <p class="text-xs text-gray-700 leading-relaxed whitespace-pre-line mt-1">{{ $req->results }}</p>
                                        @if($req->interpretation)
                                            <p class="text-xs text-gray-500 italic mt-1">{{ $req->interpretation }}</p>
                                        @endif
                                    @else
                                        <p class="text-xs text-gray-400 italic mt-1">Awaiting results...</p>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-1.5">
                                        Requested {{ $req->created_at->format('d M Y') }}
                                        @if($req->requestedBy) · by Dr. {{ $req->requestedBy->name }} @endif
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- MIDDLE: Clinical Notes --}}
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

                    @if(!in_array($patient->status, ['discharged', 'deceased']))
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

            {{-- RIGHT: Prescriptions --}}
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
                                            {{ $rx->medication_name ?? ($rx->stockItem ? $rx->stockItem->name : 'Unknown Drug') }}
                                        </p>
                                        @if($rx->dispensed_at)
                                            <span class="text-xs text-emerald-600 font-semibold">✓ Dispensed</span>
                                        @else
                                            <span class="text-xs text-amber-600 font-semibold">Pending</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-600">{{ $rx->dosage }} · {{ $rx->frequency }}</p>
                                    @if($rx->duration_days)
                                        <p class="text-xs text-gray-400">Duration: {{ $rx->duration_days }} days</p>
                                    @endif
                                    @if($rx->instructions)
                                        <p class="text-xs text-gray-400 italic">{{ $rx->instructions }}</p>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-1">
                                        By {{ $rx->prescribedBy->name ?? 'Unknown' }} · {{ \Carbon\Carbon::parse($rx->created_at)->format('d M, H:i') }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-gray-400 mb-4">No prescriptions issued yet.</p>
                    @endif

                    @if(!in_array($patient->status, ['discharged', 'deceased']))
                        <a href="{{ route('patients.prescriptions.create', $patient) }}"
                           class="block w-full py-2 text-xs font-semibold text-center text-primary-600 border border-primary-200 rounded-lg hover:bg-primary-50 transition-colors">
                            <i class="fas fa-plus mr-1"></i> New Prescription
                        </a>
                    @endif
                </div>
            </div>

        </div>

        {{-- ── Bill Status Banner ───────────────────────────────────────────── --}}
        @if($bill)
            <div class="bg-white rounded-2xl border {{ in_array($bill->status, ['unpaid','partial']) ? 'border-red-200' : 'border-green-200' }} p-5 flex items-center justify-between">
                <div>
                    <p class="text-sm font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-file-invoice-dollar {{ in_array($bill->status, ['unpaid','partial']) ? 'text-red-500' : 'text-green-500' }}"></i>
                        Hospital Bill — TZS {{ number_format($bill->grand_total, 2) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        Paid: TZS {{ number_format($bill->amount_paid, 2) }} &nbsp;·&nbsp;
                        Balance: <span class="font-bold {{ $bill->balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                            TZS {{ number_format($bill->balance, 2) }}
                        </span>
                        &nbsp;·&nbsp;
                        <span class="font-semibold capitalize
                            {{ $bill->status === 'paid' || $bill->status === 'waived' ? 'text-green-600' :
                               ($bill->status === 'partial' ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ ucfirst($bill->status) }}
                        </span>
                    </p>
                </div>
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-5 flex items-center justify-between">
                <div>
                    <p class="text-sm font-bold text-yellow-800">No bill generated yet</p>
                    <p class="text-xs text-yellow-600 mt-0.5">
                        Generate a bill to send charges to the accountant.
                    </p>
                </div>
                <form method="POST" action="{{ route('patients.generate_bill', $patient) }}">
                    @csrf
                    <button type="submit"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-yellow-500 hover:bg-yellow-600 rounded-xl transition">
                        <i class="fas fa-file-invoice-dollar mr-1"></i> Generate Bill
                    </button>
                </form>
            </div>
        @endif

    </main>
</div>

<script>
    function openMortuaryModal() {
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('timeOfDeath').value = now.toISOString().slice(0, 16);
        document.getElementById('mortuaryModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeMortuaryModal() {
        document.getElementById('mortuaryModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    document.getElementById('mortuaryModal').addEventListener('click', function (e) {
        if (e.target === this) closeMortuaryModal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeMortuaryModal();
    });
</script>
</body>
</html>
