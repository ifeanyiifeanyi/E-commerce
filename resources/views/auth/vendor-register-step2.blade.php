@extends('layouts.guest')
@section('title', 'Set Up Your Account')
@section('content')
    <div class="auth-container">
        <div class="progress-indicator">
            <div class="step completed">1</div>
            <div class="step-connector active"></div>
            <div class="step active">2</div>
            <div class="step-connector"></div>
            <div class="step incomplete">3</div>
            <div class="step-connector"></div>
            <div class="step incomplete">4</div>
        </div>

        <div class="text-end mb-3">
            <a href="{{ route('vendor.register.step1') }}" class="back-link">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>

        <h2 class="auth-title">Set up your account</h2>
        <p class="auth-subtitle">Please provide your email address to create your vendor account</p>

        <form method="POST" action="{{ route('vendor.register.step2.store') }}" class="auth-form">
            @csrf

            <div class="mb-4">
                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                    placeholder="Email address" value="{{ old('email', session('vendor_registration.email', '')) }}"
                    required>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            @if (session('verification_sent'))
                <div class="alert alert-success mb-4">
                    Verification code sent! Please check your email.
                </div>
            @endif

            <div class="mb-4">
                <input type="text" class="form-control @error('verification_code') is-invalid @enderror"
                    name="verification_code" placeholder="Enter verification code" required>
                @error('verification_code')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary">VERIFY</button>
            </div>
        </form>

        <form method="POST" action="{{ route('vendor.register.send-code') }}" class="mt-3">
            @csrf
            <input type="hidden" name="email" id="resend-email"
                value="{{ old('email', session('vendor_registration.email', '')) }}">
            <div class="d-grid">
                <button type="submit" class="btn btn-outline-secondary">RESEND CODE</button>
            </div>
        </form>
    </div>
    <!-- Include SweetAlert from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Wait for the document to be ready
        document.addEventListener('DOMContentLoaded', function() {
            // Get the email input element
            const emailInput = document.querySelector('input[name="email"]');
            const resendEmailInput = document.getElementById('resend-email');

            // Flag to track if verification has been sent during this session
            let verificationSent = {{ session('verification_sent') ? 'true' : 'false' }};

            if (emailInput) {
                // Add event listener for when the input loses focus (user finished typing)
                emailInput.addEventListener('blur', function() {
                    const email = this.value.trim();

                    // Update the hidden email field in resend form
                    if (resendEmailInput) {
                        resendEmailInput.value = email;
                    }

                    // Only send verification code automatically if no verification has been sent yet
                    if (!verificationSent && email && isValidEmail(email)) {
                        sendVerificationCode(email);
                    }
                });

                // Update the hidden field anytime email changes
                emailInput.addEventListener('input', function() {
                    if (resendEmailInput) {
                        resendEmailInput.value = this.value.trim();
                    }
                });
            }

            // Function to validate email format
            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            // Function to send verification code
            function sendVerificationCode(email) {
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
                formData.append('email', email);
                formData.append('_token', document.querySelector('input[name="_token"]').value);

                // Send AJAX request
                fetch('{{ route('vendor.register.send-code') }}', {
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
                                text: 'Please check your email for the verification code.',
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
