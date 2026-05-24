{{-- ============================================================
     resources/views/listings/index.blade.php
     ============================================================ --}}
@extends('layouts.app')
@section('title', 'Browse Listings')
@section('content')
<div class="container py-4">
    <div class="row g-4">
        {{-- Sidebar filters --}}
        <div class="col-lg-3">
            <div class="card p-3">
                <h6 class="fw-bold mb-3">Filter Listings</h6>
                <form method="GET" action="{{ route('listings.index') }}">
                    <input type="hidden" name="q" value="{{ request('q') }}">

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Category</label>
                        <select name="category" class="form-select form-select-sm">
                            <option value="">All categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Condition</label>
                        <select name="condition" class="form-select form-select-sm">
                            <option value="">Any condition</option>
                            <option value="new"         {{ request('condition') === 'new'         ? 'selected' : '' }}>New</option>
                            <option value="fairly_used" {{ request('condition') === 'fairly_used' ? 'selected' : '' }}>Fairly Used</option>
                            <option value="used"        {{ request('condition') === 'used'        ? 'selected' : '' }}>Used</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Price Range (₦)</label>
                        <div class="d-flex gap-2">
                            <input type="number" name="min_price" class="form-control form-control-sm"
                                   placeholder="Min" value="{{ request('min_price') }}">
                            <input type="number" name="max_price" class="form-control form-control-sm"
                                   placeholder="Max" value="{{ request('max_price') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Sort By</label>
                        <select name="sort" class="form-select form-select-sm">
                            <option value="newest"     {{ request('sort', 'newest') === 'newest'     ? 'selected' : '' }}>Newest First</option>
                            <option value="price_asc"  {{ request('sort') === 'price_asc'  ? 'selected' : '' }}>Price: Low–High</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High–Low</option>
                            <option value="popular"    {{ request('sort') === 'popular'    ? 'selected' : '' }}>Most Viewed</option>
                        </select>
                    </div>

                    <button class="btn btn-lasu btn-sm w-100">Apply Filters</button>
                    <a href="{{ route('listings.index') }}" class="btn btn-outline-secondary btn-sm w-100 mt-2">Clear</a>
                </form>
            </div>
        </div>

        {{-- Listings grid --}}
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="text-muted small">{{ $listings->total() }} listings found</div>
            </div>

            @if($listings->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-search fs-1 d-block mb-2"></i>
                    No listings match your search. Try different filters.
                </div>
            @else
                <div class="row g-3">
                    @foreach($listings as $listing)
                        <div class="col-6 col-md-4">
                            @include('partials.listing-card', ['listing' => $listing])
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">{{ $listings->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
