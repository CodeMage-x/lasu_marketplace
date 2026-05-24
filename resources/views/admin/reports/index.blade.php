@extends('layouts.app')
@section('title', 'Reports')
@section('content')
<div class="container-fluid py-4 px-4">
    <h4 class="fw-bold mb-4">Reports</h4>

    <div class="card p-2 mb-3">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                    <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-lasu btn-sm">Filter</button>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Reason</th><th>Reported By</th><th>Item Type</th><th>Status</th><th>Date</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @foreach($reports as $r)
                        <tr>
                            <td class="fw-semibold small">{{ ucfirst(str_replace('_', ' ', $r->reason)) }}</td>
                            <td class="small">{{ $r->reporter->name ?? '—' }}</td>
                            <td class="small text-muted">{{ class_basename($r->reportable_type) }}</td>
                            <td>
                                <span class="badge bg-{{ match($r->status) {
                                    'resolved'=>'success','reviewed'=>'primary',default=>'warning text-dark'
                                } }}">{{ ucfirst($r->status) }}</span>
                            </td>
                            <td class="small text-muted">{{ $r->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    @if($r->status === 'pending')
                                        <form method="POST" action="{{ route('admin.reports.review', $r->id) }}">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-xs btn-outline-warning" style="font-size:.75rem;padding:2px 8px">Review</button>
                                        </form>
                                    @endif
                                    @if($r->status !== 'resolved')
                                        <form method="POST" action="{{ route('admin.reports.resolve', $r->id) }}">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-xs btn-outline-success" style="font-size:.75rem;padding:2px 8px">Resolve</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $reports->links() }}</div>
</div>
@endsection
