@extends('layouts.app')
@section('title', 'My Listings')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">My Listings</h4>
        <a href="{{ route('seller.listings.create') }}" class="btn btn-lasu">
            <i class="bi bi-plus-circle me-1"></i>Add Listing
        </a>
    </div>

    @if($listings->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-box fs-1 d-block mb-3"></i>
            <h5>No listings yet</h5>
            <a href="{{ route('seller.listings.create') }}" class="btn btn-lasu mt-2">Create First Listing</a>
        </div>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Listing</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th>Views</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($listings as $listing)
                            <tr class="{{ $listing->trashed() ? 'table-secondary text-muted' : '' }}">
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $listing->primary_image_url }}"
                                             style="width:48px;height:48px;object-fit:cover;border-radius:6px">
                                        <div class="fw-semibold small" style="max-width:200px">
                                            {{ Str::limit($listing->title, 45) }}
                                        </div>
                                    </div>
                                </td>
                                <td class="small">{{ $listing->category->name ?? '—' }}</td>
                                <td class="fw-bold small" style="color:var(--lasu-green)">{{ $listing->formatted_price }}</td>
                                <td class="small">{{ $listing->stock_quantity }}</td>
                                <td>
                                    @if($listing->trashed())
                                        <span class="badge bg-secondary">Deleted</span>
                                    @else
                                        <span class="badge bg-{{ $listing->availability === 'available' ? 'success' : 'warning text-dark' }}">
                                            {{ ucfirst(str_replace('_',' ',$listing->availability)) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ number_format($listing->view_count) }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        @if(!$listing->trashed())
                                            <a href="{{ route('listings.show', $listing->id) }}" class="btn btn-sm btn-outline-secondary" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('seller.listings.edit', $listing->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" action="{{ route('seller.listings.destroy', $listing->id) }}">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this listing?')" title="Delete">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
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
        <div class="mt-3">{{ $listings->links() }}</div>
    @endif
</div>
@endsection
