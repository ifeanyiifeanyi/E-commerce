@extends('vendor.layouts.vendor')

@section('title', 'Cancel Advertisement')

@section('vendor')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4>Cancel Advertisement</h4>
                </div>
                <div class="card-body">
                    <!-- Advertisement Details -->
                    <div class="mb-4">
                        <h5>Advertisement Details</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Title:</strong> {{ $advertisement->title }}</p>
                                <p><strong>Package:</strong> {{ $advertisement->package?->name }}</p>
                                <p><strong>Amount Paid:</strong> ₦{{ number_format($advertisement->amount_paid, 2) }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Status:</strong>
                                    <span class="badge bg-{{ $advertisement->status === 'active' ? 'success' : 'warning' }}">
                                        {{ ucfirst($advertisement->status) }}
                                    </span>
                                </p>
                                <p><strong>Start Date:</strong> {{ $advertisement->start_date->format('M d, Y') }}</p>
                                <p><strong>End Date:</strong> {{ $advertisement->end_date->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Refund Information -->
                    @if($canCancel)
                        <div class="alert alert-info mb-4">
                            <h6>Refund Information</h6>
                            @if($refundAmount > 0)
                                <p>You will receive a refund of <strong>₦{{ number_format($refundAmount, 2) }}</strong> for the remaining days.</p>
                                <p class="small">Refund will be processed within 5-7 business days.</p>
                            @else
                                <p>No refund will be processed for this cancellation.</p>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-danger mb-4">
                            <h6>Cancellation Not Allowed</h6>
                            <p>This advertisement has been active for more than 24 hours and cannot be cancelled through self-service.</p>
                            <p>If you need urgent assistance, please contact the admin.</p>
                            <p><strong>Note:</strong> No refunds will be processed for advertisements that have been active for more than 24 hours.</p>
                        </div>
                    @endif

                    @if($canCancel)
                        <!-- Cancellation Form -->
                        <form id="cancellationForm" method="POST" action="{{ route('vendor.advertisements.cancel', $advertisement) }}">
                            @csrf
                            <div class="form-group mb-3 mt-3">
                                <label for="reason" class="form-label">Reason for Cancellation <b class="text-danger">*</b></label>
                                <textarea class="form-control @error('reason') border-danger @enderror" id="reason" name="reason" rows="4" required
                                    placeholder="Please provide a reason for cancelling this advertisement...">{{ old('reason') }}</textarea>
                                    @error('reason')
                                       <div class="text-danger">{{ $message }}</div>
                                    @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="email_confirmation" class="form-label">Confirm Your Email Address <b class="text-danger">*</b></label>
                                <input type="email" class="form-control" id="email_confirmation"
                                    placeholder="Enter your email address to confirm cancellation" required>
                                <small class="form-text text-primary">
                                    Use your registered email
                                </small>
                            </div>

                            <div class="form-group mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="confirm_cancellation" required>
                                    <label class="form-check-label" for="confirm_cancellation">
                                        I understand that this action cannot be undone and confirm that I want to cancel this advertisement.
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="button" class="btn btn-danger" id="cancelBtn">
                                    <i class="fas fa-times"></i> Cancel Advertisement
                                </button>
                                <a href="{{ route('vendor.advertisement') }}" class="btn btn-secondary ml-2">
                                    <i class="fas fa-arrow-left"></i> Go Back
                                </a>
                            </div>
                        </form>
                    @else
                        <div class="text-center">
                            <a href="{{ route('vendor.advertisement') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Go Back
                            </a>
                            <a href="mailto:admin@example.com" class="btn btn-primary ml-2">
                                <i class="fas fa-envelope"></i> Contact Admin
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.10.1/sweetalert2.min.css">
<style>
    .card {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .alert {
        border-radius: 8px;
    }
    .form-control:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
</style>
@endsection

@section('js')
<!-- SweetAlert2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.10.1/sweetalert2.all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cancelBtn = document.getElementById('cancelBtn');
    const cancellationForm = document.getElementById('cancellationForm');
    const emailConfirmation = document.getElementById('email_confirmation');
    const reasonTextarea = document.getElementById('reason');
    const confirmCheckbox = document.getElementById('confirm_cancellation');
    const userEmail = '{{ auth()->user()->email }}';

    if (cancelBtn) {
        cancelBtn.addEventListener('click', function(e) {
            e.preventDefault();

            // Validate form
            const reason = reasonTextarea.value.trim();
            const emailValue = emailConfirmation.value.trim();

            if (!reason) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Information',
                    text: 'Please provide a reason for cancellation.',
                    confirmButtonColor: '#dc3545'
                });
                reasonTextarea.focus();
                return;
            }

            if (!emailValue) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Email Required',
                    text: 'Please enter your email address.',
                    confirmButtonColor: '#dc3545'
                });
                emailConfirmation.focus();
                return;
            }

            if (emailValue !== userEmail) {
                Swal.fire({
                    icon: 'error',
                    title: 'Email Mismatch',
                    text: 'Email address does not match your registered email.',
                    confirmButtonColor: '#dc3545'
                });
                emailConfirmation.focus();
                return;
            }

            if (!confirmCheckbox.checked) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Confirmation Required',
                    text: 'Please confirm that you understand this action cannot be undone.',
                    confirmButtonColor: '#dc3545'
                });
                confirmCheckbox.focus();
                return;
            }

            // Show confirmation dialog with SweetAlert
            Swal.fire({
                title: 'Confirm Cancellation',
                html: `
                    <div class="text-left">
                        <div class="alert alert-warning mb-3">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Warning:</strong> This action cannot be undone.
                        </div>
                        <p><strong>Reason:</strong> ${reason}</p>
                        @if($canCancel && $refundAmount > 0)
                            <p><strong>Refund Amount:</strong> ₦{{ number_format($refundAmount, 2) }}</p>
                        @endif
                        <p>Are you sure you want to cancel this advertisement?</p>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-times"></i> Yes, Cancel Advertisement',
                cancelButtonText: 'No, Keep Advertisement',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return new Promise((resolve) => {
                        // Submit the form
                        cancellationForm.submit();
                        resolve();
                    });
                }
            });
        });
    }

    // Show success/error messages if they exist in session
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#28a745'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session('error') }}',
            confirmButtonColor: '#dc3545'
        });
    @endif

    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Validation Error!',
            html: `
                <ul class="text-left">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            `,
            confirmButtonColor: '#dc3545'
        });
    @endif
});
</script>
@endsection
