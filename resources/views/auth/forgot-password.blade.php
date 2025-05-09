@extends('layouts.guest')

@section('title', 'Login')
@section('content')
<div class="auth-container">
    <div class="mb-4 text-sm text-muted">
        Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>


        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
               Email Password Reset Link
            </x-primary-button>
        </div>
    </form>
</div>

@endsection
