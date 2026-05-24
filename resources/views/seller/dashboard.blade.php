@extends('layouts.app')
@section('title', 'Seller Dashboard')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-shop me-2"></i>Seller Dashboard</h4>
        @if(!$store)
            <a href="{{ route('seller.store.create') }}" class="btn btn-lasu">
                <i class="bi bi-plus-circle me-1"></i>Create Your Store
            </a>
        @else
            <a href="{{ route('seller.listings.create') }}" class="btn btn-lasu">
                <i class="bi bi-plus-circle me-1"></i>Add Listing
            </a>
        @endif
    </div>

    @if($store && !$store->isVerified())
        <div class="alert alert-warning">
            <i class="bi bi-clock me-2"></i>Your store is pending admin verification.
        </div>
    @endif

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        @foreach([
            ['icon'=>'bi-box-seam','label'=>'Total Listings','value'=>$stats['total_listings'],'color'=>'var(--lasu-green)'],
            ['icon'=>'bi-bag-check','label'=>'Pending Orders','value'=>$stats['pending_orders'],'color'=>'#f59e0b'],
            ['icon'=>'bi-check-circle','label'=>'Completed Orders','value'=>$stats['completed_orders'],'color'=>'#10b981'],
            ['icon'=>'bi-cash-coin','label'=>'Total Revenue','value'=>'₦'.number_format($stats['total_revenue'],2),'color'=>'var(--lasu-green)'],
        ] as $stat)
            <div class="col-6 col-lg-3">
                <div class="card p-3 h-100 text-center">
                    <i class="bi {{ $stat['icon'] }} fs-2 mb-1" style="color:{{ $stat['color'] }}"></i>
                    <div class="fw-bold fs-5">{{ $stat['value'] }}</div>
                    <div class="text-muted small">{{ $stat['label'] }}</div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Recent Orders --}}
    <div class="card p-4">
        <div class="d-flex justify-content-between mb-3">
            <h6 class="fw-bold mb-0">Recent Orders</h6>
            <a href="{{ route('seller.orders.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
        </div>
        @if($recentOrders->isEmpty())
            <p class="text-muted small">No orders yet.</p>
        @else
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Order #</th><th>Buyer</th><th>Items</th><th>Total</th><th>Status</th><th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                            <tr>
                                <td class="fw-semibold small">{{ $order->order_number }}</td>
                                <td class="small">{{ $order->buyer->name }}</td>
                                <td class="small">{{ $order->items->count() }} item(s)</td>
                                <td class="fw-bold small" style="color:var(--lasu-green)">{{ $order->formatted_total }}</td>
                                <td>
                                    <span class="badge bg-{{ match($order->order_status) {
                                        'completed'=>'success','cancelled'=>'danger',
                                        'confirmed','handed_over'=>'primary',default=>'warning text-dark'
                                    } }} small">{{ ucfirst(str_replace('_',' ',$order->order_status)) }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('seller.orders.show', $order->id) }}"
                                       class="btn btn-sm btn-outline-secondary">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
