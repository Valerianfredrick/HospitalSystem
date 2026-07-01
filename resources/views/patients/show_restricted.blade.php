<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $patient->name }} — {{ config('app.name', 'MediCore HMS') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        body { background: #f8f7ff; }
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
        .badge-admitted { background: #d1fae5; color: #065f46; }
        .badge-critical { background: #fee2e2; color: #991b1b; }
        .badge-observation { background: #fef3c7; color: #92400e; }
        .badge-discharged { background: #f3f4f6; color: #374151; }
        .info-row { display: flex; justify-content: space-between; padding: 0.6rem 0; border-bottom: 1px solid #f5f3ff; font-size: 0.875rem; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #9ca3af; }
        .info-value { color: #374151; font-weight: 500; }
    </style>
</head>
<body class="min-h-screen p-6">

<div class="max-w-2xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('patients.index') }}" class="w-9 h-9 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-primary-600">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="font-bold text-gray-800 text-lg">{{ $patient->name }}</h1>
            <p class="text-xs text-gray-400">Limited view — you are not the attending doctor</p>
        </div>
    </div>

    <div class="bg-amber-50 border border-amber-200 text-amber-700 text-sm rounded-xl p-4 flex items-start gap-2 mb-5">
        <i class="fas fa-lock mt-0.5"></i>
        <div>
            This patient is currently attended by
            <strong>{{ $patient->doctor->name ?? 'no doctor yet' }}</strong>.
            Clinical notes, prescriptions, and lab results are hidden until
            the patient is reassigned to you.
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-primary-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold text-gray-800 text-sm">Patient Overview</h2>
            @php
                $badgeMap = ['critical' => 'badge-critical', 'observation' => 'badge-observation', 'discharged' => 'badge-discharged'];
                $badgeClass = $badgeMap[$patient->status] ?? 'badge-admitted';
            @endphp
            <span class="badge {{ $badgeClass }} capitalize">{{ $patient->status }}</span>
        </div>

        <div class="info-row">
            <span class="info-label">Age / Gender</span>
            <span class="info-value">{{ $patient->age ?? '—' }} / {{ ucfirst($patient->gender) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Phone</span>
            <span class="info-value">{{ $patient->phone ?? '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Ward / Bed</span>
            <span class="info-value">{{ $patient->ward_name ?? '—' }} {{ $patient->bed_number ? '· Bed ' . $patient->bed_number : '' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Attending Doctor</span>
            <span class="info-value">{{ $patient->doctor->name ?? 'Unassigned' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Admitted</span>
            <span class="info-value">{{ $patient->admitted_at?->format('d M Y, H:i') ?? '—' }}</span>
        </div>
    </div>

    <p class="text-xs text-gray-400 mt-4 text-center">
        Need to take over this patient's care? Ask Dr. {{ $patient->doctor->name ?? '(unassigned)' }}
        to reassign them to you from the patient's full record.
    </p>

</div>

</body>
</html>
