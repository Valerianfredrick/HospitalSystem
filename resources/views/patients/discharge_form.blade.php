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
        .nav-item.active { background: linear-gradient(135deg, #7c3aed 0%, #047481 100%); color: white; }
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
        <a href="{{ route('patients.discharge') }}" class="nav-item active"><span class="icon"><i class="fas fa-sign-out-alt"></i></span> Discharges</a>
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
                    <p class="font-bold text-lg">{{ $patient->name }}</p>
                    <p class="text-white/70 text-sm">{{ ucfirst($patient->gender) }} · {{ $patient->age }} yrs · {{ $patient->ward ?? 'No ward' }} Ward</p>
                    <p class="text-white/60 text-xs mt-0.5">
                        Admitted {{ $patient->admitted_at->format('d M Y') }} ·
                        {{ $patient->days_admitted }} day(s)
                    </p>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════
             PAYMENT STATUS BLOCK — shown in all 3 states
             ══════════════════════════════════════════════════════════════ --}}

        @php
            $bill       = $patient->bills()->latest()->first();
            $billStatus = $bill?->status; // null | unpaid | partial | paid | waived
            $canDischarge = in_array($billStatus, ['paid', 'waived']);
        @endphp

        {{-- ── State 1: No bill yet ─────────────────────────────────── --}}
        @if(!$bill)
            <div class="bg-yellow-50 border-2 border-yellow-200 rounded-2xl p-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center shrink-0">
                        <i class="fas fa-file-invoice-dollar text-yellow-600 text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-yellow-800 text-base">No Bill Generated Yet</h3>
                        <p class="text-yellow-700 text-sm mt-1">
                            A bill must be generated and paid before this patient can be discharged.
                            Generate the bill now to send it to the accountant for processing.
                        </p>

                        {{-- Estimated breakdown --}}
                        @php
                            $bedDays   = max(1, $patient->days_admitted);
                            $bedRate   = 10000;
                            $labCount  = $patient->labRequests()->where('status','completed')->count();
                            $drugsQty  = $patient->prescriptions()->count();
                            $estimated = ($bedDays * $bedRate) + ($labCount * 5000) + ($drugsQty * 500);
                        @endphp
                        <div class="mt-4 bg-white rounded-xl border border-yellow-200 p-4 text-sm space-y-2">
                            <div class="flex justify-between text-gray-600">
                                <span>Bed ({{ $bedDays }} day{{ $bedDays > 1 ? 's' : '' }} × 10,000)</span>
                                <span class="font-semibold">TZS {{ number_format($bedDays * $bedRate) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Lab tests ({{ $labCount }} × 5,000)</span>
                                <span class="font-semibold">TZS {{ number_format($labCount * 5000) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Drugs ({{ $drugsQty }} × 500)</span>
                                <span class="font-semibold">TZS {{ number_format($drugsQty * 500) }}</span>
                            </div>
                            <div class="flex justify-between font-bold text-yellow-800 pt-2 border-t border-yellow-100">
                                <span>Estimated Total</span>
                                <span>TZS {{ number_format($estimated) }}</span>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('patients.generate_bill', $patient) }}" class="mt-4">
                            @csrf
                            <button type="submit"
                                    class="w-full py-2.5 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold rounded-xl transition flex items-center justify-center gap-2">
                                <i class="fas fa-paper-plane"></i> Generate Bill & Send to Accountant
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ── State 2: Bill exists but NOT paid ────────────────────── --}}
        @elseif(!$canDischarge)
            <div class="bg-red-50 border-2 border-red-200 rounded-2xl p-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center shrink-0">
                        <i class="fas fa-lock text-red-600 text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-red-800 text-base">Payment Required Before Discharge</h3>
                        <p class="text-red-700 text-sm mt-1">
                            This patient has an outstanding bill. Discharge is blocked until the accountant
                            marks the bill as <strong>paid</strong> or <strong>waived</strong>.
                        </p>

                        {{-- Bill details --}}
                        <div class="mt-4 bg-white rounded-xl border border-red-200 p-4 text-sm space-y-2">
                            <div class="flex justify-between text-gray-600">
                                <span>Grand Total</span>
                                <span class="font-semibold">TZS {{ number_format($bill->grand_total) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Amount Paid</span>
                                <span class="font-semibold text-green-600">TZS {{ number_format($bill->amount_paid ?? 0) }}</span>
                            </div>
                            <div class="flex justify-between font-bold text-red-700 pt-2 border-t border-red-100">
                                <span>Outstanding Balance</span>
                                <span>TZS {{ number_format($bill->balance) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-500 pt-1">
                                <span>Bill Status</span>
                                <span class="inline-flex items-center gap-1 bg-orange-100 text-orange-700 text-xs font-bold px-2.5 py-0.5 rounded-full capitalize">
                                    <i class="fas fa-clock"></i> {{ ucfirst($bill->status) }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-4 p-3 bg-red-100 rounded-xl text-xs text-red-700 flex items-center gap-2">
                            <i class="fas fa-info-circle shrink-0"></i>
                            The bill has been sent to the accountant. Please wait for payment confirmation
                            or ask the patient to visit the billing desk.
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── State 3: Bill paid / waived — show green clearance ──────── --}}
        @else
            <div class="bg-green-50 border-2 border-green-200 rounded-2xl p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center shrink-0">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-green-800">Payment Cleared</h3>
                        <p class="text-green-700 text-sm">
                            @if($billStatus === 'waived')
                                Bill waived by accountant. Patient is cleared for discharge.
                            @else
                                TZS {{ number_format($bill->grand_total) }} paid in full.
                                Patient is cleared for discharge.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- ══════════════════════════════════════════════════════════════
             DISCHARGE FORM — only shown when payment is cleared
             ══════════════════════════════════════════════════════════════ --}}
        @if($canDischarge)
            <div class="bg-white rounded-2xl border border-purple-100 p-6 space-y-5">
                <h2 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-sign-out-alt text-red-500"></i> Discharge Details
                </h2>

                <form method="POST" action="{{ route('patients.discharge.submit', $patient) }}" class="space-y-5">
                    @csrf @method('PUT')

                    {{-- Condition --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Discharge Condition <span class="text-red-500">*</span>
                        </label>
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

                    {{-- Cause of death --}}
                    <div id="causeOfDeathSection" class="hidden">
                        <div class="p-4 bg-red-50 border border-red-200 rounded-xl mb-3">
                            <p class="text-sm font-semibold text-red-700 flex items-center gap-2">
                                <i class="fas fa-exclamation-triangle"></i>
                                Mortuary will be notified immediately upon submission.
                            </p>
                        </div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Cause of Death <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="cause_of_death" value="{{ old('cause_of_death') }}"
                               placeholder="e.g. Cardiac arrest, Respiratory failure..."
                               class="w-full text-sm border border-red-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-red-400">
                    </div>

                    {{-- Bed rate --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Bed Rate Per Day (TZS)</label>
                        <input type="number" name="bed_rate_per_day" value="{{ old('bed_rate_per_day', 10000) }}" min="0"
                               class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-purple-400">
                        <p class="text-xs text-gray-400 mt-1">Adjust if this patient is in a different ward class.</p>
                    </div>

                    {{-- Discharge notes --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Discharge Notes / Summary <span class="text-red-500">*</span>
                        </label>
                        <textarea name="discharge_notes" rows="4" required
                                  placeholder="Summarize the patient's hospital stay, treatment given, and outcome..."
                                  class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-purple-400 resize-none">{{ old('discharge_notes') }}</textarea>
                    </div>

                    {{-- Follow-up --}}
                    <div id="followupSection">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Follow-up Date (optional)</label>
                        <input type="date" name="followup_date" value="{{ old('followup_date') }}"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
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

        @else
            {{-- Blocked — show cancel only --}}
            <div class="flex justify-center">
                <a href="{{ route('patients.show', $patient) }}"
                   class="px-8 py-2.5 text-sm font-semibold text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">
                    ← Back to Patient
                </a>
            </div>
        @endif

    </main>
</div>

<script>
    const conditionSelect = document.getElementById('conditionSelect');
    const causeSection    = document.getElementById('causeOfDeathSection');
    const followupSection = document.getElementById('followupSection');
    const submitBtn       = document.getElementById('submitBtn');

    if (conditionSelect) {
        conditionSelect.addEventListener('change', function () {
            const isDeceased = this.value === 'deceased';
            causeSection.classList.toggle('hidden', !isDeceased);
            followupSection.classList.toggle('hidden', isDeceased);

            if (submitBtn) {
                if (isDeceased) {
                    submitBtn.innerHTML = '<i class="fas fa-skull mr-2"></i> Discharge & Notify Mortuary';
                    submitBtn.style.background = '#dc2626';
                } else {
                    submitBtn.innerHTML = '<i class="fas fa-sign-out-alt mr-2"></i> Discharge Patient';
                    submitBtn.style.background = '';
                    submitBtn.classList.add('bg-gradient-main');
                }
            }
        });

        if (conditionSelect.value === 'deceased') {
            conditionSelect.dispatchEvent(new Event('change'));
        }
    }
</script>
</body>
</html>
