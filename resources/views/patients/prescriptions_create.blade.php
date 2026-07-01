<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Prescription — {{ $patient->name }}</title>
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

{{-- SIDEBAR --}}
<aside class="sidebar">
    <div style="padding:1.5rem 1.5rem 1rem; border-bottom:1px solid rgba(255,255,255,0.07);">
        <a href="{{ url('/') }}" class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-gradient-main flex items-center justify-center">
                <i class="fas fa-heartbeat text-white text-sm"></i>
            </div>
            <span class="text-white font-bold text-lg">{{ config('app.name', 'MediCore') }}</span>
        </a>
        <p class="text-xs mt-1 ml-10" style="color:rgba(255,255,255,0.3)">Medical Dashboard</p>
    </div>
    <nav style="flex:1; padding:1.25rem 0.75rem; overflow-y:auto;">
        <a href="{{ route('medical.dashboard') }}" class="nav-item"><span class="icon"><i class="fas fa-th-large"></i></span> Dashboard</a>
        <a href="{{ route('patients.index') }}" class="nav-item active"><span class="icon"><i class="fas fa-user-injured"></i></span> All Patients</a>
        <a href="{{ route('patients.admission') }}" class="nav-item"><span class="icon"><i class="fas fa-bed"></i></span> Admissions</a>
        <a href="{{ route('patients.discharge') }}" class="nav-item"><span class="icon"><i class="fas fa-sign-out-alt"></i></span> Discharges</a>
        <a href="{{ route('patients.create') }}" class="nav-item"><span class="icon"><i class="fas fa-user-plus"></i></span> Register Patient</a>
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

{{-- MAIN --}}
<div class="main-content">
    <header style="background:white; border-bottom:1px solid #ede9fe; padding:0 2rem; height:64px; display:flex; align-items:center; gap:1rem; position:sticky; top:0; z-index:40;">
        <a href="{{ route('patients.show', $patient) }}" class="text-gray-400 hover:text-purple-600">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="font-bold text-gray-800">New Prescription</h1>
            <p class="text-xs text-gray-400">{{ $patient->name }} · {{ ucfirst($patient->status) }}</p>
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
                    <p class="text-white/70 text-sm">
                        {{ ucfirst($patient->gender) }} · {{ $patient->age }} yrs
                        @if($patient->ward) · {{ $patient->ward }} Ward @endif
                    </p>
                    <p class="text-white/60 text-xs mt-0.5">Diagnosis: {{ $patient->diagnosis ?? '—' }}</p>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <div class="bg-white rounded-2xl border border-purple-100 p-6 space-y-5">
            <h2 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-file-prescription text-purple-500"></i> Prescription Details
            </h2>

            <form method="POST" action="{{ route('patients.prescriptions.store', $patient) }}" class="space-y-5">
                @csrf

                {{-- Drug selection --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Medication <span class="text-red-500">*</span>
                    </label>
                    <select name="stock_item_id" required
                            class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-100">
                        <option value="">— Select medication —</option>
                        @foreach($stockItems as $item)
                            <option value="{{ $item->id }}"
                                {{ old('stock_item_id') == $item->id ? 'selected' : '' }}
                                {{ $item->quantity < 1 ? 'disabled' : '' }}>
                                {{ $item->name }}
                                ({{ $item->quantity }} in stock)
                                {{ $item->quantity < 1 ? '— OUT OF STOCK' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Dosage & Frequency --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Dosage <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="dosage" value="{{ old('dosage') }}"
                               placeholder="e.g. 500mg, 10ml"
                               required
                               class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-100">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Frequency <span class="text-red-500">*</span>
                        </label>
                        <select name="frequency" required
                                class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-100">
                            <option value="">— Select —</option>
                            <option value="Once daily"         {{ old('frequency') === 'Once daily'         ? 'selected' : '' }}>Once daily</option>
                            <option value="Twice daily"        {{ old('frequency') === 'Twice daily'        ? 'selected' : '' }}>Twice daily (BD)</option>
                            <option value="Three times daily"  {{ old('frequency') === 'Three times daily'  ? 'selected' : '' }}>Three times daily (TDS)</option>
                            <option value="Four times daily"   {{ old('frequency') === 'Four times daily'   ? 'selected' : '' }}>Four times daily (QDS)</option>
                            <option value="Every 6 hours"      {{ old('frequency') === 'Every 6 hours'      ? 'selected' : '' }}>Every 6 hours</option>
                            <option value="Every 8 hours"      {{ old('frequency') === 'Every 8 hours'      ? 'selected' : '' }}>Every 8 hours</option>
                            <option value="Every 12 hours"     {{ old('frequency') === 'Every 12 hours'     ? 'selected' : '' }}>Every 12 hours</option>
                            <option value="As needed (PRN)"    {{ old('frequency') === 'As needed (PRN)'    ? 'selected' : '' }}>As needed (PRN)</option>
                            <option value="Stat (once only)"   {{ old('frequency') === 'Stat (once only)'   ? 'selected' : '' }}>Stat (once only)</option>
                        </select>
                    </div>
                </div>

                {{-- Duration & Quantity --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Duration</label>
                        <input type="text" name="duration" value="{{ old('duration') }}"
                               placeholder="e.g. 5 days, 2 weeks"
                               class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-100">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Quantity</label>
                        <input type="number" name="quantity" value="{{ old('quantity') }}"
                               placeholder="e.g. 14" min="1"
                               class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-100">
                    </div>
                </div>

                {{-- Instructions --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Special Instructions</label>
                    <textarea name="instructions" rows="3"
                              placeholder="e.g. Take after meals, avoid alcohol, store in cool place..."
                              class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-100 resize-none">{{ old('instructions') }}</textarea>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                            class="flex-1 py-2.5 text-sm font-semibold text-white rounded-xl bg-gradient-main hover:opacity-90 transition-opacity">
                        <i class="fas fa-paper-plane mr-2"></i> Issue Prescription
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
