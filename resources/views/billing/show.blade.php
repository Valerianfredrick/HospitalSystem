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

        /* Payment method cards */
        .method-card { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; border: 1.5px solid #e5e7eb; border-radius: 0.875rem; cursor: pointer; transition: all 0.2s; }
        .method-card:hover { border-color: #a78bfa; background: #faf8ff; }
        .method-card.selected { border-color: #7c3aed; background: linear-gradient(135deg, rgba(109,40,217,0.06) 0%, rgba(6,148,162,0.06) 100%); }
        .method-card input[type="radio"] { display: none; }
        .method-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }

        /* Mobile sub-options */
        .mobile-sub { display: none; }
        .mobile-sub.visible { display: block; }
        .provider-btn { display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.875rem; border: 1.5px solid #e5e7eb; border-radius: 0.625rem; cursor: pointer; font-size: 0.8rem; font-weight: 600; transition: all 0.2s; }
        .provider-btn:hover { border-color: #a78bfa; }
        .provider-btn.active { border-color: #7c3aed; background: #f5f3ff; color: #6d28d9; }
        .provider-btn input[type="radio"] { display: none; }

        /* Phone field slide-in */
        .phone-section { overflow: hidden; max-height: 0; opacity: 0; transition: max-height 0.35s ease, opacity 0.3s ease; }
        .phone-section.open { max-height: 120px; opacity: 1; }

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

        {{-- Payment form --}}
        @if(in_array($bill->status, ['unpaid', 'partial']))
            <div class="bg-white rounded-2xl border border-purple-100 p-6 no-print">
                <h3 class="font-bold text-gray-800 text-sm mb-5 flex items-center gap-2">
                    <i class="fas fa-money-bill-wave text-green-500"></i> Record Payment
                </h3>

                @if($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                        <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('billing.process', $bill) }}" class="space-y-5">
                    @csrf @method('PATCH')

                    {{-- Amount --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Amount Paid (TZS) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="amount_paid"
                               value="{{ old('amount_paid', $bill->grand_total) }}"
                               min="0" max="{{ $bill->grand_total }}" required
                               class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-purple-400">
                    </div>

                    {{-- Payment Method --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-3">
                            Payment Method <span class="text-red-500">*</span>
                        </label>

                        <div class="grid grid-cols-2 gap-3" id="methodCards">

                            {{-- Cash --}}
                            <label class="method-card {{ old('payment_method') === 'cash' ? 'selected' : '' }}" id="card-cash">
                                <input type="radio" name="payment_method" value="cash"
                                       {{ old('payment_method') === 'cash' ? 'checked' : '' }}
                                       onchange="selectMethod('cash')">
                                <div class="method-icon bg-green-50 text-green-600">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Cash</p>
                                    <p class="text-xs text-gray-400">Physical payment</p>
                                </div>
                            </label>

                            {{-- Mobile Money --}}
                            <label class="method-card {{ old('payment_method') === 'mobile_money' ? 'selected' : '' }}" id="card-mobile_money">
                                <input type="radio" name="payment_method" value="mobile_money"
                                       {{ old('payment_method') === 'mobile_money' ? 'checked' : '' }}
                                       onchange="selectMethod('mobile_money')">
                                <div class="method-icon bg-blue-50 text-blue-600">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Mobile Money</p>
                                    <p class="text-xs text-gray-400">M-Pesa, Tigo, Airtel…</p>
                                </div>
                            </label>

                            {{-- Insurance --}}
                            <label class="method-card {{ old('payment_method') === 'insurance' ? 'selected' : '' }}" id="card-insurance">
                                <input type="radio" name="payment_method" value="insurance"
                                       {{ old('payment_method') === 'insurance' ? 'checked' : '' }}
                                       onchange="selectMethod('insurance')">
                                <div class="method-icon bg-purple-50 text-purple-600">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Insurance</p>
                                    <p class="text-xs text-gray-400">NHIF, private cover</p>
                                </div>
                            </label>

                            {{-- Bank Transfer --}}
                            <label class="method-card {{ old('payment_method') === 'bank' ? 'selected' : '' }}" id="card-bank">
                                <input type="radio" name="payment_method" value="bank"
                                       {{ old('payment_method') === 'bank' ? 'checked' : '' }}
                                       onchange="selectMethod('bank')">
                                <div class="method-icon bg-amber-50 text-amber-600">
                                    <i class="fas fa-university"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Bank Transfer</p>
                                    <p class="text-xs text-gray-400">Direct bank deposit</p>
                                </div>
                            </label>

                        </div>{{-- /grid --}}

                        {{-- ── Mobile Money sub-form ── --}}
                        <div class="mobile-sub mt-4 {{ old('payment_method') === 'mobile_money' ? 'visible' : '' }}" id="mobileSubForm">
                            <div class="bg-blue-50 rounded-xl p-4 space-y-4 border border-blue-100">

                                <p class="text-xs font-semibold text-blue-700 uppercase tracking-wide">
                                    <i class="fas fa-sim-card mr-1"></i> Select Provider
                                </p>

                                <div class="flex flex-wrap gap-2" id="providerBtns">
                                    @foreach([
                                        ['val' => 'mpesa',   'label' => 'M-Pesa',      'color' => 'text-green-700',  'bg' => 'bg-green-100'],
                                        ['val' => 'tigopesa','label' => 'Tigo Pesa',   'color' => 'text-blue-700',   'bg' => 'bg-blue-100'],
                                        ['val' => 'airtelmoney','label' => 'Airtel Money','color' => 'text-red-700',  'bg' => 'bg-red-100'],
                                        ['val' => 'halopesa','label' => 'HaloPesa',    'color' => 'text-amber-700',  'bg' => 'bg-amber-100'],
                                        ['val' => 'other',   'label' => 'Other',        'color' => 'text-gray-700',  'bg' => 'bg-gray-100'],
                                    ] as $p)
                                        <label class="provider-btn {{ old('mobile_provider') === $p['val'] ? 'active' : '' }}"
                                               id="prov-{{ $p['val'] }}"
                                               onclick="selectProvider('{{ $p['val'] }}')">
                                            <input type="radio" name="mobile_provider" value="{{ $p['val'] }}"
                                                {{ old('mobile_provider') === $p['val'] ? 'checked' : '' }}>
                                            <span class="w-5 h-5 rounded-full {{ $p['bg'] }} {{ $p['color'] }} flex items-center justify-center text-xs font-bold">
                                                {{ strtoupper(substr($p['val'],0,1)) }}
                                            </span>
                                            {{ $p['label'] }}
                                        </label>
                                    @endforeach
                                </div>

                                {{-- Phone number (slides in after provider chosen) --}}
                                <div class="phone-section {{ old('mobile_provider') ? 'open' : '' }}" id="phoneSection">
                                    <label class="block text-xs font-semibold text-blue-700 mb-1.5">
                                        <i class="fas fa-phone mr-1"></i>
                                        Phone Number <span class="text-red-500">*</span>
                                    </label>
                                    <div class="flex gap-2">
                                        <div class="flex items-center px-3 bg-white border border-blue-200 rounded-xl text-sm text-gray-500 font-semibold">
                                            +255
                                        </div>
                                        <input type="tel" name="mobile_phone"
                                               value="{{ old('mobile_phone') }}"
                                               placeholder="7XX XXX XXX"
                                               maxlength="9"
                                               class="flex-1 text-sm border border-blue-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-purple-400 bg-white"
                                               id="mobilePhoneInput">
                                    </div>
                                    <p class="text-xs text-blue-500 mt-1.5">
                                        Enter the number used to send the payment
                                    </p>
                                </div>

                            </div>
                        </div>{{-- /mobileSubForm --}}

                        {{-- ── Insurance sub-form ── --}}
                        <div class="mobile-sub mt-4 {{ old('payment_method') === 'insurance' ? 'visible' : '' }}" id="insuranceSubForm">
                            <div class="bg-purple-50 rounded-xl p-4 space-y-3 border border-purple-100">
                                <p class="text-xs font-semibold text-purple-700 uppercase tracking-wide">
                                    <i class="fas fa-shield-alt mr-1"></i> Insurance Details
                                </p>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-purple-700 mb-1.5">Provider</label>
                                        <select name="insurance_provider"
                                                class="w-full text-sm border border-purple-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-purple-400 bg-white">
                                            <option value="">— Select —</option>
                                            <option value="nhif"    {{ old('insurance_provider') === 'nhif'    ? 'selected' : '' }}>NHIF</option>
                                            <option value="jubilee" {{ old('insurance_provider') === 'jubilee' ? 'selected' : '' }}>Jubilee Insurance</option>
                                            <option value="aon"     {{ old('insurance_provider') === 'aon'     ? 'selected' : '' }}>AON Insurance</option>
                                            <option value="AAR"     {{ old('insurance_provider') === 'AAR'     ? 'selected' : '' }}>AAR Insurance</option>
                                            <option value="other"   {{ old('insurance_provider') === 'other'   ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-purple-700 mb-1.5">Claim / Reference No.</label>
                                        <input type="text" name="insurance_ref"
                                               value="{{ old('insurance_ref') }}"
                                               placeholder="e.g. NHIF-2024-XXXX"
                                               class="w-full text-sm border border-purple-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-purple-400 bg-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ── Bank sub-form ── --}}
                        <div class="mobile-sub mt-4 {{ old('payment_method') === 'bank' ? 'visible' : '' }}" id="bankSubForm">
                            <div class="bg-amber-50 rounded-xl p-4 space-y-3 border border-amber-100">
                                <p class="text-xs font-semibold text-amber-700 uppercase tracking-wide">
                                    <i class="fas fa-university mr-1"></i> Bank Transfer Details
                                </p>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-amber-700 mb-1.5">Bank Name</label>
                                        <select name="bank_name"
                                                class="w-full text-sm border border-amber-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-purple-400 bg-white">
                                            <option value="">— Select —</option>
                                            <option value="crdb"   {{ old('bank_name') === 'crdb'   ? 'selected' : '' }}>CRDB Bank</option>
                                            <option value="nmb"    {{ old('bank_name') === 'nmb'    ? 'selected' : '' }}>NMB Bank</option>
                                            <option value="nbcbank"{{ old('bank_name') === 'nbcbank'? 'selected' : '' }}>NBC Bank</option>
                                            <option value="stanbic"{{ old('bank_name') === 'stanbic'? 'selected' : '' }}>Stanbic Bank</option>
                                            <option value="other"  {{ old('bank_name') === 'other'  ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-amber-700 mb-1.5">Transaction Ref.</label>
                                        <input type="text" name="bank_ref"
                                               value="{{ old('bank_ref') }}"
                                               placeholder="e.g. TXN-XXXXXXXX"
                                               class="w-full text-sm border border-amber-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-purple-400 bg-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>{{-- /payment method --}}

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
    const subForms = {
        mobile_money : document.getElementById('mobileSubForm'),
        insurance    : document.getElementById('insuranceSubForm'),
        bank         : document.getElementById('bankSubForm'),
    };

    function selectMethod(method) {
        // Update card highlight
        document.querySelectorAll('.method-card').forEach(c => c.classList.remove('selected'));
        document.getElementById('card-' + method)?.classList.add('selected');

        // Show/hide sub-forms
        Object.keys(subForms).forEach(key => {
            subForms[key].classList.toggle('visible', key === method);
        });

        // If mobile money, require phone; otherwise clear provider + phone
        if (method !== 'mobile_money') {
            document.querySelectorAll('.provider-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('[name="mobile_provider"]').forEach(r => r.checked = false);
            document.getElementById('phoneSection').classList.remove('open');
            document.getElementById('mobilePhoneInput').required = false;
        }
    }

    function selectProvider(val) {
        document.querySelectorAll('.provider-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('prov-' + val)?.classList.add('active');
        document.querySelector(`[name="mobile_provider"][value="${val}"]`).checked = true;

        // Slide in phone field
        const ps = document.getElementById('phoneSection');
        ps.classList.add('open');
        document.getElementById('mobilePhoneInput').required = true;
        setTimeout(() => document.getElementById('mobilePhoneInput').focus(), 350);
    }

    // Re-apply state on page load (for validation errors / old input)
    (function () {
        const oldMethod = '{{ old('payment_method') }}';
        if (oldMethod) selectMethod(oldMethod);
        const oldProv = '{{ old('mobile_provider') }}';
        if (oldProv) selectProvider(oldProv);
    })();

    // Extra charges
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
