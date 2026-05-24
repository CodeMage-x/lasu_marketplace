@extends('layouts.app')
@section('title', 'Orders')
@section('content')
<div class="container py-4">
    <h4 class="fw-bold mb-4">Customer Orders</h4>

    @if($orders->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-bag-x fs-1 d-block mb-3"></i>
            <h5>No orders yet</h5>
        </div>
    @else
        @foreach($orders as $order)
            <div class="card mb-3 p-3">
                <div class="d-flex justify-content-between flex-wrap gap-2 align-items-start">
                    <div>
                        <div class="fw-bold">Order #{{ $order->order_number }}</div>
                        <div class="small text-muted">Buyer: {{ $order->buyer->name }} &bull; {{ $order->created_at->format('d M Y') }}</div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold" style="color:var(--lasu-green)">{{ $order->formatted_total }}</div>
                        <span class="badge bg-{{ match($order->order_status) {
                            'completed'=>'success','cancelled'=>'danger',
                            'confirmed','handed_over'=>'primary',default=>'warning text-dark'
                        } }}">{{ ucfirst(str_replace('_',' ',$order->order_status)) }}</span>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-3 flex-wrap">
                    <a href="{{ route('seller.orders.show', $order->id) }}" class="btn btn-sm btn-outline-secondary">View</a>
                    @if($order->isPending())
                        <form method="POST" action="{{ route('seller.orders.confirm', $order->id) }}">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm btn-success">Confirm Order</button>
                        </form>
                    @elseif($order->isConfirmed())
                        <form method="POST" action="{{ route('seller.orders.handedOver', $order->id) }}">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm btn-primary">Mark Handed Over</button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
        {{ $orders->links() }}
    @endif
</div>
@endsection
