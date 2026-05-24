@extends('layouts.app')
@section('title', 'Add New Listing')
@section('content')
<div class="container py-4" style="max-width:720px">
    <h4 class="fw-bold mb-4">Add New Listing</h4>

    <div class="card p-4">
        <form method="POST" action="{{ route('seller.listings.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                       value="{{ old('title') }}" placeholder="e.g. Engineering Mathematics Textbook" required>
                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror"
                          placeholder="Describe your item, its condition, what's included..." required>{{ old('description') }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Price (₦) <span class="text-danger">*</span></label>
                    <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                           value="{{ old('price') }}" min="0" step="0.01" required>
                    @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                        <option value="">Select...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Condition <span class="text-danger">*</span></label>
                    <select name="item_condition" class="form-select" required>
                        <option value="new"         {{ old('item_condition') === 'new'         ? 'selected' : '' }}>New</option>
                        <option value="fairly_used" {{ old('item_condition') === 'fairly_used' ? 'selected' : '' }}>Fairly Used</option>
                        <option value="used"        {{ old('item_condition') === 'used'        ? 'selected' : '' }}>Used</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Stock Quantity</label>
                    <input type="number" name="stock_quantity" class="form-control"
                           value="{{ old('stock_quantity', 1) }}" min="1" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Campus Pickup Zone</label>
                    <select name="campus_zone_id" class="form-select">
                        <option value="">Any zone</option>
                        @foreach($zones as $zone)
                            <option value="{{ $zone->id }}" {{ old('campus_zone_id') == $zone->id ? 'selected' : '' }}>
                                {{ $zone->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_negotiable" value="1"
                               id="negotiable" {{ old('is_negotiable') ? 'checked' : '' }}>
                        <label class="form-check-label" for="negotiable">Negotiable</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_preorder" value="1"
                               id="preorder" {{ old('is_preorder') ? 'checked' : '' }}>
                        <label class="form-check-label" for="preorder">Pre-order</label>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Photos <span class="text-danger">*</span></label>
                <input type="file" name="images[]" class="form-control @error('images') is-invalid @enderror"
                       multiple accept="image/*" required>
                <div class="form-text">Upload up to 5 photos. First image will be the cover photo.</div>
                @error('images') <div class="invalid-feedback">{{ $message }}</div> @enderror
                @error('images.*') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-lasu px-4">Publish Listing</button>
                <a href="{{ route('seller.listings.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
