<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill — {{ $bill->patient->name }}</title>
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
        @media print {
            .sidebar, header, .no-print { display: none !important; }
            .main-content { margin-left: 0 !important; }
        }
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
    <nav style="flex:1; padding:1.25rem 0.75rem;">
        <a href="{{ route('billing.index') }}" class="nav-item active"><span class="icon"><i class="fas fa-receipt"></i></span> All Bills</a>
    </nav>
    <div style="padding:1rem 0.75rem; border-top:1px solid rgba(255,255,255,0.07);">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item w-full" style="background:none; border:none; cursor:pointer;">
                <span class="icon"><i class="fas fa-sign-out-alt"></i></span> Logout
            </button>
        </form>
    </div>
</aside>

<div class="main-content">
    <header style="background:white; border-bottom:1px solid #ede9fe; padding:0 2rem; height:64px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:40;" class="no-print">
        <div class="flex items-center gap-3">
            <a href="{{ route('billing.index') }}" class="text-gray-400 hover:text-purple-600">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="font-bold text-gray-800">Bill — {{ $bill->patient->name }}</h1>
        </div>
        <button onclick="window.print()" class="px-4 py-2 text-xs font-semibold text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">
            <i class="fas fa-print mr-1"></i> Print
        </button>
    </header>

    <main class="p-6 max-w-3xl mx-auto space-y-6">

        @if(session('success'))
            <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            </div>
        @endif

        {{-- Bill header --}}
        <div class="bg-gradient-main rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/60 text-xs mb-1">PATIENT BILL</p>
                    <h2 class="text-xl font-bold">{{ $bill->patient->name }}</h2>
                    <p class="text-white/70 text-sm">
                        {{ ucfirst($bill->patient->gender) }} · {{ $bill->patient->age }} yrs
                        · {{ $bill->patient->ward ?? '—' }} Ward
                    </p>
                    <p class="text-white/60 text-xs mt-1">
                        Admitted {{ $bill->patient->admitted_at?->format('d M Y') }}
                        → Discharged {{ $bill->patient->discharged_at?->format('d M Y') }}
                        ({{ $bill->bed_days }} day{{ $bill->bed_days > 1 ? 's' : '' }})
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-white/60 text-xs">Grand Total</p>
                    <p class="text-3xl font-bold">TZS {{ number_format($bill->grand_total) }}</p>
                    <span class="mt-1 inline-block px-3 py-1 rounded-full text-xs font-semibold
                        {{ $bill->status === 'paid'    ? 'bg-green-400/30 text-white' :
                           ($bill->status === 'partial' ? 'bg-blue-400/30 text-white'  :
                           ($bill->status === 'waived'  ? 'bg-gray-400/30 text-white'  :
                                                          'bg-red-400/30 text-white')) }}">
                        {{ ucfirst($bill->status) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Line items --}}
        <div class="bg-white rounded-2xl border border-purple-100 p-6">
            <h3 class="font-bold text-gray-800 text-sm mb-4 flex items-center gap-2">
                <i class="fas fa-list text-purple-500"></i> Charge Breakdown
            </h3>
            <div>
                <div class="info-row">
                    <span class="text-gray-500">Bed / Ward ({{ $bill->bed_days }} days × TZS {{ number_format($bill->bed_rate_per_day) }})</span>
                    <span class="font-semibold">TZS {{ number_format($bill->bed_total) }}</span>
                </div>
                <div class="info-row">
                    <span class="text-gray-500">Laboratory Tests</span>
                    <span class="font-semibold">TZS {{ number_format($bill->lab_total) }}</span>
                </div>
                <div class="info-row">
                    <span class="text-gray-500">Medications / Drugs</span>
                    <span class="font-semibold">TZS {{ number_format($bill->drugs_total) }}</span>
                </div>

                {{-- Extra charges --}}
                @if($bill->extra_charges && count($bill->extra_charges))
                    @foreach($bill->extra_charges as $extra)
                        <div class="info-row">
                            <span class="text-gray-500">{{ $extra['label'] }}</span>
                            <span class="font-semibold">TZS {{ number_format($extra['amount']) }}</span>
                        </div>
                    @endforeach
                @endif

                <div class="info-row font-bold text-purple-700 text-base">
                    <span>Grand Total</span>
                    <span>TZS {{ number_format($bill->grand_total) }}</span>
                </div>
                <div class="info-row text-green-700">
                    <span>Amount Paid</span>
                    <span class="font-semibold">TZS {{ number_format($bill->amount_paid) }}</span>
                </div>
                <div class="info-row {{ $bill->balance > 0 ? 'text-red-700' : 'text-green-700' }} font-bold">
                    <span>Balance</span>
                    <span>TZS {{ number_format($bill->balance) }}</span>
                </div>
            </div>
        </div>

        {{-- Lab details --}}
        @if($bill->patient->labRequests->where('status','completed')->count())
            <div class="bg-white rounded-2xl border border-purple-100 p-6">
                <h3 class="font-bold text-gray-800 text-sm mb-3 flex items-center gap-2">
                    <i class="fas fa-flask text-secondary-500"></i> Lab Tests Performed
                </h3>
                <div class="space-y-2">
                    @foreach($bill->patient->labRequests->where('status','completed') as $req)
                        <div class="flex justify-between text-sm py-1.5 border-b border-gray-50">
                            <span class="text-gray-600">{{ $req->test_name }}</span>
                            <span class="text-gray-400 text-xs">{{ $req->completed_at?->format('d M Y') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Payment form (only if unpaid/partial) --}}
        @if(in_array($bill->status, ['unpaid', 'partial']))
            <div class="bg-white rounded-2xl border border-purple-100 p-6 no-print">
                <h3 class="font-bold text-gray-800 text-sm mb-4 flex items-center gap-2">
                    <i class="fas fa-money-bill-wave text-green-500"></i> Record Payment
                </h3>

                @if($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                        <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('billing.process', $bill) }}" class="space-y-4">
                    @csrf @method('PATCH')

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Amount Paid (TZS) <span class="text-red-500">*</span></label>
                            <input type="number" name="amount_paid" value="{{ old('amount_paid', $bill->grand_total) }}"
                                   min="0" max="{{ $bill->grand_total }}" required
                                   class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-purple-400">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Payment Method <span class="text-red-500">*</span></label>
                            <select name="payment_method" required
                                    class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-purple-400">
                                <option value="">— Select —</option>
                                <option value="cash">Cash</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="insurance">Insurance</option>
                                <option value="bank">Bank Transfer</option>
                            </select>
                        </div>
                    </div>

                    {{-- Extra charges --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Additional Charges (optional)</label>
                        <div id="extraCharges" class="space-y-2"></div>
                        <button type="button" onclick="addExtraCharge()"
                                class="mt-2 text-xs text-purple-600 hover:underline">
                            <i class="fas fa-plus mr-1"></i> Add charge
                        </button>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Notes</label>
                        <textarea name="notes" rows="2" placeholder="Payment notes, insurance ref, etc."
                                  class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-purple-400 resize-none">{{ old('notes') }}</textarea>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit"
                                class="flex-1 py-2.5 text-sm font-semibold text-white rounded-xl bg-gradient-main hover:opacity-90">
                            <i class="fas fa-check mr-2"></i> Confirm Payment
                        </button>

                        <form method="POST" action="{{ route('billing.waive', $bill) }}" class="inline">
                            @csrf @method('PATCH')
                            <input type="hidden" name="reason" value="Waived by accountant">
                            <button type="submit"
                                    onclick="return confirm('Waive this entire bill?')"
                                    class="px-4 py-2.5 text-sm font-semibold text-gray-500 border border-gray-200 rounded-xl hover:bg-gray-50">
                                Waive Bill
                            </button>
                        </form>
                    </div>
                </form>
            </div>
        @endif

    </main>
</div>

<script>
    let extraIdx = 0;
    function addExtraCharge() {
        const container = document.getElementById('extraCharges');
        const row = document.createElement('div');
        row.className = 'flex gap-2 items-center';
        row.innerHTML = `
        <input type="text"   name="extra_charges[${extraIdx}][label]"  placeholder="Description"
               class="flex-1 text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:border-purple-400">
        <input type="number" name="extra_charges[${extraIdx}][amount]" placeholder="Amount" min="0"
               class="w-32 text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:border-purple-400">
        <button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600 text-xs">
            <i class="fas fa-times"></i>
        </button>`;
        container.appendChild(row);
        extraIdx++;
    }
</script>
</body>
</html>
