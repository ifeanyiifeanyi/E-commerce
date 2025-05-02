@extends('layouts.guest')
@section('title', 'Sell on ' . config('app.name'))
@section('meta')
    <meta name="description" content="Register as a vendor on {{ config('app.name') }} and start selling your products.">
    <meta name="keywords" content="vendor registration, sell on {{ config('app.name') }}, register as a vendor">
@endsection

@section('content')
    <div class="auth-container">
        <div class="progress-indicator">
            <div class="step active">1</div>
            <div class="step-connector active"></div>
            <div class="step incomplete">2</div>
            <div class="step-connector"></div>
            <div class="step incomplete">3</div>
            <div class="step-connector"></div>
            <div class="step incomplete">4</div>
        </div>

        <h2 class="auth-title">Sell on {{ config('app.name') }}</h2>
        <p class="auth-subtitle">Choose your country</p>

        <form method="POST" action="{{ route('vendor.register.step1.store') }}" class="auth-form">
            @csrf

            <div class="mb-4">
                <select class="form-select form-control @error('country') is-invalid @enderror" name="country">
                    <option selected disabled>Select your country</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->name }}" {{ old('country') == $country->name ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
                @error('country')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <p class="text-muted small mb-4">Only for sellers registered and selling in their country</p>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">NEXT</button>
            </div>
        </form>
        <div class="text-center mt-3">
            <p>Already have an account? <a href="{{ route('vendor.login.view') }}">Sign In</a></p>
        </div>
    </div>
@endsection
