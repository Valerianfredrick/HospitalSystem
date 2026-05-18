@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Panel')
@section('page-subtitle', 'System overview and user management')

@section('content')

{{-- System Stats --}}
<div class="grid grid-4" style="margin-bottom:24px;">
    <div class="stat-card violet">
        <div class="stat-icon violet"><i class="fas fa-users-cog"></i></div>
        <div class="stat-value">{{ $totalUsers ?? 0 }}</div>
        <div class="stat-label">Total System Users</div>
    </div>
    <div class="stat-card teal">
        <div class="stat-icon teal"><i class="fas fa-user-md"></i></div>
        <div class="stat-value">{{ $doctors ?? 0 }}</div>
        <div class="stat-label">Doctors</div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon green"><i class="fas fa-users"></i></div>
        <div class="stat-value">{{ $totalPatients ?? 0 }}</div>
        <div class="stat-label">Total Patients (All Time)</div>
    </div>
    <div class="stat-card amber">
        <div class="stat-icon amber"><i class="fas fa-pills"></i></div>
        <div class="stat-value">{{ $stockItems ?? 0 }}</div>
        <div class="stat-label">Stock Items Managed</div>
    </div>
</div>

<div class="grid" style="grid-template-columns:1fr 360px;gap:20px;">

    {{-- Users Table --}}
    <div class="card">
        <div class="card-header">
            <i class="fas fa-users-cog" style="color:var(--violet-lt);"></i>
            <div class="card-title">System Users</div>
            <a href="{{ route('admin.users') }}" class="btn btn-ghost btn-sm" style="margin-left:auto;">
                Manage Users <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Patients</th>
                        <th>Last Active</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users ?? [] as $user)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:34px;height:34px;border-radius:50%;background:var(--grad);display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff;font-size:13px;">
                                    {{ strtoupper(substr($user->name,0,1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:600;font-size:13px;">{{ $user->name }}</div>
                                    <div style="font-size:11px;color:var(--text-muted);">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="pill {{ $user->role === 'admin' ? 'violet' : ($user->role === 'doctor' ? 'teal' : 'blue') }}">
                                {{ ucfirst($user->role ?? 'doctor') }}
                            </span>
                        </td>
                        <td>{{ $user->patients_count ?? 0 }}</td>
                        <td style="font-size:12px;color:var(--text-muted);">{{ $user->updated_at?->diffForHumans() }}</td>
                        <td><span class="pill green">Active</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:40px;color:var(--text-muted);">No users found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Right Column --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- System Health --}}
        <div class="card">
            <div class="card-header">
                <i class="fas fa-heartbeat" style="color:#f87171;"></i>
                <div class="card-title">System Health</div>
            </div>
            <div class="card-body">
                @foreach([
                    ['Database', 'Connected', 'green'],
                    ['Storage', '2.4 GB / 50 GB', 'teal'],
                    ['Queue Workers', 'Running', 'green'],
                    ['Cache', 'Active', 'green'],
                ] as [$label,$value,$color])
                <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);">
                    <span style="font-size:13px;color:var(--text-muted);">{{ $label }}</span>
                    <span class="pill {{ $color }}" style="font-size:11px;">{{ $value }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Quick Stats --}}
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-bar" style="color:var(--teal-lt);"></i>
                <div class="card-title">Today's Activity</div>
            </div>
            <div class="card-body">
                @foreach([
                    ['Admissions Today', $todayAdmissions ?? 0, 'violet'],
                    ['Discharges Today', $todayDischarges ?? 0, 'green'],
                    ['Prescriptions Issued', $todayPrescriptions ?? 0, 'teal'],
                    ['Medicines Dispensed', $todayDispensed ?? 0, 'amber'],
                ] as [$label,$val,$color])
                <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);">
                    <span style="font-size:13px;color:var(--text-muted);">{{ $label }}</span>
                    <span style="font-weight:700;font-size:16px;font-family:'Syne',sans-serif;color:var(--{{ $color }}-lt);">{{ $val }}</span>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

@endsection
