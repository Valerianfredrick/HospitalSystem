@extends('layouts.app')

@section('title', 'Patients')
@section('page-title', 'All Patients')
@section('page-subtitle', 'Manage and view all patient records')

@section('content')

{{-- Toolbar --}}
<div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
    <div class="search-wrap" style="flex:1;min-width:220px;max-width:360px;">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" class="form-control" placeholder="Search patients by name or ID…">
    </div>

    <select class="form-control" id="statusFilter" style="width:160px;">
        <option value="">All Statuses</option>
        <option value="admitted">Admitted</option>
        <option value="stable">Stable</option>
        <option value="critical">Critical</option>
        <option value="recovering">Recovering</option>
        <option value="observation">Observation</option>
        <option value="discharged">Discharged</option>
    </select>

    <a href="{{ route('patients.create') }}" class="btn btn-primary" style="margin-left:auto;">
        <i class="fas fa-user-plus"></i> Admit New Patient
    </a>
</div>

{{-- Table --}}
<div class="card">
    <div class="table-wrap">
        <table id="patientsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Patient</th>
                    <th>Age / Sex</th>
                    <th>Ward</th>
                    <th>Diagnosis</th>
                    <th>Status</th>
                    <th>Admitted</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $patient)
                @php
                    $map = ['admitted'=>'blue','stable'=>'green','critical'=>'red','observation'=>'amber','recovering'=>'teal','discharged'=>'gray'];
                    $c = $map[$patient->status ?? 'admitted'] ?? 'gray';
                @endphp
                <tr data-status="{{ $patient->status }}" data-search="{{ strtolower($patient->name) }}">
                    <td style="color:var(--text-muted);font-size:12px;">#{{ $patient->id }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:36px;height:36px;border-radius:50%;background:var(--grad);display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff;flex-shrink:0;">
                                {{ strtoupper(substr($patient->name,0,1)) }}
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:13px;">{{ $patient->name }}</div>
                                <div style="font-size:11px;color:var(--text-muted);">{{ $patient->phone ?? '—' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $patient->age ?? '—' }} / {{ ucfirst($patient->gender ?? '—') }}</td>
                    <td>{{ $patient->ward ?? 'General' }}</td>
                    <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $patient->diagnosis ?? '' }}">
                        {{ $patient->diagnosis ?? '—' }}
                    </td>
                    <td><span class="pill {{ $c }}"><i class="fas fa-circle" style="font-size:6px;"></i> {{ ucfirst($patient->status ?? 'Admitted') }}</span></td>
                    <td style="font-size:12px;color:var(--text-muted);">{{ $patient->created_at?->format('d M Y') }}</td>
                    <td>
                        <div style="display:flex;gap:6px;justify-content:center;">
                            <a href="{{ route('patients.show', $patient) }}" class="btn btn-ghost btn-sm" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('patients.edit', $patient) }}" class="btn btn-ghost btn-sm" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($patient->status !== 'discharged')
                            <a href="{{ route('patients.discharge.form', $patient) }}" class="btn btn-danger btn-sm" title="Discharge">
                                <i class="fas fa-sign-out-alt"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:60px;color:var(--text-muted);">
                        <i class="fas fa-user-slash" style="font-size:36px;display:block;margin-bottom:12px;opacity:.3;"></i>
                        No patients found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($patients->hasPages())
    <div style="padding:16px 20px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
        <div style="font-size:13px;color:var(--text-muted);">
            Showing {{ $patients->firstItem() }}–{{ $patients->lastItem() }} of {{ $patients->total() }}
        </div>
        <div style="display:flex;gap:6px;">
            {{ $patients->links('pagination::simple-default') }}
        </div>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
const searchInput  = document.getElementById('searchInput');
const statusFilter = document.getElementById('statusFilter');
const rows         = document.querySelectorAll('#patientsTable tbody tr[data-status]');

function filterTable() {
    const q = searchInput.value.toLowerCase();
    const s = statusFilter.value;
    rows.forEach(row => {
        const matchQ = !q || row.dataset.search.includes(q);
        const matchS = !s || row.dataset.status === s;
        row.style.display = matchQ && matchS ? '' : 'none';
    });
}

searchInput.addEventListener('input', filterTable);
statusFilter.addEventListener('change', filterTable);
</script>
@endpush
