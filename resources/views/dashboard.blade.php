@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Welcome back, Dr. {{ auth()->user()->name }}')

@section('content')

{{-- ── Stat Cards ── --}}
<div class="grid grid-4" style="margin-bottom:24px;">

    <div class="stat-card violet">
        <div class="stat-icon violet"><i class="fas fa-bed"></i></div>
        <div class="stat-value">{{ $stats['inpatients'] ?? 0 }}</div>
        <div class="stat-label">Current Inpatients</div>
        <div class="stat-change up"><i class="fas fa-arrow-up"></i> 3 admitted today</div>
    </div>

    <div class="stat-card teal">
        <div class="stat-icon teal"><i class="fas fa-users"></i></div>
        <div class="stat-value">{{ $stats['total_patients'] ?? 0 }}</div>
        <div class="stat-label">Total Patients</div>
        <div class="stat-change up"><i class="fas fa-arrow-up"></i> All time</div>
    </div>

    <div class="stat-card green">
        <div class="stat-icon green"><i class="fas fa-sign-out-alt"></i></div>
        <div class="stat-value">{{ $stats['discharged_today'] ?? 0 }}</div>
        <div class="stat-label">Discharged Today</div>
        <div class="stat-change up"><i class="fas fa-check"></i> Successful</div>
    </div>

    <div class="stat-card amber">
        <div class="stat-icon amber"><i class="fas fa-pills"></i></div>
        <div class="stat-value">{{ $stats['low_stock'] ?? 0 }}</div>
        <div class="stat-label">Low Stock Items</div>
        <div class="stat-change down"><i class="fas fa-exclamation-triangle"></i> Needs restock</div>
    </div>

</div>

{{-- ── Main Grid ── --}}
<div class="grid" style="grid-template-columns: 1fr 340px; gap:20px;">

    {{-- Recent Admissions Table --}}
    <div class="card">
        <div class="card-header">
            <i class="fas fa-clipboard-list" style="color:var(--violet-lt)"></i>
            <div class="card-title">Recent Admissions</div>
            <a href="{{ route('patients.admission') }}" class="btn btn-ghost btn-sm" style="margin-left:auto">
                View All <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Ward</th>
                        <th>Status</th>
                        <th>Admitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPatients ?? [] as $patient)
                    @php
                        $colors = ['admitted'=>'blue','stable'=>'green','critical'=>'red','observation'=>'amber','recovering'=>'teal'];
                        $c = $colors[$patient->status ?? 'admitted'] ?? 'gray';
                    @endphp
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:34px;height:34px;border-radius:50%;background:var(--grad);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#fff;flex-shrink:0;">
                                    {{ strtoupper(substr($patient->name,0,1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:600;font-size:13px;">{{ $patient->name }}</div>
                                    <div style="font-size:11px;color:var(--text-muted);">ID #{{ $patient->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $patient->ward ?? 'General' }}</td>
                        <td><span class="pill {{ $c }}">{{ ucfirst($patient->status ?? 'Admitted') }}</span></td>
                        <td style="color:var(--text-muted);font-size:12px;">{{ $patient->created_at?->diffForHumans() }}</td>
                        <td>
                            <a href="{{ route('patients.show', $patient) }}" class="btn btn-ghost btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:40px;color:var(--text-muted);">
                            <i class="fas fa-inbox" style="font-size:28px;margin-bottom:8px;display:block;opacity:.4;"></i>
                            No patients admitted yet
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Right Column --}}
    <div style="display:flex;flex-direction:column;gap:20px;">

        {{-- Quick Actions --}}
        <div class="card">
            <div class="card-header">
                <i class="fas fa-bolt" style="color:var(--teal-lt)"></i>
                <div class="card-title">Quick Actions</div>
            </div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:10px;">
                <a href="{{ route('patients.create') }}" class="btn btn-primary" style="justify-content:center;">
                    <i class="fas fa-user-plus"></i> Admit New Patient
                </a>
                <a href="{{ route('pharmacy.create') }}" class="btn btn-ghost" style="justify-content:center;">
                    <i class="fas fa-plus"></i> Add Medicine Stock
                </a>
                <a href="{{ route('patients.discharge') }}" class="btn btn-ghost" style="justify-content:center;">
                    <i class="fas fa-sign-out-alt"></i> Process Discharge
                </a>
            </div>
        </div>

        {{-- Low Stock Alert --}}
        <div class="card">
            <div class="card-header">
                <i class="fas fa-exclamation-triangle" style="color:#fbbf24"></i>
                <div class="card-title">Low Stock Alert</div>
            </div>
            <div class="card-body" style="padding:0;">
                @forelse($lowStock ?? [] as $item)
                <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 20px;border-bottom:1px solid var(--border);">
                    <div>
                        <div style="font-size:13px;font-weight:600;">{{ $item->name }}</div>
                        <div style="font-size:11px;color:var(--text-muted);">{{ $item->category ?? 'Medicine' }}</div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:13px;font-weight:700;color:#f87171;">{{ $item->quantity }}</div>
                        <div style="font-size:10px;color:var(--text-muted);">units left</div>
                    </div>
                </div>
                @empty
                <div style="padding:20px;text-align:center;color:var(--text-muted);font-size:13px;">
                    <i class="fas fa-check-circle" style="color:#34d399;margin-right:6px;"></i> All stock levels OK
                </div>
                @endforelse
            </div>
        </div>

        {{-- Status Breakdown --}}
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-pie" style="color:var(--violet-lt)"></i>
                <div class="card-title">Patient Status</div>
            </div>
            <div class="card-body">
                @foreach(['stable'=>['green','Stable'],'critical'=>['red','Critical'],'recovering'=>['teal','Recovering'],'observation'=>['amber','Observation']] as $status=>[$color,$label])
                <div style="margin-bottom:12px;">
                    <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:5px;">
                        <span style="color:var(--text-muted);">{{ $label }}</span>
                        <span style="font-weight:600;">{{ $statusCounts[$status] ?? 0 }}</span>
                    </div>
                    <div style="height:5px;background:var(--surface2);border-radius:10px;overflow:hidden;">
                        @php $pct = ($stats['inpatients'] ?? 0) > 0 ? (($statusCounts[$status] ?? 0) / $stats['inpatients']) * 100 : 0; @endphp
                        <div style="height:100%;width:{{ $pct }}%;background:var(--grad);border-radius:10px;transition:width .6s ease;"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

@endsection
