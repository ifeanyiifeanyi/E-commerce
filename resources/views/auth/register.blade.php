@extends('layouts.guest')

@section('title', 'Register')

@section('content')

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="bg-white p-4 p-md-5 shadow-sm rounded-3">
                    <div class="row mb-4">
                        <div class="col-12">
                            <h2 class="fw-bold text-success">Join our platform</h2>
                            <p class="text-muted">Access the most powerful tools in the web industry</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-7">
                            <form method="POST" action="{{ route('register') }}">
                                @csrf
                                <div class="mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-user text-muted"></i>
                                        </span>
                                        <input class="form-control border-start-0 @error('name') is-invalid @enderror"
                                            type="text" name="name" placeholder="Full Name"
                                            value="{{ old('name') }}">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-at text-muted"></i>
                                        </span>
                                        <input class="form-control border-start-0 @error('username') is-invalid @enderror"
                                            type="text" name="username" placeholder="Username"
                                            value="{{ old('username') }}">
                                        @error('username')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-phone text-muted"></i>
                                        </span>
                                        <input class="form-control border-start-0 @error('phone') is-invalid @enderror"
                                            type="text" name="phone" placeholder="Phone Number"
                                            value="{{ old('phone') }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-envelope text-muted"></i>
                                        </span>
                                        <input class="form-control border-start-0 @error('email') is-invalid @enderror"
                                            type="email" name="email" placeholder="Email Address"
                                            value="{{ old('email') }}">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-lock text-muted"></i>
                                        </span>
                                        <input class="form-control border-start-0 @error('password') is-invalid @enderror"
                                            type="password" name="password" placeholder="Password">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-check-circle text-muted"></i>
                                        </span>
                                        <input class="form-control border-start-0" type="password"
                                            name="password_confirmation" placeholder="Confirm Password">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="terms" name="terms">
                                        <label class="form-check-label small" for="terms">
                                            I agree to the <a href="#" class="text-decoration-none">Terms</a>
                                        </label>
                                    </div>
                                    <button type="submit" class="btn btn-primary px-4">
                                        Sign Up <i class="fas fa-arrow-right ms-1"></i>
                                    </button>
                                </div>
                            </form>

                            <div class="d-flex align-items-center mb-3">
                                <hr class="flex-grow-1">
                                <span class="mx-3 text-muted small">OR SIGN UP WITH</span>
                                <hr class="flex-grow-1">
                            </div>

                            <div class="d-flex justify-content-center gap-3 mb-3">
                                <a href="#" class="btn btn-outline-secondary">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="btn btn-outline-secondary">
                                    <i class="fab fa-google"></i>
                                </a>
                                <a href="#" class="btn btn-outline-secondary">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            </div>
                        </div>

                        <div class="col-md-5 d-flex flex-column justify-content-center border-start">
                            <div class="text-center p-4">
                                <div class="mb-4">
                                    <span class="d-inline-block p-3 bg-light rounded-circle mb-2">
                                        <i class="fas fa-user-plus fa-2x text-primary"></i>
                                    </span>
                                    <h4 class="mt-2">Already have an account?</h4>
                                    <p class="text-muted mb-4">Sign in to access your dashboard</p>
                                </div>
                                <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg px-4">
                                    Log In
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex justify-content-end gap-3 small">
                            <a href="#" class="text-muted text-decoration-none">Terms</a>
                            <a href="#" class="text-muted text-decoration-none">Privacy</a>
                            <a href="#" class="text-muted text-decoration-none">Support</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
