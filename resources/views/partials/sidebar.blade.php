<aside class="sidebar">
    <div class="sidebar-brand">
        <h1>{{ config('app.name', 'MediCore HMS') }}</h1>
        <p>Hospital Management System</p>
    </div>

    <nav class="sidebar-nav">
        @php $role = auth()->user()->role ?? ''; @endphp

        @if(in_array($role, ['doctor', 'nurse', 'admin']))
            <div class="nav-section">Clinical</div>
            <a href="{{ route('medical.dashboard') }}"
               class="nav-item {{ request()->routeIs('medical.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
            <a href="{{ route('patients.index') }}"
               class="nav-item {{ request()->routeIs('patients.*') ? 'active' : '' }}">
                <i class="fas fa-user-injured"></i> Patients
            </a>
            <a href="{{ route('patients.admission') }}"
               class="nav-item {{ request()->routeIs('patients.admission') ? 'active' : '' }}">
                <i class="fas fa-procedures"></i> Admissions
            </a>
            <a href="{{ route('patients.discharge') }}"
               class="nav-item {{ request()->routeIs('patients.discharge') ? 'active' : '' }}">
                <i class="fas fa-sign-out-alt"></i> Discharges
            </a>
        @endif

        @if(in_array($role, ['pharmacist', 'admin']))
            <div class="nav-section">Pharmacy</div>
            <a href="{{ route('pharmacy.index') }}"
               class="nav-item {{ request()->routeIs('pharmacy.*') ? 'active' : '' }}">
                <i class="fas fa-pills"></i> Stock & Dispensing
            </a>
        @endif

        @if($role === 'admin')
            <div class="nav-section">Administration</div>
            <a href="{{ route('admin.dashboard') }}"
               class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-cogs"></i> Admin Panel
            </a>
            <a href="{{ route('admin.users') }}"
               class="nav-item {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                <i class="fas fa-users-cog"></i> Manage Users
            </a>
        @endif

        <div style="padding-top:32px;border-top:1px solid var(--border);margin-top:24px;">
            <div class="nav-section">Account</div>
            <div style="padding:10px 12px 12px;">
                <div style="font-size:13px;font-weight:600;color:var(--text);">
                    {{ auth()->user()->name ?? 'User' }}
                </div>
                <div style="font-size:11px;color:var(--text-muted);">
                    {{ ucfirst(auth()->user()->role ?? 'staff') }}
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-item danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>

    </nav>
</aside>
