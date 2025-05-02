@extends('layouts.guest')

@section('title', 'Vendor Login')

@section('content')
<div class="auth-container">
    <h2 class="auth-title">Vendor Login</h2>
    <p class="auth-subtitle">Access your vendor dashboard</p>

    @if(session('status'))
        <div class="alert alert-success mb-3">
            {{ session('status') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger mb-3">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('vendor.login') }}" class="auth-form">
        @csrf

        <div class="mb-3">
            <input class="form-control @error('username') is-invalid @enderror" type="text"
                name="username" placeholder="Email or Username" required value="{{ old('username') }}">
            @error('username')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="mb-3 position-relative">
            <input class="form-control @error('password') is-invalid @enderror" type="password"
                name="password" placeholder="Password" required>
            <i class="fa fa-eye password-toggle"></i>
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="mb-3 d-flex justify-content-between align-items-center">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">
                    Remember Me
                </label>
            </div>
            <a href="{{ route('password.request') }}" class="text-decoration-none">Forgot Password?</a>
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary">Login</button>
        </div>

        <div class="text-center">
            <p>Don't have a vendor account? <a href="{{ route('vendor.register.step1') }}">Register</a></p>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Toggle password visibility
    document.querySelector('.password-toggle').addEventListener('click', function() {
        const passwordInput = document.querySelector('input[name="password"]');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            this.classList.remove('fa-eye');
            this.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            this.classList.remove('fa-eye-slash');
            this.classList.add('fa-eye');
        }
    });
</script>
@endpush
@endsection
