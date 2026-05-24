@extends('layouts.app')
@section('title', 'Verify Email')
@section('content')
<div class="container py-5" style="max-width:500px">
    <div class="card p-4 text-center shadow-sm">
        <i class="bi bi-envelope-check fs-1 mb-3" style="color:var(--lasu-green)"></i>
        <h4 class="fw-bold">Verify Your Email</h4>
        <p class="text-muted">We sent a verification link to <strong>{{ auth()->user()->email }}</strong>. Please check your inbox and click the link to activate your account.</p>
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button class="btn btn-lasu w-100 mb-2">Resend Verification Email</button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-outline-secondary w-100">Logout</button>
        </form>
    </div>
</div>
@endsection
