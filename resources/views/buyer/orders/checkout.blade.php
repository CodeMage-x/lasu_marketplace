@extends('layouts.app')
@section('title', 'Checkout')
@section('content')
<div class="container py-4" style="max-width:760px">
    <h4 class="fw-bold mb-4"><i class="bi bi-credit-card me-2"></i>Checkout</h4>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card p-4 mb-3">
                <h6 class="fw-bold mb-3">Your Items</h6>
                @foreach($cartItems as $item)
                    <div class="d-flex gap-3 align-items-center mb-3">
                        <img src="{{ $item->listing->primary_image_url }}"
                             style="width:56px;height:56px;object-fit:cover;border-radius:8px">
                        <div class="flex-grow-1">
                            <div class="fw-semibold small">{{ $item->listing->title }}</div>
                            <div class="text-muted" style="font-size:.8rem">Qty: {{ $item->quantity }}</div>
                        </div>
                        <div class="fw-bold small" style="color:var(--lasu-green)">
                            ₦{{ number_format($item->quantity * $item->listing->price, 2) }}
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card p-4">
                <h6 class="fw-bold mb-3">Payment Method</h6>
                <form method="POST" action="{{ route('buyer.orders.place') }}" id="checkoutForm">
                    @csrf
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="payment_method"
                               value="cash_on_meetup" id="cashMeetup" checked>
                        <label class="form-check-label" for="cashMeetup">
                            <strong>Cash on Meetup</strong>
                            <div class="text-muted small">Pay physically when you collect your item on campus.</div>
                        </label>
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="radio" name="payment_method"
                               value="online" id="onlinePayment">
                        <label class="form-check-label" for="onlinePayment">
                            <strong>Pay Online (Paystack)</strong>
                            <div class="text-muted small">Secure online payment with card, bank transfer or USSD.</div>
                        </label>
                    </div>
                    <button class="btn btn-lasu w-100 py-2 fw-semibold">
                        <i class="bi bi-bag-check me-1"></i> Place Order
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card p-4">
                <h6 class="fw-bold mb-3">Order Summary</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span>₦{{ number_format($total, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between fw-bold border-top pt-2 fs-5">
                    <span>Total</span>
                    <span style="color:var(--lasu-green)">₦{{ number_format($total, 2) }}</span>
                </div>
                <div class="mt-3 p-2 rounded small text-muted" style="background:var(--lasu-light)">
                    <i class="bi bi-shield-check me-1 text-success"></i>
                    All sellers are verified LASU students. Meetup locations are campus zones for your safety.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
