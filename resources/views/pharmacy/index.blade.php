@extends('layouts.app')

@section('title', 'Pharmacy')
@section('page-title', 'Pharmacy & Stock')
@section('page-subtitle', 'Manage medicines, supplies, and dispense prescriptions')

@section('content')

{{-- Summary --}}
<div class="grid grid-4" style="margin-bottom:24px;">
    <div class="stat-card teal">
        <div class="stat-icon teal"><i class="fas fa-pills"></i></div>
        <div class="stat-value">{{ $totalItems ?? 0 }}</div>
        <div class="stat-label">Total Items</div>
    </div>
    <div class="stat-card violet">
        <div class="stat-icon violet"><i class="fas fa-boxes"></i></div>
        <div class="stat-value">{{ $totalStock ?? 0 }}</div>
        <div class="stat-label">Total Units in Stock</div>
    </div>
    <div class="stat-card amber">
        <div class="stat-icon amber"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="stat-value">{{ $lowStockCount ?? 0 }}</div>
        <div class="stat-label">Low Stock Items</div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon green"><i class="fas fa-prescription"></i></div>
        <div class="stat-value">{{ $pendingPrescriptions ?? 0 }}</div>
        <div class="stat-label">Pending Prescriptions</div>
    </div>
</div>

{{-- Tabs --}}
<div style="display:flex;gap:4px;margin-bottom:20px;background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:4px;width:fit-content;">
    <button class="btn tab-btn active" data-tab="stock" style="border-radius:9px;">
        <i class="fas fa-boxes"></i> Stock
    </button>
    <button class="btn tab-btn" data-tab="prescriptions" style="border-radius:9px;">
        <i class="fas fa-prescription-bottle-alt"></i> Pending Prescriptions
    </button>
</div>

{{-- Stock Tab --}}
<div id="tab-stock">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;flex-wrap:wrap;">
        <div class="search-wrap" style="flex:1;max-width:300px;">
            <i class="fas fa-search"></i>
            <input type="text" id="stockSearch" class="form-control" placeholder="Search medicine…">
        </div>
        <select id="catFilter" class="form-control" style="width:150px;">
            <option value="">All Categories</option>
            @foreach(['Antibiotic','Analgesic','Antihypertensive','Antidiabetic','Antihistamine','IV Fluid','Supplement','Other'] as $cat)
            <option>{{ $cat }}</option>
            @endforeach
        </select>
        <a href="{{ route('pharmacy.create') }}" class="btn btn-primary" style="margin-left:auto;">
            <i class="fas fa-plus"></i> Add Medicine
        </a>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Medicine</th>
                        <th>Category</th>
                        <th>Unit</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Expiry</th>
                        <th>Status</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockItems as $item)
                    @php
                        $qty = $item->quantity ?? 0;
                        $low = $qty <= ($item->reorder_level ?? 10);
                        $out = $qty === 0;
                        $statusClass = $out ? 'red' : ($low ? 'amber' : 'green');
                        $statusLabel = $out ? 'Out of Stock' : ($low ? 'Low Stock' : 'In Stock');
                    @endphp
                    <tr data-search="{{ strtolower($item->name) }}" data-cat="{{ $item->category }}">
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:36px;height:36px;border-radius:10px;background:var(--grad-soft);display:flex;align-items:center;justify-content:center;border:1px solid var(--border);">
                                    <i class="fas fa-capsules" style="color:var(--violet-lt);font-size:14px;"></i>
                                </div>
                                <div>
                                    <div style="font-weight:600;font-size:13px;">{{ $item->name }}</div>
                                    @if($item->generic_name)<div style="font-size:11px;color:var(--text-muted);">{{ $item->generic_name }}</div>@endif
                                </div>
                            </div>
                        </td>
                        <td><span class="pill violet" style="font-size:11px;">{{ $item->category ?? 'Other' }}</span></td>
                        <td style="font-size:13px;">{{ $item->unit ?? 'Tablet' }}</td>
                        <td>
                            <div style="font-weight:700;font-size:15px;font-family:'Syne',sans-serif;">{{ $qty }}</div>
                        </td>
                        <td style="font-size:13px;">{{ $item->unit_price ? 'TZS '.number_format($item->unit_price) : '—' }}</td>
                        <td style="font-size:12px;color:var(--text-muted);">
                            {{ $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date)->format('M Y') : '—' }}
                        </td>
                        <td><span class="pill {{ $statusClass }}">{{ $statusLabel }}</span></td>
                        <td>
                            <div style="display:flex;gap:6px;justify-content:center;">
                                <a href="{{ route('pharmacy.restock', $item) }}" class="btn btn-ghost btn-sm" title="Restock">
                                    <i class="fas fa-plus-circle"></i>
                                </a>
                                <a href="{{ route('pharmacy.edit', $item) }}" class="btn btn-ghost btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:60px;color:var(--text-muted);">
                            <i class="fas fa-pills" style="font-size:36px;display:block;margin-bottom:12px;opacity:.3;"></i>
                            No medicines in stock
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($stockItems->hasPages())
        <div style="padding:16px 20px;border-top:1px solid var(--border);">
            {{ $stockItems->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Prescriptions Tab --}}
