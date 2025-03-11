@extends('layouts.vendor')

@section('title', 'Register As Vendor')

@section('vendor-content')
    <div class="form-holder">
        <div class="form-content">
            <div class="form-items">
                <h3>Signup as a vendor</h3>
                <div class="page-links">
                    <a class="{{ request()->routeIs('vendor.login.view') ? 'active' : '' }}" href="{{ route('vendor.login.view') }}">Login</a>
                    <a class="{{ request()->routeIs('vendor.register.view') ? 'active' : '' }}" href="{{ route('vendor.register.view') }}">Register</a>
                </div>


                <form method="POST" action="{{ route('vendor.register') }}">
                    @csrf

                    <!-- Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                            name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    <!-- Email Address -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" required autocomplete="email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                            name="password" required autocomplete="new-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation"
                            required autocomplete="new-password">
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="mb-3 form-check">
                        <input id="terms" type="checkbox" class="form-check-input @error('terms') is-invalid @enderror"
                            name="terms" {{ old('terms') ? 'checked' : '' }}>
                        <label class="form-check-label" for="terms">
                            I agree to the Terms of Service and Privacy Policy
                        </label>
                        @error('terms')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="gap-2 d-grid">
                        <button type="submit" class="btn btn-primary">Register as Vendor</button>
                    </div>


                </form>
            </div>
        </div>
    </div>

@endsection
