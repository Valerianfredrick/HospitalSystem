@extends('layouts.app')

@section('title', 'Manage Users')
@section('page-title', 'User Management')
@section('page-subtitle', 'Create staff accounts and manage system roles')

@section('content')

    {{-- Create User --}}
    <div class="card" style="margin-bottom:20px;">
        <div class="card-header">
            <i class="fas fa-user-plus" style="color:var(--violet-lt);"></i>
            <div class="card-title">Add User</div>
            <button type="button" class="btn btn-primary btn-sm" style="margin-left:auto;" onclick="toggleCreateForm()">
                <i class="fas fa-plus"></i> <span id="createToggleLabel">New User</span>
            </button>
        </div>
        <div class="card-body" id="createFormWrap" style="display:none;">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="form-row cols-2">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                </div>
                <div class="form-row cols-3">
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" class="form-control" required>
                            <option value="">Select role…</option>
                            @foreach($roles as $role)
                                <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>
                                    {{ ucwords(str_replace('_', ' ', $role)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i> Create User
                </button>
            </form>
        </div>
    </div>

    {{-- Users list --}}
    <div class="card">
        <div class="card-header" style="flex-wrap:wrap;gap:12px;">
            <i class="fas fa-users-cog" style="color:var(--violet-lt);"></i>
            <div class="card-title">System Users</div>

            <form method="GET" action="{{ route('admin.users') }}" style="margin-left:auto;display:flex;gap:8px;flex-wrap:wrap;">
                <div class="search-wrap">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="form-control" placeholder="Search name or email…" value="{{ request('search') }}" style="width:200px;">
                </div>
                <select name="role" class="form-control" style="width:170px;" onchange="this.form.submit()">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role }}" {{ request('role') === $role ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_', ' ', $role)) }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-ghost btn-sm"><i class="fas fa-filter"></i></button>
                @if(request('search') || request('role'))
                    <a href="{{ route('admin.users') }}" class="btn btn-ghost btn-sm"><i class="fas fa-times"></i></a>
                @endif
            </form>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Patients</th>
                    <th>Joined</th>
                    <th>Status</th>
                    <th>Change Role</th>
                </tr>
                </thead>
                <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:34px;height:34px;border-radius:50%;background:var(--grad);display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff;font-size:13px;flex-shrink:0;">
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
                            {{ ucwords(str_replace('_', ' ', $user->role ?? 'doctor')) }}
                        </span>
                        </td>
                        <td>{{ $user->patients_count ?? 0 }}</td>
                        <td style="font-size:12px;color:var(--text-muted);">{{ $user->created_at?->format('d M Y') }}</td>
                        <td><span class="pill green">Active</span></td>
                        <td>
                            @if($user->id === auth()->id())
                                <span class="pill gray">You</span>
                            @else
                                <form method="POST" action="{{ route('admin.users.role', $user) }}" style="margin:0;">
                                    @csrf
                                    @method('PUT')
                                    <select name="role" class="form-control" style="font-size:12px;padding:6px 10px;width:160px;" onchange="this.form.submit()">
                                        @foreach($roles as $role)
                                            <option value="{{ $role }}" {{ $user->role === $role ? 'selected' : '' }}>
                                                {{ ucwords(str_replace('_', ' ', $role)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">No users found</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-top:1px solid var(--border);">
        <span style="font-size:12px;color:var(--text-muted);">
            Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }} users
        </span>
                <div style="display:flex;gap:8px;">
                    @if($users->onFirstPage())
                        <span class="btn btn-ghost btn-sm" style="opacity:.5;cursor:default;">Previous</span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}" class="btn btn-ghost btn-sm">Previous</a>
                    @endif

                    @if($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}" class="btn btn-ghost btn-sm">Next</a>
                    @else
                        <span class="btn btn-ghost btn-sm" style="opacity:.5;cursor:default;">Next</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            function toggleCreateForm() {
                const wrap = document.getElementById('createFormWrap');
                const label = document.getElementById('createToggleLabel');
                const isHidden = wrap.style.display === 'none' || wrap.style.display === '';
                wrap.style.display = isHidden ? 'block' : 'none';
                label.textContent = isHidden ? 'Cancel' : 'New User';
            }

            @if($errors->any() && old('name'))
            document.addEventListener('DOMContentLoaded', toggleCreateForm);
            @endif
        </script>
    @endpush

@endsection
