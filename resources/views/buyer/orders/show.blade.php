@extends('layouts.app')
@section('title', 'Order #' . $order->order_number)
@section('content')
<div class="container py-4" style="max-width:780px">
    <a href="{{ route('buyer.orders.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left me-1"></i>Back to Orders
    </a>

    <div class="card p-4 mb-3">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-1">Order #{{ $order->order_number }}</h5>
                <div class="text-muted small">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</div>
            </div>
            <span class="badge fs-6 bg-{{ match($order->order_status) {
                'completed' => 'success',
                'cancelled' => 'danger',
                'confirmed','handed_over' => 'primary',
                default => 'warning text-dark'
            } }}">{{ ucfirst(str_replace('_', ' ', $order->order_status)) }}</span>
        </div>
    </div>

    {{-- Items --}}
    <div class="card p-4 mb-3">
        <h6 class="fw-bold mb-3">Items Ordered</h6>
        @foreach($order->items as $item)
            <div class="d-flex gap-3 align-items-center mb-3">
                <img src="{{ $item->listing?->primary_image_url ?? asset('images/placeholder.png') }}"
                     style="width:64px;height:64px;object-fit:cover;border-radius:8px">
                <div class="flex-grow-1">
                    <div class="fw-semibold">{{ $item->listing_title_snapshot }}</div>
                    <div class="small text-muted">Qty: {{ $item->quantity }} × ₦{{ number_format($item->unit_price, 2) }}</div>
                </div>
                <div class="fw-bold" style="color:var(--lasu-green)">₦{{ number_format($item->subtotal, 2) }}</div>
            </div>
        @endforeach
        <div class="border-top pt-3 d-flex justify-content-between fw-bold">
            <span>Total</span>
            <span style="color:var(--lasu-green)">{{ $order->formatted_total }}</span>
        </div>
    </div>

    {{-- Seller & Payment --}}
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="card p-3 h-100">
                <h6 class="fw-bold mb-2">Seller</h6>
                <div>{{ $order->seller->name }}</div>
                <div class="small text-muted">{{ $order->seller->email }}</div>
                <div class="mt-2">
                    <a href="{{ route('conversations.open', $order->items->first()->listing_id ?? 0) }}"
                       class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-chat-dots me-1"></i>Message Seller
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-3 h-100">
                <h6 class="fw-bold mb-2">Payment</h6>
                <div class="small">Method: <strong>{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</strong></div>
                <div class="small">Status:
                    <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : 'warning text-dark' }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
                @if($order->payment_method === 'online' && $order->payment_status === 'unpaid' && $order->isPending())
                    <a href="{{ route('payment.initiate', $order->id) }}" class="btn btn-sm btn-lasu mt-2">
                        Pay Now
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Meetup --}}
    @if($order->meetupProposal)
        <div class="card p-3 mb-3">
            <h6 class="fw-bold mb-2"><i class="bi bi-geo-alt me-1"></i>Meetup Details</h6>
            <div><strong>Zone:</strong> {{ $order->meetupProposal->campusZone->name }}</div>
            <div><strong>Time:</strong> {{ $order->meetupProposal->proposed_at->format('d M Y, h:i A') }}</div>
            @if($order->meetupProposal->notes)
                <div class="small text-muted mt-1">{{ $order->meetupProposal->notes }}</div>
            @endif
        </div>
    @endif

    {{-- Actions --}}
    <div class="card p-3 mb-3">
        <h6 class="fw-bold mb-2">Actions</h6>
        <div class="d-flex gap-2 flex-wrap">
            @if($order->order_status === 'handed_over')
                <form method="POST" action="{{ route('buyer.orders.confirmReceived', $order->id) }}">
                    @csrf @method('PATCH')
                    <button class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i>Confirm Received
                    </button>
                </form>
            @endif
            @if($order->isPending())
                <form method="POST" action="{{ route('buyer.orders.cancel', $order->id) }}">
                    @csrf @method('PATCH')
                    <button class="btn btn-outline-danger" onclick="return confirm('Cancel this order?')">Cancel Order</button>
                </form>
            @endif
        </div>
    </div>

    {{-- Review form --}}
    @if($order->isCompleted() && !$order->review)
        <div class="card p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-star me-1"></i>Leave a Review</h6>
            <form method="POST" action="{{ route('reviews.store', $order->id) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Rating</label>
                    <div class="d-flex gap-2">
                        @for($i = 1; $i <= 5; $i++)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="rating" value="{{ $i }}" id="star{{ $i }}" required>
                                <label class="form-check-label star fs-5" for="star{{ $i }}">★</label>
                            </div>
                        @endfor
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Comment <span class="text-muted fw-normal">(optional)</span></label>
                    <textarea name="comment" class="form-control" rows="3" placeholder="Share your experience..."></textarea>
                </div>
                <button class="btn btn-lasu">Submit Review</button>
            </form>
        </div>
    @elseif($order->review)
        <div class="card p-3">
            <h6 class="fw-bold mb-1">Your Review</h6>
            <div class="star">{{ str_repeat('★', $order->review->rating) }}{{ str_repeat('☆', 5 - $order->review->rating) }}</div>
            @if($order->review->comment)
                <p class="mt-2 mb-0 text-muted">{{ $order->review->comment }}</p>
            @endif
        </div>
    @endif
</div>
@endsection
