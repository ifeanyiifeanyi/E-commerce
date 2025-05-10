@extends('admin.layouts.admin')

@section('title', 'Store Details - ' . $store->store_name)

@section('breadcrumb-parent', 'Vendor Stores')
@section('breadcrumb-parent-route', route('admin.vendor.stores'))
@section('breadcrumb-current', 'Store Details')

@section('admin-content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Store Details</h4>
                    <div>
                        <a href="{{ route('admin.vendor.stores.documents', $store) }}" class="btn btn-info">
                            <i class="fas fa-file-alt"></i> View Documents
                        </a>
                        <form action="{{ route('admin.vendor.stores.toggle-featured', $store) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn {{ $store->is_featured ? 'btn-warning' : 'btn-secondary' }}">
                                <i class="fas {{ $store->is_featured ? 'fa-star' : 'fa-star' }}"></i>
                                {{ $store->is_featured ? 'Remove Featured' : 'Make Featured' }}
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <h5>Store Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 30%">Store Name</th>
                                    <td>{{ $store->store_name }}</td>
                                </tr>
                                <tr>
                                    <th>Store URL</th>
                                    <td>
                                        @if($store->store_slug)
                                            <a href="{{ url('/store/' . $store->store_slug) }}" target="_blank">
                                                {{ url('/store/' . $store->store_slug) }}
                                            </a>
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if($store->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($store->status == 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($store->status == 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $store->status }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Featured</th>
                                    <td>{{ $store->is_featured ? 'Yes' : 'No' }}</td>
                                </tr>
                                <tr>
                                    <th>Join Date</th>
                                    <td>{{ $store->join_date ? $store->join_date->format('M d, Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Commission Rate</th>
                                    <td>{{ $store->commission_rate ?? 'Default' }}%</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h5>Contact Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 30%">Email</th>
                                    <td>{{ $store->store_email ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>{{ $store->store_phone ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td>{{ $store->store_address ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>City</th>
                                    <td>{{ $store->store_city ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>State</th>
                                    <td>{{ $store->store_state ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Country</th>
                                    <td>{{ $store->store_country ?: 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5>Store Media</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">Store Logo</div>
                                        <div class="card-body text-center">
                                            @if($store->store_logo)
                                                <img src="{{ asset('storage/' . $store->store_logo) }}"
                                                    alt="Store Logo" class="img-fluid" style="max-height: 150px;">
                                            @else
                                                <p class="text-muted">No logo uploaded</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">Store Banner</div>
                                        <div class="card-body text-center">
                                            @if($store->store_banner)
                                                <img src="{{ asset('storage/' . $store->store_banner) }}"
                                                    alt="Store Banner" class="img-fluid" style="max-height: 150px;">
                                            @else
                                                <p class="text-muted">No banner uploaded</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5>Social Media</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 30%">Facebook</th>
                                    <td>
                                        @if($store->social_facebook)
                                            <a href="{{ $store->social_facebook }}" target="_blank">
                                                {{ $store->social_facebook }}
                                            </a>
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Twitter</th>
                                    <td>
                                        @if($store->social_twitter)
                                            <a href="{{ $store->social_twitter }}" target="_blank">
                                                {{ $store->social_twitter }}
                                            </a>
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Instagram</th>
                                    <td>
                                        @if($store->social_instagram)
                                            <a href="{{ $store->social_instagram }}" target="_blank">
                                                {{ $store->social_instagram }}
                                            </a>
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>YouTube</th>
                                    <td>
                                        @if($store->social_youtube)
                                            <a href="{{ $store->social_youtube }}" target="_blank">
                                                {{ $store->social_youtube }}
                                            </a>
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5>Store Description</h5>
                            <div class="card">
                                <div class="card-body">
                                    {!! $store->store_description ?: '<p class="text-muted">No description provided</p>' !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($store->status == 'pending')
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="m-0">Approve Store</h5>
                                    </div>
                                    <div class="card-body">
                                        <p>Click the button below to approve this store. This will allow the vendor to start selling on your platform.</p>
                                        <form action="{{ route('admin.vendor.stores.approve', $store) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-check-circle"></i> Approve Store
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card border-danger">
                                    <div class="card-header bg-danger text-white">
                                        <h5 class="m-0">Reject Store</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('admin.vendor.stores.reject', $store) }}" method="POST">
                                            @csrf
                                            <div class="form-group">
                                                <label for="rejection_reason">Reason for Rejection</label>
                                                <textarea name="rejection_reason" id="rejection_reason" rows="3"
                                                    class="form-control @error('rejection_reason') is-invalid @enderror"
                                                    required></textarea>
                                                @error('rejection_reason')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <button type="submit" class="btn btn-danger mt-2">
                                                <i class="fas fa-times-circle"></i> Reject Store
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($store->status == 'rejected')
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card border-danger">
                                    <div class="card-header bg-danger text-white">
                                        <h5 class="m-0">Rejection Reason</h5>
                                    </div>
                                    <div class="card-body">
                                        <p>{{ $store->rejection_reason }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Logs -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Recent Activity</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Action</th>
                                    <th>User</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activityLogs as $log)
                                    <tr>
                                        <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                                        <td>{{ $log->description }}</td>
                                        <td>
                                            @if($log->causer)
                                                {{ $log->causer->name }}
                                            @else
                                                System
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->properties->count() > 0)
                                                <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#logModal{{ $log->id }}">
                                                    View Details
                                                </button>

                                                <!-- Modal -->
                                                <div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1" role="dialog" aria-labelledby="logModalLabel{{ $log->id }}">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="logModalLabel{{ $log->id }}">Activity Details</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <pre>{{ json_encode($log->properties->toArray(), JSON_PRETTY_PRINT) }}</pre>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">No additional details</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No activity logs found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    .badge {
        padding: 0.5em 0.75em;
    }
    .table th {
        background-color: #f8f9fa;
    }
</style>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Initialize modals
        $('.modal').modal({
            show: false
        });
    });
</script>
@endsection
