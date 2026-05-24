@extends('layouts.app')
@section('title', 'Manage Stores')
@section('content')
<div class="container-fluid py-4 px-4">
    <h4 class="fw-bold mb-4">Manage Stores</h4>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Store</th><th>Owner</th><th>Status</th><th>Created</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @foreach($stores as $store)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $store->logo_url }}" class="rounded" width="40" height="40" style="object-fit:cover">
                                    <div class="fw-semibold small">{{ $store->name }}</div>
                                </div>
                            </td>
                            <td class="small">{{ $store->user->name ?? '—' }}</td>
                            <td>
                                <span class="badge bg-{{ match($store->status) {
                                    'verified'=>'success','suspended'=>'danger',default=>'warning text-dark'
                                } }}">{{ ucfirst($store->status) }}</span>
                            </td>
                            <td class="small text-muted">{{ $store->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    @if($store->status !== 'verified')
                                        <form method="POST" action="{{ route('admin.stores.verify', $store->id) }}">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-xs btn-outline-success" style="font-size:.75rem;padding:2px 8px">Verify</button>
                                        </form>
                                    @endif
                                    @if($store->status !== 'suspended')
                                        <form method="POST" action="{{ route('admin.stores.suspend', $store->id) }}">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-xs btn-outline-danger" style="font-size:.75rem;padding:2px 8px">Suspend</button>
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
    <div class="mt-3">{{ $stores->links() }}</div>
</div>
@endsection
