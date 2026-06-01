<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discharge — {{ $patient->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        body { background: #f8f7ff; }
        .sidebar { width: 260px; min-height: 100vh; background: #1e1035; position: fixed; left: 0; top: 0; z-index: 50; display: flex; flex-direction: column; }
        .main-content { margin-left: 260px; min-height: 100vh; }
        .nav-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.65rem 0.85rem; border-radius: 10px; color: rgba(255,255,255,0.55); font-size: 0.875rem; font-weight: 500; text-decoration: none; margin-bottom: 2px; transition: all 0.2s; }
        .nav-item:hover { background: rgba(255,255,255,0.08); color: white; }
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
    </div>
    <nav style="flex:1; padding:1.25rem 0.75rem; overflow-y:auto;">
        <a href="{{ route('medical.dashboard') }}" class="nav-item"><span class="icon"><i class="fas fa-th-large"></i></span> Dashboard</a>
        <a href="{{ route('patients.index') }}" class="nav-item"><span class="icon"><i class="fas fa-user-injured"></i></span> All Patients</a>
        <a href="{{ route('patients.admission') }}" class="nav-item"><span class="icon"><i class="fas fa-bed"></i></span> Admissions</a>
        <a href="{{ route('patients.discharge') }}" class="nav-item"><span class="icon"><i class="fas fa-sign-out-alt"></i></span> Discharges</a>
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
    <header style="background:white; border-bottom:1px solid #ede9fe; padding:0 2rem; height:64px; display:flex; align-items:center; gap:1rem; position:sticky; top:0; z-index:40;">
        <a href="{{ route('patients.show', $patient) }}" class="text-gray-400 hover:text-purple-600">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="font-bold text-gray-800">Discharge Patient</h1>
            <p class="text-xs text-gray-400">{{ $patient->name }} · Admitted {{ $patient->admitted_at->format('d M Y') }}</p>
        </div>
    </header>

    <main class="p-6 max-w-2xl mx-auto space-y-6">

        @if($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        {{-- Patient summary --}}
        <div class="bg-gradient-main rounded-2xl p-5 text-white">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center text-lg font-bold">
                    {{ strtoupper(substr($patient->name, 0, 1)) }}
                </div>
                <div>
                    <p class="font-bold">{{ $patient->name }}</p>
                    <p class="text-white/70 text-sm">{{ ucfirst($patient->gender) }} · {{ $patient->age }} yrs · {{ $patient->ward ?? 'No ward' }} Ward</p>
                    <p class="text-white/60 text-xs mt-0.5">
                        Admitted {{ $patient->admitted_at->format('d M Y') }} ·
                        {{ $patient->days_admitted }} day(s)
                    </p>
                </div>
            </div>
        </div>

        {{-- Bill preview --}}
        <div class="bg-white rounded-2xl border border-purple-100 p-5">
            <h3 class="font-bold text-gray-800 text-sm mb-3 flex items-center gap-2">
                <i class="fas fa-receipt text-purple-500"></i> Estimated Bill Preview
            </h3>
            @php
                $bedDays   = max(1, $patient->days_admitted);
                $bedRate   = 10000;
                $labCount  = $patient->labRequests()->where('status','completed')->count();
                $drugsQty  = $patient->prescriptions()->sum(\DB::raw('COALESCE(quantity,1)'));
                $estimated = ($bedDays * $bedRate) + ($labCount * 5000) + ($drugsQty * 500);
            @endphp
            <div class="space-y-2 text-sm">
                <div class="flex justify-between py-1.5 border-b border-gray-100">
                    <span class="text-gray-500">Bed charges ({{ $bedDays }} day{{ $bedDays > 1 ? 's' : '' }} × 10,000)</span>
                    <span class="font-semibold">{{ number_format($bedDays * $bedRate) }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-gray-100">
                    <span class="text-gray-500">Lab tests ({{ $labCount }} test{{ $labCount != 1 ? 's' : '' }} × 5,000)</span>
                    <span class="font-semibold">{{ number_format($labCount * 5000) }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-gray-100">
                    <span class="text-gray-500">Drugs ({{ $drugsQty }} units × 500)</span>
                    <span class="font-semibold">{{ number_format($drugsQty * 500) }}</span>
                </div>
                <div class="flex justify-between py-2 font-bold text-purple-700">
                    <span>Estimated Total</span>
                    <span>TZS {{ number_format($estimated) }}</span>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">* Final bill calculated by accountant. Extra charges may apply.</p>
        </div>

        {{-- Discharge form --}}
        <div class="bg-white rounded-2xl border border-purple-100 p-6 space-y-5">
            <h2 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-sign-out-alt text-red-500"></i> Discharge Details
            </h2>

            <form method="POST" action="{{ route('patients.discharge.submit', $patient) }}" class="space-y-5" id="dischargeForm">
                @csrf @method('PUT')

                {{-- Condition --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Discharge Condition <span class="text-red-500">*</span></label>
                    <select name="discharge_condition" id="conditionSelect" required
                            class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-purple-400">
                        <option value="">— Select condition —</option>
                        <option value="recovered"      {{ old('discharge_condition') === 'recovered'      ? 'selected' : '' }}>Recovered</option>
                        <option value="improved"       {{ old('discharge_condition') === 'improved'       ? 'selected' : '' }}>Improved</option>
                        <option value="transferred"    {{ old('discharge_condition') === 'transferred'    ? 'selected' : '' }}>Transferred</option>
                        <option value="self-discharge" {{ old('discharge_condition') === 'self-discharge' ? 'selected' : '' }}>Self Discharge (DAMA)</option>
                        <option value="deceased"       {{ old('discharge_condition') === 'deceased'       ? 'selected' : '' }}>Deceased — Send to Mortuary</option>
                    </select>
                </div>

                {{-- Cause of death (shown only when deceased) --}}
                <div id="causeOfDeathSection" class="hidden">
                    <div class="p-4 bg-red-50 border border-red-200 rounded-xl mb-3">
                        <p class="text-sm font-semibold text-red-700 flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle"></i>
                            Mortuary will be notified immediately upon submission.
                        </p>
                    </div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Cause of Death <span class="text-red-500">*</span></label>
                    <input type="text" name="cause_of_death" value="{{ old('cause_of_death') }}"
                           placeholder="e.g. Cardiac arrest, Respiratory failure..."
                           class="w-full text-sm border border-red-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-red-400">
                </div>

                {{-- Bed rate override --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Bed Rate Per Day (TZS)</label>
                    <input type="number" name="bed_rate_per_day" value="{{ old('bed_rate_per_day', 10000) }}" min="0"
                           class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-purple-400">
                    <p class="text-xs text-gray-400 mt-1">Adjust if this patient is in a different ward class.</p>
                </div>

                {{-- Discharge notes --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Discharge Notes / Summary <span class="text-red-500">*</span></label>
                    <textarea name="discharge_notes" rows="4" required
                              placeholder="Summarize the patient's hospital stay, treatment given, and outcome..."
                              class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-purple-400 resize-none">{{ old('discharge_notes') }}</textarea>
                </div>

                {{-- Follow-up (hidden for deceased) --}}
                <div id="followupSection">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Follow-up Date (optional)</label>
                    <input type="date" name="followup_date" value="{{ old('followup_date') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-purple-400">
                </div>

                {{-- Submit --}}
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" id="submitBtn"
                            class="flex-1 py-2.5 text-sm font-semibold text-white rounded-xl bg-gradient-main hover:opacity-90 transition">
                        <i class="fas fa-sign-out-alt mr-2"></i> Discharge Patient
                    </button>
                    <a href="{{ route('patients.show', $patient) }}"
                       class="px-5 py-2.5 text-sm font-semibold text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
    const conditionSelect     = document.getElementById('conditionSelect');
    const causeSection        = document.getElementById('causeOfDeathSection');
    const followupSection     = document.getElementById('followupSection');
    const submitBtn           = document.getElementById('submitBtn');

    conditionSelect.addEventListener('change', function () {
        const isDeceased = this.value === 'deceased';
        causeSection.classList.toggle('hidden', !isDeceased);
        followupSection.classList.toggle('hidden', isDeceased);

        if (isDeceased) {
            submitBtn.innerHTML = '<i class="fas fa-skull mr-2"></i> Discharge & Notify Mortuary';
            submitBtn.classList.add('bg-red-600');
            submitBtn.classList.remove('bg-gradient-main');
        } else {
            submitBtn.innerHTML = '<i class="fas fa-sign-out-alt mr-2"></i> Discharge Patient';
            submitBtn.classList.remove('bg-red-600');
            submitBtn.classList.add('bg-gradient-main');
        }
    });

    // Restore state on validation error
    if (conditionSelect.value === 'deceased') conditionSelect.dispatchEvent(new Event('change'));
</script>
</body>
</html>
