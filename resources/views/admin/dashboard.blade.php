@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('content')
<div class="container-fluid py-4 px-4">
    <h4 class="fw-bold mb-4"><i class="bi bi-speedometer2 me-2"></i>Admin Dashboard</h4>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        @foreach([
            ['label'=>'Total Users',   'value'=>$stats['total_users'],    'icon'=>'bi-people',       'color'=>'#6366f1'],
            ['label'=>'Active Listings','value'=>$stats['active_listings'],'icon'=>'bi-grid',         'color'=>'var(--lasu-green)'],
            ['label'=>'Total Orders',  'value'=>$stats['total_orders'],   'icon'=>'bi-bag',           'color'=>'#f59e0b'],
            ['label'=>'Revenue',       'value'=>'₦'.number_format($stats['total_revenue'],2),'icon'=>'bi-cash-coin','color'=>'var(--lasu-green)'],
            ['label'=>'Pending Stores','value'=>$stats['pending_stores'], 'icon'=>'bi-shop',          'color'=>'#ef4444'],
            ['label'=>'Pending Reports','value'=>$stats['pending_reports'],'icon'=>'bi-flag',         'color'=>'#ef4444'],
        ] as $s)
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card p-3 text-center h-100">
                    <i class="bi {{ $s['icon'] }} fs-2 mb-1" style="color:{{ $s['color'] }}"></i>
                    <div class="fw-bold">{{ $s['value'] }}</div>
                    <div class="text-muted small">{{ $s['label'] }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4">
        {{-- Recent Users --}}
        <div class="col-lg-4">
            <div class="card p-3 h-100">
                <div class="d-flex justify-content-between mb-3">
                    <h6 class="fw-bold mb-0">Recent Users</h6>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">All</a>
                </div>
                @foreach($recentUsers as $u)
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <img src="{{ $u->avatar_url }}" class="rounded-circle" width="32" height="32" style="object-fit:cover">
                        <div class="flex-grow-1">
                            <div class="small fw-semibold">{{ $u->name }}</div>
                            <div class="text-muted" style="font-size:.7rem">{{ ucfirst($u->role) }} &bull; {{ $u->created_at->diffForHumans() }}</div>
                        </div>
                        <span class="badge bg-{{ $u->status === 'active' ? 'success' : 'danger' }} bg-opacity-75 small">{{ $u->status }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Recent Orders --}}
        <div class="col-lg-4">
            <div class="card p-3 h-100">
                <div class="d-flex justify-content-between mb-3">
                    <h6 class="fw-bold mb-0">Recent Orders</h6>
                </div>
                @foreach($recentOrders as $o)
                    <div class="border-bottom pb-2 mb-2 small">
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold">{{ $o->order_number }}</span>
                            <span class="badge bg-{{ match($o->order_status) {'completed'=>'success','cancelled'=>'danger',default=>'warning text-dark'} }}">
                                {{ ucfirst($o->order_status) }}
                            </span>
                        </div>
                        <div class="text-muted">{{ $o->buyer->name }} → {{ $o->seller->name }}</div>
                        <div style="color:var(--lasu-green)" class="fw-bold">{{ $o->formatted_total }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Pending Reports --}}
        <div class="col-lg-4">
            <div class="card p-3 h-100">
                <div class="d-flex justify-content-between mb-3">
                    <h6 class="fw-bold mb-0">Pending Reports</h6>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-secondary">All</a>
                </div>
                @forelse($pendingReports as $r)
                    <div class="border-bottom pb-2 mb-2 small">
                        <div class="fw-semibold">{{ ucfirst($r->reason) }}</div>
                        <div class="text-muted">By {{ $r->reporter->name }} &bull; {{ $r->created_at->diffForHumans() }}</div>
                        <div class="d-flex gap-1 mt-1">
                            <form method="POST" action="{{ route('admin.reports.review', $r->id) }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-xs btn-outline-warning" style="font-size:.7rem;padding:1px 6px">Review</button>
                            </form>
                            <form method="POST" action="{{ route('admin.reports.resolve', $r->id) }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-xs btn-outline-success" style="font-size:.7rem;padding:1px 6px">Resolve</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-muted small">No pending reports.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="row g-3 mt-2">
        @foreach([
            ['href'=>route('admin.users.index'),   'icon'=>'bi-people',    'label'=>'Manage Users'],
            ['href'=>route('admin.stores.index'),  'icon'=>'bi-shop',      'label'=>'Manage Stores'],
            ['href'=>route('admin.listings.index'),'icon'=>'bi-grid',      'label'=>'Manage Listings'],
            ['href'=>route('admin.reports.index'), 'icon'=>'bi-flag',      'label'=>'View Reports'],
            ['href'=>route('admin.zones.index'),   'icon'=>'bi-geo-alt',   'label'=>'Campus Zones'],
        ] as $link)
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ $link['href'] }}" class="text-decoration-none">
                    <div class="card p-3 text-center hover-shadow">
                        <i class="bi {{ $link['icon'] }} fs-3 mb-1" style="color:var(--lasu-green)"></i>
                        <div class="small fw-semibold text-dark">{{ $link['label'] }}</div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>
@endsection