<div id="tab-prescriptions" style="display:none;">
    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Medicine</th>
                        <th>Dosage</th>
                        <th>Frequency</th>
                        <th>Prescribed By</th>
                        <th>Date</th>
                        <th style="text-align:center;">Dispense</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingRx ?? [] as $rx)
                    <tr>
                        <td>
                            <a href="{{ route('patients.show', $rx->patient) }}" style="font-weight:600;font-size:13px;color:var(--violet-lt);text-decoration:none;">
                                {{ $rx->patient->name ?? '—' }}
                            </a>
                        </td>
                        <td style="font-weight:500;">{{ $rx->stockItem->name ?? $rx->medicine_name ?? '—' }}</td>
                        <td>{{ $rx->dosage ?? '—' }}</td>
                        <td>{{ $rx->frequency ?? '—' }}</td>
                        <td style="font-size:12px;color:var(--text-muted);">Dr. {{ $rx->prescribedBy->name ?? 'Unknown' }}</td>
                        <td style="font-size:12px;color:var(--text-muted);">{{ $rx->created_at?->format('d M Y') }}</td>
                        <td style="text-align:center;">
                            <form method="POST" action="{{ route('pharmacy.dispense', $rx) }}" style="display:inline;">
                                @csrf @method('PUT')
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-check"></i> Dispense
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">
                            <i class="fas fa-check-circle" style="font-size:28px;display:block;margin-bottom:8px;color:#34d399;opacity:.7;"></i>
                            No pending prescriptions
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.tab-btn { background:transparent; color:var(--text-muted); border:none; }
.tab-btn.active { background:var(--grad); color:#fff; box-shadow:0 4px 12px rgba(109,40,217,.3); }
</style>
@endpush

@push('scripts')
<script>
// Tabs
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('tab-stock').style.display        = btn.dataset.tab === 'stock'         ? '' : 'none';
        document.getElementById('tab-prescriptions').style.display = btn.dataset.tab === 'prescriptions' ? '' : 'none';
    });
});

// Stock search
const ss = document.getElementById('stockSearch');
const cf = document.getElementById('catFilter');
const rows = document.querySelectorAll('table tbody tr[data-search]');

function filterStock() {
    const q = ss.value.toLowerCase();
    const c = cf.value;
    rows.forEach(r => {
        const mq = !q || r.dataset.search.includes(q);
        const mc = !c || r.dataset.cat === c;
        r.style.display = mq && mc ? '' : 'none';
    });
}
ss.addEventListener('input', filterStock);
cf.addEventListener('change', filterStock);
</script>
@endpush
