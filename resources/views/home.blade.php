@extends('layouts.app')
@section('title', 'Home')

@section('content')
{{-- Hero --}}
<div style="background: linear-gradient(135deg, var(--lasu-green) 60%, #0d8a56); color:#fff; padding: 60px 0">
    <div class="container text-center">
        <h1 class="display-5 fw-bold mb-2">Buy & Sell on Campus</h1>
        <p class="lead mb-4">The verified marketplace for LASU students &amp; entrepreneurs.</p>
        <form class="d-flex justify-content-center" action="{{ route('listings.index') }}" method="GET">
            <div class="input-group" style="max-width:500px">
                <input type="search" name="q" class="form-control form-control-lg" placeholder="Search textbooks, electronics, fashion...">
                <button class="btn btn-gold btn-lg px-4" type="submit"><i class="bi bi-search me-1"></i>Search</button>
            </div>
        </form>
    </div>
</div>

{{-- Categories --}}
<div class="container my-5">
    <h4 class="fw-bold mb-3">Browse by Category</h4>
    <div class="row g-3">
        @foreach($categories as $cat)
            <div class="col-6 col-md-3 col-lg-2">
                <a href="{{ route('listings.index', ['category' => $cat->slug]) }}" class="text-decoration-none">
                    <div class="card text-center p-3 h-100 category-card">
                        <i class="bi {{ $cat->icon }} fs-2 mb-2" style="color:var(--lasu-green)"></i>
                        <div class="small fw-semibold text-dark">{{ $cat->name }}</div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>

{{-- Featured Listings --}}
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Latest Listings</h4>
        <a href="{{ route('listings.index') }}" class="btn btn-outline-secondary btn-sm">View all</a>
    </div>
    <div class="row g-3">
        @forelse($featured as $listing)
            <div class="col-6 col-md-4 col-lg-3">
                @include('partials.listing-card', ['listing' => $listing])
            </div>
        @empty
            <div class="col-12 text-center text-muted py-5">No listings yet. Be the first to sell!</div>
        @endforelse
    </div>
</div>

{{-- CTA --}}
@guest
<div class="container my-5">
    <div class="card p-5 text-center" style="background: var(--lasu-light); border: 2px dashed var(--lasu-green)">
        <h4 class="fw-bold">Are you a student entrepreneur?</h4>
        <p class="text-muted">Create a store, list your products, and reach thousands of LASU students.</p>
        <a href="{{ route('register') }}" class="btn btn-lasu btn-lg px-5">Get Started Free</a>
    </div>
</div>
@endguest
@endsection
