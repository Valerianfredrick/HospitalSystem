<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admissions — {{ config('app.name', 'MediCore HMS') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        body { background: #f8f7ff; }
        .patient-table { width: 100%; border-collapse: collapse; }
        .patient-table th { background: #f5f3ff; color: #6d28d9; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.06em; padding: 0.85rem 1rem; text-align: left; font-weight: 700; }
        .patient-table td { padding: 0.9rem 1rem; border-bottom: 1px solid #f5f3ff; font-size: 0.84rem; color: #374151; }
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 999px; font-size: 0.7rem; font-weight: 600; }
        .badge-admitted { background: #d1fae5; color: #065f46; }
        .badge-observation { background: #fef3c7; color: #92400e; }
        .badge-critical { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body class="min-h-screen p-6">

<div class="max-w-5xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('receptionist.dashboard') }}" class="w-9 h-9 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-primary-600">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <h1 class="font-bold text-gray-800 text-lg">Current Admissions</h1>
    </div>

    <form method="GET" class="mb-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or diagnosis…"
               class="w-full sm:w-80 px-4 py-2.5 border border-gray-300 rounded-xl text-sm bg-white">
    </form>

    <div class="bg-white rounded-2xl border border-primary-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="patient-table">
                <thead>
                <tr>
                    <th>Patient</th>
                    <th>Diagnosis</th>
                    <th>Status</th>
                    <th>Doctor</th>
                    <th>Admitted</th>
                </tr>
                </thead>
                <tbody>
                @forelse($patients as $p)
                    <tr>
                        <td class="font-semibold text-gray-800">{{ $p->name }}</td>
                        <td class="text-gray-600 text-xs">{{ $p->diagnosis ?? '—' }}</td>
                        <td>
                            <span class="badge
                                @if($p->status === 'critical') badge-critical
                                @elseif($p->status === 'observation') badge-observation
                                @else badge-admitted
                                @endif capitalize">{{ $p->status }}</span>
                        </td>
                        <td class="text-xs">{{ $p->doctor->name ?? '—' }}</td>
                        <td class="text-gray-400 text-xs">{{ $p->admitted_at?->format('d M Y, H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400">No current admissions.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $patients->links() }}
    </div>
</div>

</body>
</html>
