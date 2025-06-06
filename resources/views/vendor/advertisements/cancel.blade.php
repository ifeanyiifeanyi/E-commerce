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
                                <p><strong>Package:</strong> {{ $advertisement->package->name }}</p>
                                <p><strong>Amount Paid:</strong> ₦{{ number_format($advertisement->amount_paid, 2) }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Status:</strong>
                                    <span class="badge badge-{{ $advertisement->status === 'active' ? 'success' : 'warning' }}">
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
                            <div class="form-group">
                                <label for="reason">Reason for Cancellation *</label>
                                <textarea class="form-control" id="reason" name="reason" rows="4" required
                                    placeholder="Please provide a reason for cancelling this advertisement..."></textarea>
                            </div>

                            <div class="form-group">
                                <label for="email_confirmation">Confirm Your Email Address *</label>
                                <input type="email" class="form-control" id="email_confirmation"
                                    placeholder="Enter your email address to confirm cancellation" required>
                                <small class="form-text text-muted">
                                    Your registered email: {{ auth()->user()->email }}
                                </small>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="confirm_cancellation" required>
                                    <label class="custom-control-label" for="confirm_cancellation">
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

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Confirm Cancellation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Warning:</strong> This action cannot be undone.
                </div>
                <p>Are you sure you want to cancel this advertisement?</p>
                <p><strong>Reason:</strong> <span id="modalReason"></span></p>
                @if($canCancel && $refundAmount > 0)
                    <p><strong>Refund Amount:</strong> ₦{{ number_format($refundAmount, 2) }}</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmCancelBtn">
                    <i class="fas fa-times"></i> Confirm Cancellation
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cancelBtn = document.getElementById('cancelBtn');
    const confirmCancelBtn = document.getElementById('confirmCancelBtn');
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
                alert('Please provide a reason for cancellation.');
                reasonTextarea.focus();
                return;
            }

            if (!emailValue) {
                alert('Please enter your email address.');
                emailConfirmation.focus();
                return;
            }

            if (emailValue !== userEmail) {
                alert('Email address does not match your registered email.');
                emailConfirmation.focus();
                return;
            }

            if (!confirmCheckbox.checked) {
                alert('Please confirm that you understand this action cannot be undone.');
                confirmCheckbox.focus();
                return;
            }

            // Show confirmation modal
            document.getElementById('modalReason').textContent = reason;
            $('#confirmationModal').modal('show');
        });
    }

    if (confirmCancelBtn) {
        confirmCancelBtn.addEventListener('click', function() {
            // Show loading state
            confirmCancelBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            confirmCancelBtn.disabled = true;

            // Submit form
            cancellationForm.submit();
        });
    }
});
</script>
@endsection
