<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send to Lab — {{ $patient->name }}</title>
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
        .bg-gradient-main { background: linear-gradient(135deg, #6d28d9 0%, #0694a2 100%); }
    </style>
</head>
<body>

{{-- SIDEBAR --}}
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
        <a href="{{ route('medical.dashboard') }}" class="nav-item"><span class="icon"><i class="fas fa-th-large"></i></span> Dashboard</a>
        <a href="{{ route('patients.index') }}" class="nav-item active"><span class="icon"><i class="fas fa-user-injured"></i></span> All Patients</a>
        <a href="{{ route('patients.admission') }}" class="nav-item"><span class="icon"><i class="fas fa-bed"></i></span> Admissions</a>
        <a href="{{ route('patients.discharge') }}" class="nav-item"><span class="icon"><i class="fas fa-sign-out-alt"></i></span> Discharges</a>
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

{{-- MAIN --}}
<div class="main-content">
    <header style="background:white; border-bottom:1px solid #ede9fe; padding:0 2rem; height:64px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:40;">
        <div class="flex items-center gap-3">
            <a href="{{ route('patients.show', $patient) }}" class="text-gray-400 hover:text-purple-600">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="font-bold text-gray-800">Send to Lab</h1>
                <p class="text-xs text-gray-400">{{ $patient->name }} · {{ ucfirst($patient->status) }}</p>
            </div>
        </div>
    </header>

    <main class="p-6 max-w-2xl mx-auto">

        @if($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        {{-- Patient summary --}}
        <div class="bg-gradient-main rounded-2xl p-5 text-white mb-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center text-lg font-bold">
                    {{ strtoupper(substr($patient->name, 0, 1)) }}
                </div>
                <div>
                    <p class="font-bold">{{ $patient->name }}</p>
                    <p class="text-white/70 text-sm">{{ ucfirst($patient->gender) }} · {{ $patient->age }} yrs · {{ $patient->ward ?? 'No ward' }} Ward</p>
                    <p class="text-white/60 text-xs mt-0.5">Diagnosis: {{ $patient->diagnosis ?? '—' }}</p>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <div class="bg-white rounded-2xl border border-purple-100 p-6 space-y-5">
            <h2 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-flask text-secondary-500"></i> Lab Request Details
            </h2>

            <form method="POST" action="{{ route('patients.lab.store', $patient) }}" class="space-y-5">
                @csrf

                {{-- Test Name --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Test Name <span class="text-red-500">*</span></label>
                    <select name="test_name" required
                            class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-100">
                        <option value="">— Select a test —</option>
                        <optgroup label="Haematology">
                            <option value="Full Blood Count (FBC)">Full Blood Count (FBC)</option>
                            <option value="Haemoglobin (Hb)">Haemoglobin (Hb)</option>
                            <option value="Blood Group & Cross Match">Blood Group & Cross Match</option>
                            <option value="Erythrocyte Sedimentation Rate (ESR)">ESR</option>
                            <option value="Prothrombin Time (PT)">Prothrombin Time (PT)</option>
                        </optgroup>
                        <optgroup label="Biochemistry">
                            <option value="Random Blood Sugar (RBS)">Random Blood Sugar (RBS)</option>
                            <option value="Fasting Blood Sugar (FBS)">Fasting Blood Sugar (FBS)</option>
                            <option value="Renal Function Tests (RFT)">Renal Function Tests (RFT)</option>
                            <option value="Liver Function Tests (LFT)">Liver Function Tests (LFT)</option>
                            <option value="Lipid Profile">Lipid Profile</option>
                            <option value="Uric Acid">Uric Acid</option>
                            <option value="Serum Electrolytes">Serum Electrolytes</option>
                            <option value="Thyroid Function Tests (TFT)">Thyroid Function Tests (TFT)</option>
                        </optgroup>
                        <optgroup label="Microbiology">
                            <option value="Malaria RDT">Malaria RDT</option>
                            <option value="Malaria Smear">Malaria Smear</option>
                            <option value="Urine Culture & Sensitivity">Urine Culture & Sensitivity</option>
                            <option value="Blood Culture">Blood Culture</option>
                            <option value="Stool Microscopy">Stool Microscopy</option>
                            <option value="Sputum AFB (TB)">Sputum AFB (TB)</option>
                            <option value="HIV Rapid Test">HIV Rapid Test</option>
                            <option value="Hepatitis B Surface Antigen">Hepatitis B Surface Antigen</option>
                            <option value="Widal Test">Widal Test</option>
                        </optgroup>
                        <optgroup label="Urinalysis">
                            <option value="Urinalysis (Routine)">Urinalysis (Routine)</option>
                            <option value="Urine Pregnancy Test (UPT)">Urine Pregnancy Test (UPT)</option>
                        </optgroup>
                        <optgroup label="Other">
                            <option value="Other">Other (specify in notes)</option>
                        </optgroup>
                    </select>
                </div>

                {{-- Clinical Notes --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Clinical Notes / Reason for Test</label>
                    <textarea name="clinical_notes" rows="4"
                              placeholder="Describe clinical indication, symptoms, or what to look for..."
                              class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-100 resize-none">{{ old('clinical_notes') }}</textarea>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                            class="flex-1 py-2.5 text-sm font-semibold text-white rounded-xl bg-gradient-main hover:opacity-90 transition-opacity">
                        <i class="fas fa-paper-plane mr-2"></i> Submit Lab Request
                    </button>
                    <a href="{{ route('patients.show', $patient) }}"
                       class="px-5 py-2.5 text-sm font-semibold text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
