@extends('layouts.guest')
@section('title', 'Shop Details')
@section('content')
    <div class="auth-container">
        <div class="progress-indicator">
            <div class="step completed">1</div>
            <div class="step-connector active"></div>
            <div class="step completed">2</div>
            <div class="step-connector active"></div>
            <div class="step completed">3</div>
            <div class="step-connector active"></div>
            <div class="step active">4</div>
        </div>

        <div class="text-end mb-3">
            <a href="{{ route('vendor.register.step3') }}" class="back-link">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>

        <h2 class="auth-title">Shop Details</h2>
        <p class="auth-subtitle">Finish your shop set up by completing these details</p>

        <form method="POST" action="{{ route('vendor.register.complete') }}" class="auth-form">
            @csrf

            <div class="row mb-4">
                <div class="col-6">
                    <div
                        class="selector-option d-flex align-items-center {{ old('account_type') == 'business' ? 'selected' : '' }}">
                        <div class="me-3">
                            <i class="fa fa-briefcase fa-lg text-success"></i>
                        </div>
                        <div class="flex-grow-1">
                            Business
                        </div>
                        <div>
                            <input type="radio" name="account_type" value="business" class="form-check-input"
                                {{ old('account_type') == 'business' ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div
                        class="selector-option d-flex align-items-center {{ old('account_type', 'individual') == 'individual' ? 'selected' : '' }}">
                        <div class="me-3">
                            <i class="fa fa-user fa-lg text-success"></i>
                        </div>
                        <div class="flex-grow-1">
                            Individual
                        </div>
                        <div>
                            <input type="radio" name="account_type" value="individual" class="form-check-input"
                                {{ old('account_type', 'individual') == 'individual' ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
                @error('account_type')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                    placeholder="Your name. Eg John Doe Rivers" value="{{ old('name') }}" required>
                @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="mb-4">
                <input type="text" class="form-control @error('shop_name') is-invalid @enderror" name="shop_name"
                    placeholder="Shop name" value="{{ old('shop_name') }}" required>
                @error('shop_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>


            <div class="mb-4">
                <input type="text" class="form-control @error('location') is-invalid @enderror" name="location"
                    placeholder="Location" value="{{ old('location') }}" required>
                @error('location')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="mb-4 form-check">
                <input type="checkbox" class="form-check-input @error('terms') is-invalid @enderror" id="terms"
                    name="terms" {{ old('terms') ? 'checked' : '' }} required>
                <label class="form-check-label" for="terms">
                    I have read and accepted the <a href="#" class="text-danger">terms and conditions</a>
                </label>
                @error('terms')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">FINISH</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Make the radio option selector work with the visual elements
            const selectorOptions = document.querySelectorAll('.selector-option');

            selectorOptions.forEach(option => {
                option.addEventListener('click', function() {
                    // Find the radio input within this option
                    const radio = this.querySelector('input[type="radio"]');
                    if (radio) {
                        radio.checked = true;

                        // Update visual selection
                        selectorOptions.forEach(opt => {
                            opt.classList.remove('selected');
                        });
                        this.classList.add('selected');
                    }
                });
            });
        });
    </script>
@endsection
