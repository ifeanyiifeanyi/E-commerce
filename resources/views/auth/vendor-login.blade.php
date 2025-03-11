@extends('layouts.vendor')

@section('title', 'Login')


@section('vendor-content')
    <div class="form-holder">
        <div class="form-content">
            <div class="form-items">
                <h3>Signup as a vendor</h3>
                <p>Access to the most powerful tool in the entire design and web industry.</p>
                <div class="page-links">
                    <a class="{{ request()->routeIs('vendor.login.view') ? 'active' : '' }}" href="{{ route('vendor.login.view') }}">Login</a>
                    <a class="{{ request()->routeIs('vendor.register.view') ? 'active' : '' }}" href="{{ route('vendor.register.view') }}">Register</a>
                </div>



                <form method="POST" action="{{ route('vendor.register') }}">
                    @csrf


                    <!-- Username -->
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input id="username" type="text" class="form-control @error('username') is-invalid @enderror"
                            name="username" value="{{ old('username') }}" required autocomplete="username">
                        @error('username')
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


                    <div class="gap-2 d-grid">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>

                    <div class="mt-3 text-center">
                        <p>Get started as a vendor ? <a href="{{ route('vendor.register.view') }}"
                                class="fw-medium text-decoration-none">Sign Up</a></p>
                    </div>
                </form>
                <div class="other-links">
                    <span>Or register with</span><a href="#">Facebook</a><a href="#">Google</a><a
                        href="#">Linkedin</a>
                </div>
            </div>
        </div>
    </div>
@endsection
