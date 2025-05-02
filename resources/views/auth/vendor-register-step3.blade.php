@extends('layouts.guest')
@section('title', 'Set Up Your Account')
@section('content')
    <div class="auth-container">
        <div class="progress-indicator">
            <div class="step completed">1</div>
            <div class="step-connector active"></div>
            <div class="step completed">2</div>
            <div class="step-connector active"></div>
            <div class="step active">3</div>
            <div class="step-connector"></div>
            <div class="step incomplete">4</div>
        </div>

        <div class="text-end mb-3">
            <a href="{{ route('vendor.register.step2') }}" class="back-link">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>

        <h2 class="auth-title">Set up your account</h2>
        <p class="auth-subtitle">Please provide your contact details</p>

        <form method="POST" action="{{ route('vendor.register.step3.store') }}" class="auth-form">
            @csrf

            <div class="mb-4 d-flex">
                <div class="flex-grow-1">
                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" name="phone"
                        placeholder="Phone number +234 81 000 0000 0"
                        value="{{ old('phone', session('vendor_registration.phone', '')) }}" required>
                    @error('phone')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            @if (session('phone_verification_sent'))
                <div class="alert alert-success mb-4">
                    Verification code sent! Please check your phone.
                </div>
            @endif

            <div class="mb-4">
                <input type="text" class="form-control @error('verification_code') is-invalid @enderror"
                    name="verification_code" placeholder="Enter phone verification code" required>
                @error('verification_code')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="mb-4 position-relative">
                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password"
                    placeholder="Password" required>
                <i class="fa fa-eye password-toggle"></i>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="mb-4 position-relative">
                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                    name="password_confirmation" placeholder="Confirm Password" required>
                <i class="fa fa-eye password-toggle"></i>
                @error('password_confirmation')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <p class="text-muted small mb-4">Password should contain at least 8 characters containing a capital letter, a
                lower letter, a number and a special character</p>

            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary">NEXT</button>
            </div>
        </form>

        <form method="POST" action="{{ route('vendor.register.send-phone-code') }}" class="mt-3">
            @csrf
            <input type="hidden" name="phone" id="resend-phone"
                value="{{ old('phone', session('vendor_registration.phone', '')) }}">
            <div class="d-grid">
                <button type="submit" class="btn btn-outline-secondary">SEND VERIFICATION CODE</button>
            </div>
        </form>
    </div>

    <!-- Include SweetAlert from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility
            const toggleButtons = document.querySelectorAll('.password-toggle');

            toggleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const input = this.previousElementSibling;
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    this.classList.toggle('fa-eye');
                    this.classList.toggle('fa-eye-slash');
                });
            });

            // Get the phone input element
            const phoneInput = document.querySelector('input[name="phone"]');
            const resendPhoneInput = document.getElementById('resend-phone');

            // Flag to track if verification has been sent during this session
            let verificationSent = {{ session('phone_verification_sent') ? 'true' : 'false' }};

            if (phoneInput) {
                // Add event listener for when the input loses focus (user finished typing)
                phoneInput.addEventListener('blur', function() {
                    const phone = this.value.trim();

                    // Update the hidden phone field in resend form
                    if (resendPhoneInput) {
                        resendPhoneInput.value = phone;
                    }

                    // Only send verification code automatically if no verification has been sent yet
                    if (!verificationSent && phone && isValidPhone(phone)) {
                        sendPhoneVerificationCode(phone);
                    }
                });

                // Update the hidden field anytime phone changes
                phoneInput.addEventListener('input', function() {
                    if (resendPhoneInput) {
                        resendPhoneInput.value = this.value.trim();
                    }
                });
            }

            // Function to validate phone format (basic check)
            function isValidPhone(phone) {
                // Simple check: at least 10 digits
                return phone.replace(/[^0-9]/g, '').length >= 10;
            }

            // Function to send verification code
            function sendPhoneVerificationCode(phone) {
                // Show loading state
                Swal.fire({
                    title: 'Sending verification code...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Create form data
                const formData = new FormData();
                formData.append('phone', phone);
                formData.append('_token', document.querySelector('input[name="_token"]').value);

                // Send AJAX request
                fetch('{{ route('vendor.register.send-phone-code') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json().catch(() => {
                        return {
                            success: response.ok
                        };
                    }))
                    .then(data => {
                        if (data.success !== false) {
                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Verification Code Sent',
                                text: 'Please check your phone for the verification code.',
                                timer: 3000,
                                timerProgressBar: true
                            });

                            // Update flag
                            verificationSent = true;
                        } else {
                            // Show error message with details if available
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed to Send Code',
                                text: data.message || 'Please try again or use the resend button.',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error sending verification code:', error);

                        // Show error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Failed to send verification code. Please try again.',
                            confirmButtonText: 'OK'
                        });
                    });
            }
        });
    </script>
@endsection
