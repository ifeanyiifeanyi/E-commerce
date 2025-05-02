@extends('layouts.guest')

@section('title', 'Login')
@section('content')
<div class="auth-container">
    <h2 class="auth-title">Sign In</h2>

    @if(session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="auth-form">
        @csrf

        <div class="mb-3">
            <input type="email" class="form-control @error('email') is-invalid @enderror"
                name="email" value="{{ old('email') }}" placeholder="Email"
                autocomplete="email" autofocus>

            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="mb-3 position-relative">
            <input type="password" class="form-control @error('password') is-invalid @enderror"
                name="password" placeholder="Password" autocomplete="current-password">
            <i class="fa fa-eye password-toggle"></i>

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="remember" name="remember">
            <label class="form-check-label" for="remember">Remember me</label>
        </div>

        <div class="mb-3 d-grid">
            <button type="submit" class="btn btn-primary">Sign in</button>
        </div>

        @if (Route::has('password.request'))
            <div class="text-center mb-3">
                <a href="{{ route('password.request') }}">Forgot Password</a>
            </div>
        @endif

        <div class="text-center">
            <p>Don't have an account? <a href="{{ route('register') }}">Register</a></p>
        </div>
    </form>
</div>
@endsection
