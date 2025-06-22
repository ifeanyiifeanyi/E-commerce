@extends('admin.layouts.admin')

@section('title', 'Advertisement Details')

@section('breadcrumb-parent', 'Advertisements')
@section('breadcrumb-parent-route', route('admin.vendor.advertisements'))

@section('admin-content')
<div class="container-fluid">
    <!-- Header with Action Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Advertisement Details</h1>
            <p class="text-muted">ID: #{{ $advertisement->id }}</p>
        </div>
        <div class="btn-group" role="group">
            <a href="{{ route('admin.vendor.advertisements') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>

            @if($advertisement->status === 'pending' && $advertisement->payment_status === 'completed')
                <button class="btn btn-success" onclick="showApprovalModal({{ $advertisement->id }})">
                    <i class="fas fa-check"></i> Approve
                </button>
                <button class="btn btn-danger" onclick="showRejectionModal({{ $advertisement->id }})">
                    <i class="fas fa-times"></i> Reject
                </button>
            @endif

            @if($advertisement->status === 'active')
                <button class="btn btn-warning" onclick="showSuspensionModal({{ $advertisement->id }})">
                    <i class="fas fa-pause"></i> Suspend
                </button>
            @endif

            @if($advertisement->status === 'paused')
                <form method="POST" action="{{ route('admin.vendor.advertisements.reactivate', $advertisement) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('Reactivate this advertisement?')">
                        <i class="fas fa-play"></i> Reactivate
                    </button>
                </form>
            @endif

            <button class="btn btn-primary" onclick="showMessageModal({{ $advertisement->id }})">
                <i class="fas fa-envelope"></i> Send Message
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Advertisement Details -->
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Advertisement Information</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        @if($advertisement->image_url)
                        <div class="col-md-4">
                            <img src="{{ $advertisement->image_url }}" alt="Advertisement Image" class="img-fluid rounded shadow">
                        </div>
                        @endif
                        <div class="col-md-{{ $advertisement->image_url ? '8' : '12' }}">
                            <h3 class="text-primary">{{ $advertisement->title }}</h3>
                            <p class="text-muted mb-3">{{ $advertisement->description ?? 'N/A' }}</p>

                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Status:</strong>
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-warning',
                                            'active' => 'bg-success',
                                            'paused' => 'bg-secondary',
                                            'rejected' => 'bg-danger',
                                            'expired' => 'bg-dark'
                                        ];
                                        $statusClass = $statusClasses[$advertisement->status] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $statusClass }} ms-2">{{ ucfirst($advertisement->status) }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Payment Status:</strong>
                                    @php
                                        $paymentStatusClasses = [
                                            'completed' => 'bg-success',
                                            'pending' => 'bg-warning',
                                            'failed' => 'bg-danger',
                                            'refunded' => 'bg-info'
                                        ];
                                        $paymentClass = $paymentStatusClasses[$advertisement->payment_status] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $paymentClass }} ms-2">{{ ucfirst($advertisement->payment_status) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Details -->
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Campaign Details</h6>
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Package:</th>
                                    <td>{{ $advertisement->package->name }}</td>
                                </tr>
                                <tr>
                                    <th>Duration:</th>
                                    <td>{{ $advertisement->package->duration_days }} days</td>
                                </tr>
                                <tr>
                                    <th>Amount Paid:</th>
                                    <td><strong>₦{{ number_format($advertisement->amount_paid, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Target Audience:</th>
                                    <td>{{ $advertisement->target_audience ?? 'General' }}</td>
                                </tr>
                                @if($advertisement->link_url)
                                <tr>
                                    <th>Website:</th>
                                    <td><a href="{{ $advertisement->link_url }}" target="_blank" class="text-primary">{{ $advertisement->link_url }}</a></td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Timeline</h6>
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Created:</th>
                                    <td>{{ $advertisement->created_at->format('M d, Y H:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Started:</th>
                                    <td>{{ $advertisement->start_date->format('M d, Y H:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Expires:</th>
                                    <td>
                                        {{ $advertisement->expires_at->format('M d, Y H:i A') }}
                                        @if($advertisement->expires_at <= now()->addDays(7) && $advertisement->status === 'active')
                                            <span class="badge bg-danger ms-2">Expiring Soon</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($advertisement->admin_notes)
                    <div class="mt-4">
                        <h6 class="text-primary">Admin Notes</h6>
                        <div class="alert alert-info">
                            <i class="fas fa-sticky-note"></i> {{ $advertisement->admin_notes }}
                        </div>
                    </div>
                    @endif

                    @if($advertisement->rejection_reason)
                    <div class="mt-4">
                        <h6 class="text-danger">Rejection Reason</h6>
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle"></i> {{ $advertisement->rejection_reason }}
                        </div>
                    </div>
                    @endif

                    @if($advertisement->cancellation_reason)
                    <div class="mt-4">
                        <h6 class="text-warning">Suspension/Cancellation Reason</h6>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> {{ $advertisement->cancellation_reason }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Analytics -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Performance Analytics</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Impressions</h5>
                                    <p class="card-text display-4">{{ $analytics['total_impressions'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Clicks</h5>
                                    <p class="card-text display-4">{{ $analytics['total_clicks'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">CTR</h5>
                                    <p class="card-text display-4">{{ number_format($analytics['average_ctr'], 2) }}%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Vendor Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Vendor Information</h6>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $advertisement->vendor->name }}</p>
                    <p><strong>Email:</strong> <a href="mailto:{{ $advertisement->vendor->email }}">{{ $advertisement->vendor->email }}</a></p>
                    <p><strong>Phone:</strong> {{ $advertisement->vendor->phone ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- Notifications -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Notifications</h6>
                </div>
                <div class="card-body">
                    @if($notifications->isEmpty())
                        <p class="text-muted">No notifications sent to this vendor.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($notifications->take(5) as $notification)
                                <li class="list-group-item">
                                    <small class="text-muted">{{ $notification->sent_at->format('M d, Y H:i A') }}</small>
                                    <p class="mb-1">{{ $notification->message }}</p>
                                    <span class="badge {{ $notification->is_read ? 'bg-success' : 'bg-warning' }}">
                                        {{ $notification->is_read ? 'Read' : 'Unread' }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<!-- Approval Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="approvalForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="approvalModalLabel">Approve Advertisement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="admin_notes" class="form-label">Approval Notes (Optional)</label>
                        <textarea class="form-control" id="admin_notes" name="admin_notes" rows="4"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="message_to_vendor" class="form-label">Message to Vendor (Optional)</label>
                        <textarea class="form-control" id="message_to_vendor" name="message_to_vendor" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectionModal" tabindex="-1" aria-labelledby="rejectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectionForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectionModalLabel">Reject Advertisement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Rejection Reason</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="message_to_vendor" class="form-label">Message to Vendor (Optional)</label>
                        <textarea class="form-control" id="message_to_vendor" name="message_to_vendor" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Suspension Modal -->
<div class="modal fade" id="suspensionModal" tabindex="-1" aria-labelledby="suspensionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="suspensionForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="suspensionModalLabel">Suspend Advertisement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="suspension_reason" class="form-label">Suspension Reason</label>
                        <textarea class="form-control" id="suspension_reason" name="suspension_reason" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="message_to_vendor" class="form-label">Message to Vendor (Optional)</label>
                        <textarea class="form-control" id="message_to_vendor" name="message_to_vendor" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Suspend</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="messageForm" method="GET" action="{{ route('admin.vendor.advertisements.send-message', $advertisement) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel">Send Message to Vendor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="6" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for Modals -->
<script>
    function showApprovalModal(adId) {
        const form = document.getElementById('approvalForm');
        form.action = `/admin/vendor-advertisements/${adId}/approve`;
        const modal = new bootstrap.Modal(document.getElementById('approvalModal'));
        modal.show();
    }

    function showRejectionModal(adId) {
        const form = document.getElementById('rejectionForm');
        form.action = `/admin/vendor-advertisements/${adId}/reject`;
        const modal = new bootstrap.Modal(document.getElementById('rejectionModal'));
        modal.show();
    }
   

    function showSuspensionModal(adId) {
        const form = document.getElementById('suspensionForm');
        form.action = `/admin/advertisements/${adId}/suspend`;
        const modal = new bootstrap.Modal(document.getElementById('suspensionModal'));
        modal.show();
    }

    function showMessageModal(adId) {
        const modal = new bootstrap.Modal(document.getElementById('messageModal'));
        modal.show();
    }
</script>
@endsection
