<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Send Lab Request — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .bg-gradient { background: linear-gradient(135deg, #6d28d9 0%, #0694a2 100%); }
        .text-gradient { background: linear-gradient(135deg, #6d28d9 0%, #0694a2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .form-input:focus { border-color: #a78bfa; box-shadow: 0 0 0 3px rgba(167,139,250,0.2); outline: none; }
        .form-input { transition: border-color 0.15s, box-shadow 0.15s; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

<nav class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
    <span class="text-xl font-bold text-gradient">{{ config('app.name', 'MediCore HMS') }}</span>
    <a href="{{ route('patients.show', $patient) }}" class="text-sm text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left mr-1"></i> Back to {{ $patient->name }}
    </a>
</nav>

<div class="max-w-2xl mx-auto px-4 py-10">

    {{-- Patient card --}}
    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-5 mb-6 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-gradient flex items-center justify-center text-white font-bold text-lg">
            {{ substr($patient->name, 0, 1) }}
        </div>
        <div>
            <p class="font-semibold text-gray-800">{{ $patient->name }}</p>
            <p class="text-sm text-gray-500">{{ $patient->diagnosis ?? 'No diagnosis recorded' }} · Ward: {{ $patient->ward ?? 'N/A' }}</p>
        </div>
        <span class="ml-auto px-3 py-1 rounded-full text-xs font-semibold
            {{ $patient->status === 'critical' ? 'bg-red-100 text-red-600' :
               ($patient->status === 'stable' ? 'bg-green-100 text-green-600' : 'bg-amber-100 text-amber-600') }}">
            {{ ucfirst($patient->status) }}
        </span>
    </div>

    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 overflow-hidden">
        <div class="bg-gradient p-5 text-white">
            <h1 class="text-lg font-bold"><i class="fas fa-flask mr-2"></i>Send Lab Request</h1>
            <p class="text-white/70 text-sm mt-0.5">Specify what the lab attendant should test and measure</p>
        </div>

        <div class="p-6">
            @if ($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('patients.lab.store', $patient) }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Test / Measurement Required <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="test_name" value="{{ old('test_name') }}"
                           placeholder="e.g. Full Blood Count, Malaria RDT, Blood Glucose, Urinalysis..."
                           class="form-input w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm" required>
                    <p class="text-xs text-gray-400 mt-1">Be specific — the lab attendant will see exactly this.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Clinical Notes for Lab</label>
                    <textarea name="clinical_notes" rows="5"
                              placeholder="Describe what to look for, patient symptoms, urgency, or any relevant history the lab attendant should know..."
                              class="form-input w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm resize-none">{{ old('clinical_notes') }}</textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="flex-1 py-3 bg-gradient text-white font-semibold rounded-xl shadow hover:opacity-90 transition text-sm flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane"></i> Send to Lab
                    </button>
                    <a href="{{ route('patients.show', $patient) }}"
                       class="px-6 py-3 border border-gray-300 text-gray-600 font-medium rounded-xl hover:bg-gray-50 transition text-sm flex items-center gap-2">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
