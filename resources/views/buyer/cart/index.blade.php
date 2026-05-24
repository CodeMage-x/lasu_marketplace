@extends('layouts.app')
@section('title', 'My Cart')
@section('content')
<div class="container py-4" style="max-width:860px">
    <h4 class="fw-bold mb-4"><i class="bi bi-cart3 me-2"></i>My Cart</h4>

    @if($cartItems->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-cart-x fs-1 d-block mb-3"></i>
            <h5>Your cart is empty</h5>
            <a href="{{ route('listings.index') }}" class="btn btn-lasu mt-2">Browse Listings</a>
        </div>
    @else
        <div class="row g-4">
            <div class="col-lg-8">
                @foreach($cartItems as $item)
                    <div class="card mb-3 p-3">
                        <div class="d-flex gap-3 align-items-start">
                            <img src="{{ $item->listing->primary_image_url }}"
                                 style="width:80px;height:80px;object-fit:cover;border-radius:8px">
                            <div class="flex-grow-1">
                                <a href="{{ route('listings.show', $item->listing_id) }}"
                                   class="text-dark text-decoration-none fw-semibold">
                                    {{ $item->listing->title }}
                                </a>
                                <div class="small text-muted">{{ $item->listing->store->name ?? '' }}</div>
                                <div class="fw-bold mt-1" style="color:var(--lasu-green)">{{ $item->listing->formatted_price }}</div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <form method="POST" action="{{ route('cart.update', $item->id) }}">
                                    @csrf @method('PATCH')
                                    <div class="input-group input-group-sm" style="width:110px">
                                        <button class="btn btn-outline-secondary" type="button"
                                                onclick="this.closest('form').querySelector('input').stepDown();this.closest('form').submit()">−</button>
                                        <input type="number" name="quantity" value="{{ $item->quantity }}"
                                               min="1" max="{{ $item->listing->stock_quantity }}"
                                               class="form-control text-center" style="width:40px"
                                               onchange="this.closest('form').submit()">
                                        <button class="btn btn-outline-secondary" type="button"
                                                onclick="this.closest('form').querySelector('input').stepUp();this.closest('form').submit()">+</button>
                                    </div>
                                </form>
                                <form method="POST" action="{{ route('cart.remove', $item->id) }}">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-link text-danger p-0"><i class="bi bi-trash3"></i></button>
                                </form>
                            </div>
                        </div>
                        <div class="text-end mt-2 small text-muted">
                            Subtotal: <strong style="color:var(--lasu-green)">₦{{ number_format($item->quantity * $item->listing->price, 2) }}</strong>
                        </div>
                    </div>
                @endforeach

                <form method="POST" action="{{ route('cart.clear') }}">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-trash me-1"></i>Clear Cart
                    </button>
                </form>
            </div>

            <div class="col-lg-4">
                <div class="card p-4">
                    <h6 class="fw-bold mb-3">Order Summary</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Items ({{ $cartItems->count() }})</span>
                        <span>₦{{ number_format($total, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold border-top pt-2 mb-4">
                        <span>Total</span>
                        <span style="color:var(--lasu-green)">₦{{ number_format($total, 2) }}</span>
                    </div>
                    <a href="{{ route('buyer.orders.checkout') }}" class="btn btn-lasu w-100 py-2 fw-semibold">
                        <i class="bi bi-credit-card me-1"></i> Proceed to Checkout
                    </a>
                    <a href="{{ route('listings.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
