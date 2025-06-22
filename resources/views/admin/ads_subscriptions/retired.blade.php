@extends('admin.layouts.admin')

@section('title', 'Advertisement Management')

@section('breadcrumb-parent', 'Expired Advertisement Management')
@section('breadcrumb-parent-route', route('admin.vendor.advertisements'))

@section('admin-content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Revenue This Month
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₦{{ number_format($stats['revenue_this_month']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bullhorn fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Average Duration (Days)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['avg_duration']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Expiring Soon
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['expiring_soon']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-play fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Active Total
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_active'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if($stats['expiring_soon'] > 0)
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>{{ $stats['expiring_soon'] }}</strong> advertisement(s) are expiring within 7 days.
        <a href="{{ route('admin.vendor.advertisements', ['status' => 'active']) }}" class="alert-link">View them</a>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Main Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Advertisement Management</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow">
                    <a class="dropdown-item" href="{{ route('admin.vendor.advertisements.pending') }}">
                        <i class="fas fa-clock fa-sm fa-fw mr-2 text-gray-400"></i>
                        Pending Approvals
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkActionModal">
                        <i class="fas fa-tasks fa-sm fa-fw mr-2 text-gray-400"></i>
                        Bulk Actions
                    </a>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card-body">
            <form method="GET" action="{{ route('admin.vendor.advertisements') }}" class="mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="paused" {{ request('status') == 'paused' ? 'selected' : '' }}>Paused/Suspended</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="package_id" class="form-select">
                            <option value="">All Packages</option>
                            @foreach($packages as $package)
                                <option value="{{ $package->id }}" {{ request('package_id') == $package->id ? 'selected' : '' }}>
                                    {{ $package->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search by title or vendor..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Filter
                        </button>
                    </div>
                </div>
            </form>

            <!-- Advertisements Table -->
            <div class="table-responsive">
                <table class="table table-hover table-striped w-100" id="datatables">
                    <thead class="">
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th>Advertisement</th>
                            <th>Vendor</th>
                            <th>Package</th>
                            <th>Created</th>
                            <th>Expires</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($advertisements as $advertisement)
                        <tr>
                            <td>
                                <input type="checkbox" name="advertisement_ids[]" value="{{ $advertisement->id }}" class="form-check-input advertisement-checkbox">
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($advertisement->image_url)
                                        <img src="{{ $advertisement->image_url }}" alt="Ad Image" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                    @endif
                                    <div>
                                        <p class="mb-0">{{ Str::limit($advertisement->title, 30) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $advertisement->vendor->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $advertisement->vendor->email }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $advertisement->package->name }}</span>
                                <br>
                                <small class="text-muted">{{ $advertisement->package->duration_days }} days</small>
                            </td>


                            <td>
                                <small>{{ $advertisement->created_at->format('M d, Y') }}</small>
                                <br>
                                <small class="text-muted">{{ $advertisement->created_at->format('H:i A') }}</small>
                            </td>
                            <td>
                                @if($advertisement->expires_at)
                                    <small>{{ $advertisement->expires_at->format('M d, Y') }}</small>
                                    <br>
                                    <small class="text-muted">{{ $advertisement->expires_at->diffForHumans() }}</small>
                                @else
                                    <small class="text-muted">N/A</small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.vendor.advertisements.show', $advertisement) }}" class="btn btn-sm btn-outline-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    {{-- @if($advertisement->status === 'pending' && $advertisement->payment_status === 'completed')
                                        <button class="btn btn-sm btn-outline-success" onclick="showApprovalModal({{ $advertisement->id }})" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="showRejectionModal({{ $advertisement->id }})" title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif --}}

                                    @if($advertisement->status === 'active')
                                        <button class="btn btn-sm btn-outline-warning" onclick="showSuspensionModal({{ $advertisement->id }})" title="Suspend">
                                            <i class="fas fa-pause"></i>
                                        </button>
                                    @endif

                                    @if($advertisement->status === 'paused')
                                        <form method="POST" action="{{ route('admin.vendor.advertisements.reactivate', $advertisement) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Reactivate" onclick="return confirm('Reactivate this advertisement?')">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <button class="btn btn-sm btn-outline-primary" onclick="showMessageModal({{ $advertisement->id }})" title="Send Message">
                                        <i class="fas fa-envelope"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-bullhorn fa-3x mb-3"></i>
                                    <p>No active advertisements found.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    Showing {{ $advertisements->firstItem() ?? 0 }} to {{ $advertisements->lastItem() ?? 0 }} of {{ $advertisements->total() }} results
                </div>
                {{ $advertisements->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="approvalForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Approve Advertisement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Admin Notes (Optional)</label>
                        <textarea name="admin_notes" class="form-control" rows="3" placeholder="Add any notes for approval..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message to Vendor (Optional)</label>
                        <textarea name="message_to_vendor" class="form-control" rows="3" placeholder="Send a personalized message to the vendor..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Approve Advertisement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectionForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject Advertisement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Warning:</strong> Rejecting this advertisement will automatically process a refund if payment was completed.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Explain why this advertisement is being rejected..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message to Vendor (Optional)</label>
                        <textarea name="message_to_vendor" class="form-control" rows="3" placeholder="Send a personalized message to the vendor..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Reject & Refund
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Suspension Modal -->
<div class="modal fade" id="suspensionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="suspensionForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Suspend Advertisement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Suspension Reason <span class="text-danger">*</span></label>
                        <textarea name="suspension_reason" class="form-control" rows="3" placeholder="Explain why this advertisement is being suspended..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message to Vendor (Optional)</label>
                        <textarea name="message_to_vendor" class="form-control" rows="3" placeholder="Send a personalized message to the vendor..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-pause"></i> Suspend Advertisement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="messageForm" method="GET">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Send Message to Vendor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control" rows="4" placeholder="Type your message to the vendor..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-envelope"></i> Send Message
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Action Modal -->
<div class="modal fade" id="bulkActionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="bulkActionForm" method="POST" action="{{ route('admin.vendor.advertisements.bulk-action') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Actions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Action</label>
                        <select name="action" class="form-select" required>
                            <option value="">Choose an action...</option>
                            <option value="approve">Approve Selected</option>
                            <option value="reject">Reject Selected</option>
                            <option value="suspend">Suspend Selected</option>
                        </select>
                    </div>
                    <div class="mb-3" id="bulkReasonField" style="display: none;">
                        <label class="form-label">Reason</label>
                        <textarea name="bulk_reason" class="form-control" rows="3" placeholder="Provide reason for this action..."></textarea>
                    </div>
                    <div id="selectedCount" class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No advertisements selected.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="executeBulkAction" disabled>
                        Execute Action
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }

    .table th {
        font-weight: 600;
        font-size: 0.875rem;
    }

    .btn-group .btn {
        margin-right: 2px;
    }

    .card {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
    }
</style>
@endsection

@section('js')
<script>
    // Select All Functionality
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.advertisement-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActionButton();
    });

    // Individual checkbox change
    document.querySelectorAll('.advertisement-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActionButton);
    });

    function updateBulkActionButton() {
        const checkedBoxes = document.querySelectorAll('.advertisement-checkbox:checked');
        const selectedCount = document.getElementById('selectedCount');
        const executeBulkAction = document.getElementById('executeBulkAction');

        if (checkedBoxes.length > 0) {
            selectedCount.innerHTML = `<i class="fas fa-check-circle"></i> ${checkedBoxes.length} advertisement(s) selected.`;
            executeBulkAction.disabled = false;
        } else {
            selectedCount.innerHTML = `<i class="fas fa-info-circle"></i> No advertisements selected.`;
            executeBulkAction.disabled = true;
        }
    }

    // Bulk action form submission
    document.getElementById('bulkActionForm').addEventListener('submit', function(e) {
        const checkedBoxes = document.querySelectorAll('.advertisement-checkbox:checked');

        // Add selected IDs to form
        checkedBoxes.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'advertisement_ids[]';
            input.value = checkbox.value;
            this.appendChild(input);
        });
    });

    // Show/hide reason field based on action
    document.querySelector('select[name="action"]').addEventListener('change', function() {
        const reasonField = document.getElementById('bulkReasonField');
        if (this.value === 'reject' || this.value === 'suspend') {
            reasonField.style.display = 'block';
            reasonField.querySelector('textarea').required = true;
        } else {
            reasonField.style.display = 'none';
            reasonField.querySelector('textarea').required = false;
        }
    });

    // Modal functions
    function showApprovalModal(advertisementId) {
        const form = document.getElementById('approvalForm');
        form.action = `/admin/vendor-advertisements/${advertisementId}/approve`;
        new bootstrap.Modal(document.getElementById('approvalModal')).show();
    }

    function showRejectionModal(advertisementId) {
        const form = document.getElementById('rejectionForm');
        form.action = `/admin/vendor-advertisements/${advertisementId}/reject`;
        new bootstrap.Modal(document.getElementById('rejectionModal')).show();
    }

    function showSuspensionModal(advertisementId) {
        const form = document.getElementById('suspensionForm');
        form.action = `/admin/vendor-advertisements/${advertisementId}/suspend`;
        new bootstrap.Modal(document.getElementById('suspensionModal')).show();
    }

    function showMessageModal(advertisementId) {
        const form = document.getElementById('messageForm');
        form.action = `/admin/vendor-advertisements/${advertisementId}/send-message`;
        new bootstrap.Modal(document.getElementById('messageModal')).show();
    }

    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(alert => {
            if (alert.querySelector('.btn-close')) {
                alert.querySelector('.btn-close').click();
            }
        });
    }, 5000);
</script>
@endsection
