<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Lab Request — {{ config('app.name') }}</title>
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
        .info-row { display: flex; justify-content: space-between; padding: 0.6rem 0; border-bottom: 1px solid #f5f3ff; font-size: 0.875rem; }
        .info-row:last-child { border-bottom: none; }
    </style>
</head>
<body>

<aside class="sidebar">
    <div style="padding: 1.5rem 1.5rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.07);">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-gradient-main flex items-center justify-center">
                <i class="fas fa-heartbeat text-white text-sm"></i>
            </div>
            <span class="text-white font-bold text-lg">{{ config('app.name', 'MediCore') }}</span>
        </div>
        <p class="text-xs mt-1 ml-10" style="color:rgba(255,255,255,0.3)">Laboratory Module</p>
    </div>
    <nav style="flex:1; padding: 1.25rem 0.75rem; overflow-y:auto;">
        <a href="{{ route('lab.dashboard') }}" class="nav-item"><span class="icon"><i class="fas fa-th-large"></i></span> Dashboard</a>
        <a href="{{ route('lab.requests') }}" class="nav-item active"><span class="icon"><i class="fas fa-flask"></i></span> Test Requests</a>
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

<div class="main-content">
    <header style="background:white; border-bottom:1px solid #ede9fe; padding:0 2rem; height:64px; display:flex; align-items:center; gap:1rem; position:sticky; top:0; z-index:40;">
        <a href="{{ route('lab.requests') }}" class="text-gray-400 hover:text-purple-600">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="font-bold text-gray-800">Process Lab Request</h1>
            <p class="text-xs text-gray-400">{{ $labRequest->patient->name }} · {{ $labRequest->test_name }}</p>
        </div>
    </header>

    <main class="p-6 max-w-3xl mx-auto space-y-6">

        @if(session('success'))
            <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            </div>
        @endif

        {{-- Request Info --}}
        <div class="bg-white rounded-2xl border border-purple-100 p-6">
            <h2 class="font-bold text-gray-800 text-sm mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle text-purple-500"></i> Request Information
            </h2>
            <div>
                <div class="info-row">
                    <span class="text-gray-400 text-xs font-medium">Patient</span>
                    <span class="font-semibold text-gray-800 text-sm">{{ $labRequest->patient->name }}</span>
                </div>
                <div class="info-row">
                    <span class="text-gray-400 text-xs font-medium">Ward</span>
                    <span class="font-semibold text-gray-800 text-sm">{{ $labRequest->patient->ward ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="text-gray-400 text-xs font-medium">Test Requested</span>
                    <span class="font-semibold text-gray-800 text-sm">{{ $labRequest->test_name }}</span>
                </div>
                <div class="info-row">
                    <span class="text-gray-400 text-xs font-medium">Requested By</span>
                    <span class="font-semibold text-gray-800 text-sm">Dr. {{ $labRequest->requestedBy->name ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="text-gray-400 text-xs font-medium">Date Requested</span>
                    <span class="font-semibold text-gray-800 text-sm">{{ $labRequest->created_at->format('d M Y, H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="text-gray-400 text-xs font-medium">Status</span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                        {{ $labRequest->status === 'completed'   ? 'bg-green-100 text-green-700' :
                           ($labRequest->status === 'in_progress' ? 'bg-blue-100 text-blue-700' :
                                                                     'bg-amber-100 text-amber-700') }}">
                        {{ ucfirst(str_replace('_', ' ', $labRequest->status)) }}
                    </span>
                </div>
                @if($labRequest->clinical_notes)
                    <div class="mt-3 p-3 bg-blue-50 rounded-xl">
                        <p class="text-xs font-semibold text-blue-700 mb-1">Doctor's Notes</p>
                        <p class="text-sm text-blue-800">{{ $labRequest->clinical_notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Start Test Button (if pending) --}}
        @if($labRequest->status === 'pending')
            <form method="POST" action="{{ route('lab.start', $labRequest) }}">
                @csrf @method('PATCH')
                <button type="submit"
                        class="w-full py-3 text-sm font-semibold text-white rounded-xl bg-blue-600 hover:bg-blue-700 transition-colors">
                    <i class="fas fa-play mr-2"></i> Mark as In Progress
                </button>
            </form>
        @endif

        {{-- Submit Results (if in progress or completed) --}}
        @if($labRequest->status !== 'pending')
            <div class="bg-white rounded-2xl border border-purple-100 p-6">
                <h2 class="font-bold text-gray-800 text-sm mb-4 flex items-center gap-2">
                    <i class="fas fa-microscope text-purple-500"></i>
                    {{ $labRequest->status === 'completed' ? 'Submitted Results' : 'Enter Results' }}
                </h2>

                @if($labRequest->status === 'completed')
                    {{-- Read-only results --}}
                    <div class="space-y-3">
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs font-semibold text-gray-500 mb-1">Results</p>
                            <p class="text-sm text-gray-800 whitespace-pre-line">{{ $labRequest->results }}</p>
                        </div>
                        @if($labRequest->interpretation)
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <p class="text-xs font-semibold text-gray-500 mb-1">Interpretation</p>
                                <p class="text-sm text-gray-800">{{ $labRequest->interpretation }}</p>
                            </div>
                        @endif
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500">Result Flag:</span>
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                {{ $labRequest->result_flag === 'normal'   ? 'bg-green-100 text-green-700' :
                                   ($labRequest->result_flag === 'abnormal' ? 'bg-amber-100 text-amber-700' :
                                                                               'bg-red-100 text-red-700') }}">
                                {{ ucfirst($labRequest->result_flag) }}
                            </span>
                            <span class="text-xs text-gray-400 ml-auto">
                                Completed {{ $labRequest->completed_at->format('d M Y, H:i') }}
                                @if($labRequest->assignedTo) · by {{ $labRequest->assignedTo->name }} @endif
                            </span>
                        </div>
                    </div>
                @else
                    {{-- Results form --}}
                    @if($errors->any())
                        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('lab.results', $labRequest) }}" class="space-y-4">
                        @csrf @method('PATCH')
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Results <span class="text-red-500">*</span></label>
                            <textarea name="results" rows="5" required
                                      placeholder="Enter test results, values, observations..."
                                      class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-100 resize-none">{{ old('results') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Interpretation / Comments</label>
                            <textarea name="interpretation" rows="3"
                                      placeholder="Clinical interpretation, recommendations..."
                                      class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-100 resize-none">{{ old('interpretation') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Result Flag <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-3 gap-3">
                                @foreach(['normal' => ['bg-green-50 border-green-300 text-green-700', 'fa-check-circle'], 'abnormal' => ['bg-amber-50 border-amber-300 text-amber-700', 'fa-exclamation-triangle'], 'critical' => ['bg-red-50 border-red-300 text-red-700', 'fa-radiation']] as $flag => [$cls, $icon])
                                    <label class="flex flex-col items-center gap-1 p-3 border-2 rounded-xl cursor-pointer transition-all {{ old('result_flag') === $flag ? $cls : 'border-gray-200 hover:border-gray-300' }}">
                                        <input type="radio" name="result_flag" value="{{ $flag }}" class="sr-only" {{ old('result_flag') === $flag ? 'checked' : '' }}>
                                        <i class="fas {{ $icon }} text-lg {{ old('result_flag') === $flag ? '' : 'text-gray-400' }}"></i>
                                        <span class="text-xs font-semibold capitalize">{{ $flag }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <button type="submit"
                                class="w-full py-3 text-sm font-semibold text-white rounded-xl bg-gradient-main hover:opacity-90 transition-opacity">
                            <i class="fas fa-paper-plane mr-2"></i> Submit Results
                        </button>
                    </form>
                @endif
            </div>
        @endif
    </main>
</div>

<script>
    // Highlight selected result flag
    document.querySelectorAll('input[name="result_flag"]').forEach(radio => {
        radio.addEventListener('change', () => {
            document.querySelectorAll('input[name="result_flag"]').forEach(r => {
                const label = r.closest('label');
                label.className = 'flex flex-col items-center gap-1 p-3 border-2 rounded-xl cursor-pointer transition-all border-gray-200 hover:border-gray-300';
                label.querySelector('i').className = `fas ${r.value === 'normal' ? 'fa-check-circle' : r.value === 'abnormal' ? 'fa-exclamation-triangle' : 'fa-radiation'} text-lg text-gray-400`;
            });
            const label = radio.closest('label');
            const colors = { normal: 'bg-green-50 border-green-300 text-green-700', abnormal: 'bg-amber-50 border-amber-300 text-amber-700', critical: 'bg-red-50 border-red-300 text-red-700' };
            label.className = `flex flex-col items-center gap-1 p-3 border-2 rounded-xl cursor-pointer transition-all ${colors[radio.value]}`;
            label.querySelector('i').className = `fas ${radio.value === 'normal' ? 'fa-check-circle' : radio.value === 'abnormal' ? 'fa-exclamation-triangle' : 'fa-radiation'} text-lg`;
        });
    });
</script>
</body>
</html>
