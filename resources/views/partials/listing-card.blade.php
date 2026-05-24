<div class="card listing-card h-100">
    <a href="{{ route('listings.show', $listing->id) }}" class="text-decoration-none">
        <img src="{{ $listing->primary_image_url }}"
             class="card-img-top"
             style="height:180px; object-fit:cover; border-radius:12px 12px 0 0"
             alt="{{ $listing->title }}"
             loading="lazy">
    </a>
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-start mb-1">
            <span class="badge" style="background:var(--lasu-light); color:var(--lasu-green); font-size:.7rem">
                {{ $listing->category->name ?? '' }}
            </span>
            <span class="badge {{ $listing->item_condition === 'new' ? 'bg-success' : 'bg-secondary' }} bg-opacity-75" style="font-size:.65rem">
                {{ ucfirst(str_replace('_', ' ', $listing->item_condition)) }}
            </span>
        </div>
        <a href="{{ route('listings.show', $listing->id) }}" class="text-decoration-none text-dark">
            <h6 class="card-title mb-1 fw-semibold" style="line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">
                {{ $listing->title }}
            </h6>
        </a>
        <div class="price-tag">{{ $listing->formatted_price }}</div>
        @if($listing->is_negotiable)
            <span class="text-muted" style="font-size:.72rem"><i class="bi bi-tag me-1"></i>Negotiable</span>
        @endif
        <div class="mt-2 d-flex align-items-center gap-1">
            <img src="{{ $listing->store->logo_url ?? '' }}"
                 class="rounded-circle" width="18" height="18" style="object-fit:cover"
                 onerror="this.style.display='none'">
            <span class="text-muted" style="font-size:.72rem">{{ $listing->store->name ?? '' }}</span>
        </div>
    </div>
    <div class="card-footer bg-transparent p-3 pt-0">
        @auth
            <form method="POST" action="{{ route('cart.add', $listing->id) }}">
                @csrf
                <button class="btn btn-lasu btn-sm w-100">
                    <i class="bi bi-cart-plus me-1"></i> Add to Cart
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm w-100">Login to Buy</a>
        @endauth
    </div>
</div>
