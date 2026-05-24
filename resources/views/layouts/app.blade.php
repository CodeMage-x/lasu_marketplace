<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LASU Marketplace') — Buy & Sell on Campus</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --lasu-green: #0a6640;
            --lasu-gold:  #c8960c;
            --lasu-light: #f0f9f4;
        }
        body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .navbar-brand { font-weight: 800; font-size: 1.4rem; color: var(--lasu-green) !important; }
        .navbar { background: #fff; border-bottom: 3px solid var(--lasu-green); box-shadow: 0 2px 8px rgba(0,0,0,.06); }
        .btn-lasu  { background: var(--lasu-green); color: #fff; }
        .btn-lasu:hover { background: #084d30; color: #fff; }
        .btn-gold  { background: var(--lasu-gold); color: #fff; }
        .btn-gold:hover { background: #a37a09; color: #fff; }
        .badge-lasu { background: var(--lasu-green); }
        .card { border: none; box-shadow: 0 2px 12px rgba(0,0,0,.07); border-radius: 12px; }
        .listing-card:hover { transform: translateY(-3px); transition: .2s; box-shadow: 0 6px 20px rgba(0,0,0,.12); }
        .sidebar { min-height: 100vh; background: var(--lasu-green); }
        .sidebar a { color: rgba(255,255,255,.85); }
        .sidebar a:hover, .sidebar a.active { color: #fff; background: rgba(255,255,255,.15); border-radius: 8px; }
        .notification-dot { width: 8px; height: 8px; background: #ef4444; border-radius: 50%; display: inline-block; }
        .star { color: var(--lasu-gold); }
        .price-tag { color: var(--lasu-green); font-weight: 700; font-size: 1.1rem; }
        footer { background: var(--lasu-green); color: rgba(255,255,255,.8); }
        @yield('extra-styles')
    </style>

    @stack('styles')
</head>
<body>

{{-- ── Navbar ── --}}
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <i class="bi bi-shop-window me-1"></i> LASU Market
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            {{-- Search bar --}}
            <form class="d-flex mx-auto" style="width:40%" action="{{ route('listings.index') }}" method="GET">
                <div class="input-group">
                    <input type="search" name="q" class="form-control" placeholder="Search listings..." value="{{ request('q') }}">
                    <button class="btn btn-lasu" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </form>

            <ul class="navbar-nav ms-auto align-items-center gap-2">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('listings.index') }}">Browse</a>
                </li>

                @guest
                    <li class="nav-item">
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-lasu btn-sm" href="{{ route('register') }}">Register</a>
                    </li>
                @endguest

                @auth
                    {{-- Cart (buyers) --}}
                    @if(auth()->user()->isBuyer())
                        <li class="nav-item">
                            <a class="nav-link position-relative" href="{{ route('cart.index') }}">
                                <i class="bi bi-cart3 fs-5"></i>
                                @php $cartCount = auth()->user()->cartItems()->count(); @endphp
                                @if($cartCount > 0)
                                    <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle" style="font-size:.6rem">{{ $cartCount }}</span>
                                @endif
                            </a>
                        </li>
                    @endif

                    {{-- Messages --}}
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="{{ route('conversations.index') }}">
                            <i class="bi bi-chat-dots fs-5"></i>
                            @php
                                $unread = auth()->user()->notifications()->whereNull('read_at')
                                    ->where('data->type', 'new_message')->count();
                            @endphp
                            @if($unread > 0)
                                <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle" style="font-size:.6rem">{{ $unread }}</span>
                            @endif
                        </a>
                    </li>

                    {{-- Notifications --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link position-relative" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-bell fs-5"></i>
                            @php $notifCount = auth()->user()->unreadNotifications()->count(); @endphp
                            @if($notifCount > 0)
                                <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle" style="font-size:.6rem">{{ $notifCount }}</span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" style="width:320px; max-height:400px; overflow-y:auto">
                            <li class="dropdown-header d-flex justify-content-between align-items-center">
                                Notifications
                                <form method="POST" action="{{ route('notifications.readAll') }}">
                                    @csrf
                                    <button class="btn btn-link btn-sm p-0 text-muted">Mark all read</button>
                                </form>
                            </li>
                            @forelse(auth()->user()->notifications()->latest()->take(8)->get() as $n)
                                <li>
                                    <a class="dropdown-item py-2 {{ $n->read_at ? '' : 'fw-semibold bg-light' }}"
                                       href="{{ $n->data['url'] ?? '#' }}">
                                        <div class="small">{{ $n->data['title'] ?? '' }}</div>
                                        <div class="text-muted" style="font-size:.75rem;white-space:normal">{{ $n->data['body'] ?? '' }}</div>
                                        <div class="text-muted" style="font-size:.7rem">{{ $n->created_at->diffForHumans() }}</div>
                                    </a>
                                </li>
                            @empty
                                <li><span class="dropdown-item text-muted small">No notifications</span></li>
                            @endforelse
                        </ul>
                    </li>

                    {{-- User menu --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                            <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle" width="30" height="30" style="object-fit:cover">
                            <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @if(auth()->user()->isAdmin())
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Admin Panel</a></li>
                            @elseif(auth()->user()->isSeller())
                                <li><a class="dropdown-item" href="{{ route('seller.dashboard') }}"><i class="bi bi-shop me-2"></i>Seller Dashboard</a></li>
                            @endif
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                            @if(auth()->user()->isBuyer())
                                <li><a class="dropdown-item" href="{{ route('buyer.orders.index') }}"><i class="bi bi-bag me-2"></i>My Orders</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

{{-- ── Flash messages ── --}}
<div class="container mt-2">
    @foreach(['success' => 'success', 'error' => 'danger', 'info' => 'info', 'warning' => 'warning'] as $type => $class)
        @if(session($type))
            <div class="alert alert-{{ $class }} alert-dismissible fade show" role="alert">
                {{ session($type) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
</div>

{{-- ── Main content ── --}}
@yield('content')

{{-- ── Footer ── --}}
<footer class="mt-5 py-4">
    <div class="container text-center">
        <p class="mb-1 fw-semibold text-white">LASU Marketplace</p>
        <p class="small mb-0">A verified campus e-commerce platform for Lagos State University students &amp; entrepreneurs.</p>
        <p class="small mt-1" style="color:rgba(255,255,255,.5)">&copy; {{ date('Y') }} LASU Marketplace. Built for the LASU community.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
