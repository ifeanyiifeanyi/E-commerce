@extends('admin.layouts.admin')

@section('title', 'Vendor Details')

@section('breadcrumb-parent', 'Vendor Management')
@section('breadcrumb-parent-route', route('admin.vendors'))

@section('admin-content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 card-title">Vendor Details</h5>
                    <div>
                        <a href="{{ route('admin.vendors.edit', $user) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i> Edit Vendor
                        </a>
                        <a href="{{ route('admin.vendors') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-4 text-center mb-4">
                            @if($user->photo)
                                <img src="{{ $user->photo }}" alt="{{ $user->name }}" class="img-fluid rounded-circle mb-3" style="max-width: 200px; height: auto;">
                            @else
                                <div class="bg-light rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 200px; height: 200px;">
                                    <i class="fas fa-user fa-5x text-secondary"></i>
                                </div>
                            @endif

                            <div class="mt-3">
                                <h4>{{ $user->name }}</h4>
                                <p class="text-muted">{{ '@'.$user->username }}</p>

                                @if ($user->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </div>

                            <div class="mt-3">
                                @if ($user->status === 'inactive')
                                    <form method="POST" action="{{ route('admin.vendors.approve', $user) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success">Approve Vendor</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.vendors.deactivate', $user) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-warning">Deactivate Vendor</button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Contact Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-4 fw-bold">Email:</div>
                                        <div class="col-md-8">
                                            <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                            @if($user->email_verified_at)
                                                <span class="badge bg-success ms-2">Verified</span>
                                            @else
                                                <span class="badge bg-warning ms-2">Not Verified</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4 fw-bold">Phone:</div>
                                        <div class="col-md-8">
                                            @if($user->phone)
                                                <a href="tel:{{ $user->phone }}">{{ $user->phone }}</a>
                                            @else
                                                <span class="text-muted">Not provided</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4 fw-bold">Business Address:</div>
                                        <div class="col-md-8">
                                            @if($user->address)
                                                {{ $user->address }}
                                            @else
                                                <span class="text-muted">Not provided</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Account Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-4 fw-bold">Registered On:</div>
                                        <div class="col-md-8">{{ $user->created_at?->format('M d, Y h:i A') }}</div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4 fw-bold">Last Updated:</div>
                                        <div class="col-md-8">{{ $user->updated_at?->format('M d, Y h:i A') }}</div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4 fw-bold">Role:</div>
                                        <div class="col-md-8">
                                            <span class="badge bg-info">{{ ucfirst($user->role) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- You can add more sections here like order history, products, etc. -->
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 text-end">
                            <form action="{{ route('admin.vendors.delete', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this vendor? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Delete Vendor
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
<style>
    .card-header h6 {
        margin-bottom: 0;
    }
</style>
@endsection

@section('js')
@endsection
