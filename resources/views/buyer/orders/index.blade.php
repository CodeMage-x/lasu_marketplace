@extends('layouts.app')
@section('title', 'My Orders')
@section('content')
<div class="container py-4">
    <h4 class="fw-bold mb-4"><i class="bi bi-bag me-2"></i>My Orders</h4>

    @if($orders->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-bag-x fs-1 d-block mb-3"></i>
            <h5>No orders yet</h5>
            <a href="{{ route('listings.index') }}" class="btn btn-lasu mt-2">Start Shopping</a>
        </div>
    @else
        @foreach($orders as $order)
            <div class="card mb-3 p-3">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <div>
                        <div class="fw-bold">Order #{{ $order->order_number }}</div>
                        <div class="small text-muted">Seller: {{ $order->seller_name_snapshot }} &bull; {{ $order->created_at->format('d M Y') }}</div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold" style="color:var(--lasu-green)">{{ $order->formatted_total }}</div>
                        <span class="badge bg-{{ match($order->order_status) {
                            'completed' => 'success',
                            'cancelled' => 'danger',
                            'confirmed','handed_over' => 'primary',
                            default => 'warning text-dark'
                        } }}">{{ ucfirst(str_replace('_', ' ', $order->order_status)) }}</span>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-2 flex-wrap">
                    @foreach($order->items->take(3) as $item)
                        <span class="small text-muted">{{ $item->listing_title_snapshot }}</span>
                    @endforeach
                </div>
                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('buyer.orders.show', $order->id) }}" class="btn btn-sm btn-outline-secondary">View Details</a>
                    @if($order->isPending())
                        <form method="POST" action="{{ route('buyer.orders.cancel', $order->id) }}">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Cancel this order?')">Cancel</button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
        {{ $orders->links() }}
    @endif
</div>
@endsection
