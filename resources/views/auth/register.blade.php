@extends('layouts.app')
@section('title', 'Register')
@section('content')
<div class="container py-5" style="max-width:560px">
    <div class="card p-4 shadow-sm">
        <h3 class="fw-bold text-center mb-1" style="color:var(--lasu-green)">Create Account</h3>
        <p class="text-center text-muted small mb-4">Join the LASU Marketplace community</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Full Name</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Email Address</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" required>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    LASU Institutional Email <span class="text-muted fw-normal">(optional)</span>
                </label>
                <input type="email" name="edu_email" class="form-control @error('edu_email') is-invalid @enderror"
                       value="{{ old('edu_email') }}" placeholder="yourname@lasu.edu.ng">
                <div class="form-text">Providing your @lasu.edu.ng email gives you verified status.</div>
                @error('edu_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">I want to</label>
                <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                    <option value="">Select role...</option>
                    <option value="buyer"  {{ old('role') === 'buyer'  ? 'selected' : '' }}>Buy items</option>
                    <option value="seller" {{ old('role') === 'seller' ? 'selected' : '' }}>Sell items (open a store)</option>
                </select>
                @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row g-2 mb-3">
                <div class="col">
                    <label class="form-label fw-semibold">Faculty <span class="text-muted fw-normal">(optional)</span></label>
                    <input type="text" name="faculty" class="form-control" value="{{ old('faculty') }}" placeholder="e.g. Faculty of Science">
                </div>
                <div class="col">
                    <label class="form-label fw-semibold">Department <span class="text-muted fw-normal">(optional)</span></label>
                    <input type="text" name="department" class="form-control" value="{{ old('department') }}" placeholder="e.g. Computer Science">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Phone <span class="text-muted fw-normal">(optional)</span></label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="080xxxxxxxx">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <button class="btn btn-lasu w-100 py-2 fw-semibold">Create Account</button>
        </form>

        <p class="text-center mt-3 mb-0 small">
            Already have an account? <a href="{{ route('login') }}" style="color:var(--lasu-green)">Sign in</a>
        </p>
    </div>
</div>
@endsection
