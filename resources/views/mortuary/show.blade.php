<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mortuary Record — {{ $record->patient->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        body { background: #f8f7ff; }
        .sidebar { width: 260px; min-height: 100vh; background: #1e1035; position: fixed; left: 0; top: 0; z-index: 50; display: flex; flex-direction: column; }
        .main-content { margin-left: 260px; min-height: 100vh; }
        .nav-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.65rem 0.85rem; border-radius: 10px; color: rgba(255,255,255,0.55); font-size: 0.875rem; font-weight: 500; text-decoration: none; margin-bottom: 2px; }
        .nav-item:hover, .nav-item.active { background: rgba(255,255,255,0.12); color: white; }
        .nav-item .icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.08); font-size: 0.8rem; }
        .bg-gradient-main { background: linear-gradient(135deg, #6d28d9 0%, #0694a2 100%); }
        .info-row { display: flex; justify-content: space-between; padding: 0.6rem 0; border-bottom: 1px solid #f5f3ff; font-size: 0.875rem; }
        .info-row:last-child { border-bottom: none; }
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
        <p class="text-xs mt-1 ml-10" style="color:rgba(255,255,255,0.3)">Mortuary Module</p>
    </div>
    <nav style="flex:1; padding:1.25rem 0.75rem;">
        <a href="{{ route('mortuary.index') }}" class="nav-item active">
            <span class="icon"><i class="fas fa-procedures"></i></span> All Records
        </a>
    </nav>
    <div style="padding:1rem 0.75rem; border-top:1px solid rgba(255,255,255,0.07);">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item w-full" style="background:none;border:none;cursor:pointer;">
                <span class="icon"><i class="fas fa-sign-out-alt"></i></span> Logout
            </button>
        </form>
    </div>
</aside>

<div class="main-content">
    <header style="background:white; border-bottom:1px solid #ede9fe; padding:0 2rem; height:64px; display:flex; align-items:center; gap:1rem; position:sticky; top:0; z-index:40;">
        <a href="{{ route('mortuary.index') }}" class="text-gray-400 hover:text-purple-600">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="font-bold text-gray-800">Mortuary Record</h1>
            <p class="text-xs text-gray-400">{{ $record->patient->name }}</p>
        </div>
    </header>

    <main class="p-6 max-w-2xl mx-auto space-y-6">

        @if(session('success'))
            <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
            </div>
        @endif

        {{-- Patient info --}}
        <div class="bg-gray-800 rounded-2xl p-6 text-white">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-white/10 flex items-center justify-center text-xl font-bold">
                    {{ strtoupper(substr($record->patient->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-lg font-bold">{{ $record->patient->name }}</h2>
                    <p class="text-white/60 text-sm">
                        {{ ucfirst($record->patient->gender) }} · {{ $record->patient->age }} yrs
                        · {{ $record->patient->ward ?? '—' }} Ward
                    </p>
                    <p class="text-white/50 text-xs mt-0.5">
                        Diagnosis: {{ $record->patient->diagnosis ?? '—' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Bill Summary --}}
        @if($bill)
            <div class="bg-white rounded-2xl border {{ in_array($bill->status, ['unpaid','partial']) ? 'border-red-200' : 'border-green-200' }} p-6">
                <h3 class="font-bold text-gray-800 text-sm mb-4 flex items-center gap-2">
                    <i class="fas fa-file-invoice-dollar {{ in_array($bill->status, ['unpaid','partial']) ? 'text-red-500' : 'text-green-500' }}"></i>
                    Hospital Bill Summary
                </h3>
                <div class="space-y-0">
                    <div class="info-row">
                        <span class="text-gray-400 text-xs font-medium">Ward / Bed Charges</span>
                        <span class="font-semibold text-gray-800">TZS {{ number_format($bill->bed_total, 2) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="text-gray-400 text-xs font-medium">Lab Charges</span>
                        <span class="font-semibold text-gray-800">TZS {{ number_format($bill->lab_total, 2) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="text-gray-400 text-xs font-medium">Medication Charges</span>
                        <span class="font-semibold text-gray-800">TZS {{ number_format($bill->drugs_total, 2) }}</span>
                    </div>
                    @if($bill->extra_charges)
                        @foreach($bill->extra_charges as $charge)
                            <div class="info-row">
                                <span class="text-gray-400 text-xs font-medium">{{ $charge['label'] }}</span>
                                <span class="font-semibold text-gray-800">TZS {{ number_format($charge['amount'], 2) }}</span>
                            </div>
                        @endforeach
                    @endif
                    <div class="info-row">
                        <span class="text-gray-600 text-xs font-bold">Grand Total</span>
                        <span class="font-bold text-gray-900">TZS {{ number_format($bill->grand_total, 2) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="text-gray-400 text-xs font-medium">Amount Paid</span>
                        <span class="font-semibold text-green-700">TZS {{ number_format($bill->amount_paid, 2) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="text-gray-600 text-xs font-bold">Balance Due</span>
                        <span class="font-bold text-lg {{ $bill->balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                            TZS {{ number_format($bill->balance, 2) }}
                        </span>
                    </div>
                </div>

                {{-- Bill status badge --}}
                <div class="mt-4 flex items-center justify-between">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        {{ $bill->status === 'paid' || $bill->status === 'waived' ? 'bg-green-100 text-green-700' :
                           ($bill->status === 'partial' ? 'bg-yellow-100 text-yellow-700' :
                                                          'bg-red-100 text-red-700') }}">
                        {{ ucfirst($bill->status) }}
                    </span>
                    @if(in_array($bill->status, ['unpaid', 'partial']))
                        <a href="{{ route('billing.show', $bill) }}"
                           class="px-4 py-2 text-xs font-semibold text-white bg-purple-600 hover:bg-purple-700 rounded-xl transition">
                            <i class="fas fa-cash-register mr-1"></i> Process Payment
                        </a>
                    @endif
                </div>
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-4 text-sm text-yellow-800">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                No bill found for this patient. Please generate a bill before releasing the body.
            </div>
        @endif

        {{-- Record details --}}
        <div class="bg-white rounded-2xl border border-purple-100 p-6">
            <h3 class="font-bold text-gray-800 text-sm mb-4 flex items-center gap-2">
                <i class="fas fa-file-medical text-purple-500"></i> Record Details
            </h3>
            <div>
                <div class="info-row">
                    <span class="text-gray-400 text-xs font-medium">Cause of Death</span>
                    <span class="font-semibold text-gray-800">{{ $record->cause_of_death ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="text-gray-400 text-xs font-medium">Referred By</span>
                    <span class="font-semibold text-gray-800">Dr. {{ $record->referredBy->name ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="text-gray-400 text-xs font-medium">Date Referred</span>
                    <span class="font-semibold text-gray-800">{{ $record->created_at->format('d M Y, H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="text-gray-400 text-xs font-medium">Status</span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                        {{ $record->status === 'released' ? 'bg-green-100 text-green-700' :
                           ($record->status === 'received' ? 'bg-blue-100 text-blue-700' :
                                                             'bg-red-100 text-red-700') }}">
                        {{ ucfirst($record->status) }}
                    </span>
                </div>
                @if($record->received_at)
                    <div class="info-row">
                        <span class="text-gray-400 text-xs font-medium">Received At</span>
                        <span class="font-semibold text-gray-800">{{ $record->received_at->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="text-gray-400 text-xs font-medium">Received By</span>
                        <span class="font-semibold text-gray-800">{{ $record->receivedBy->name ?? '—' }}</span>
                    </div>
                @endif
                @if($record->released_at)
                    <div class="info-row">
                        <span class="text-gray-400 text-xs font-medium">Released At</span>
                        <span class="font-semibold text-gray-800">{{ $record->released_at->format('d M Y, H:i') }}</span>
                    </div>
                @endif
                @if($record->notes)
                    <div class="mt-3 p-3 bg-gray-50 rounded-xl">
                        <p class="text-xs font-semibold text-gray-500 mb-1">Notes</p>
                        <p class="text-sm text-gray-700">{{ $record->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Actions --}}
        @if($record->status === 'pending')
            <form method="POST" action="{{ route('mortuary.receive', $record) }}">
                @csrf @method('PATCH')
                <button type="submit"
                        class="w-full py-3 text-sm font-semibold text-white rounded-xl bg-blue-600 hover:bg-blue-700 transition">
                    <i class="fas fa-check mr-2"></i> Confirm Body Received
                </button>
            </form>

        @elseif($record->status === 'received')
            {{-- Block release if bill unpaid --}}
            @if($bill && in_array($bill->status, ['unpaid', 'partial']))
                <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                    <i class="fas fa-lock mr-1"></i>
                    <strong>Release Blocked.</strong> The patient has an outstanding balance of
                    <strong>TZS {{ number_format($bill->balance, 2) }}</strong>.
                    Relatives must settle the bill before the body can be released.
                    <div class="mt-3">
                        <a href="{{ route('billing.show', $bill) }}"
                           class="inline-block px-4 py-2 text-xs font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl transition">
                            <i class="fas fa-cash-register mr-1"></i> Go to Billing
                        </a>
                    </div>
                </div>
            @elseif(!$bill)
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-xl text-sm text-yellow-800">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    No bill found for this patient. Please generate a bill before releasing the body.
                </div>
            @else
                <div class="bg-white rounded-2xl border border-purple-100 p-6">
                    <h3 class="font-bold text-gray-800 text-sm mb-4">
                        <i class="fas fa-door-open text-green-500 mr-1"></i> Release Body
                    </h3>
                    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
                        <i class="fas fa-check-circle mr-1"></i>
                        Bill is fully settled. You may proceed with releasing the body.
                    </div>
                    <form method="POST" action="{{ route('mortuary.release', $record) }}" class="space-y-4">
                        @csrf @method('PATCH')
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Release Notes</label>
                            <textarea name="notes" rows="3"
                                      placeholder="Recipient name, relationship, ID number..."
                                      class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-purple-400 resize-none"></textarea>
                        </div>
                        <button type="submit"
                                class="w-full py-3 text-sm font-semibold text-white rounded-xl bg-green-600 hover:bg-green-700 transition">
                            <i class="fas fa-door-open mr-2"></i> Release Body
                        </button>
                    </form>
                </div>
            @endif

        @else
            <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 text-center font-semibold">
                <i class="fas fa-check-circle mr-1"></i> Body has been released.
            </div>
        @endif

    </main>
</div>
</body>
</html>
