{{-- ============================================================
     resources/views/auth/login.blade.php
     ============================================================ --}}
@extends('layouts.app')
@section('title', 'Login')
@section('content')
<div class="container py-5" style="max-width:440px">
    <div class="card p-4 shadow-sm">
        <h3 class="fw-bold text-center mb-1" style="color:var(--lasu-green)">Welcome Back</h3>
        <p class="text-center text-muted small mb-4">Sign in to LASU Marketplace</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Email Address</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" required autofocus>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3 form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label small" for="remember">Remember me</label>
            </div>
            <button class="btn btn-lasu w-100 py-2 fw-semibold">Sign In</button>
        </form>

        <p class="text-center mt-3 mb-0 small">
            Don't have an account? <a href="{{ route('register') }}" style="color:var(--lasu-green)">Register here</a>
        </p>
    </div>
</div>
@endsection
