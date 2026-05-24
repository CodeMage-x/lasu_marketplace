@extends('layouts.app')
@section('title', 'Manage Users')
@section('content')
<div class="container-fluid py-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Manage Users</h4>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Dashboard
        </a>
    </div>

    {{-- Filters --}}
    <div class="card p-3 mb-4">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control form-control-sm"
                       placeholder="Search name or email..." value="{{ request('q') }}">
            </div>
            <div class="col-md-2">
                <select name="role" class="form-select form-select-sm">
                    <option value="">All Roles</option>
                    <option value="buyer"  {{ request('role') === 'buyer'  ? 'selected' : '' }}>Buyers</option>
                    <option value="seller" {{ request('role') === 'seller' ? 'selected' : '' }}>Sellers</option>
                    <option value="admin"  {{ request('role') === 'admin'  ? 'selected' : '' }}>Admins</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-lasu btn-sm w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm w-100">Clear</a>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>User</th><th>Role</th><th>Faculty</th><th>Status</th><th>Joined</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr class="{{ $user->trashed() ? 'table-secondary' : '' }}">
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $user->avatar_url }}" class="rounded-circle" width="36" height="36" style="object-fit:cover">
                                    <div>
                                        <div class="fw-semibold small">{{ $user->name }}</div>
                                        <div class="text-muted" style="font-size:.72rem">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-{{ $user->role === 'admin' ? 'dark' : ($user->role === 'seller' ? 'primary' : 'secondary') }}">{{ ucfirst($user->role) }}</span></td>
                            <td class="small text-muted">{{ $user->faculty ?? '—' }}</td>
                            <td>
                                <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'danger' }}">{{ ucfirst($user->status) }}</span>
                                @if($user->trashed()) <span class="badge bg-secondary ms-1">Deleted</span> @endif
                            </td>
                            <td class="small text-muted">{{ $user->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-xs btn-outline-secondary" style="font-size:.75rem;padding:2px 8px">View</a>
                                    @if(!$user->isAdmin() && !$user->trashed())
                                        @if($user->status === 'active')
                                            <form method="POST" action="{{ route('admin.users.suspend', $user->id) }}">
                                                @csrf @method('PATCH')
                                                <button class="btn btn-xs btn-outline-danger" style="font-size:.75rem;padding:2px 8px">Suspend</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.users.activate', $user->id) }}">
                                                @csrf @method('PATCH')
                                                <button class="btn btn-xs btn-outline-success" style="font-size:.75rem;padding:2px 8px">Activate</button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $users->links() }}</div>
</div>
@endsection
