@extends('admin.layouts.admin')

@section('title', 'Vendor Management')

@section('breadcrumb-parent', 'Vendor Management')
@section('breadcrumb-parent-route', route('admin.dashboard'))

@section('admin-content')
    <div class="row">

                <div class="card-header">
                    <h5 class="mb-0 card-title">Manage Vendors</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>sn</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Registered</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($vendors as $vendor)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <a href="{{ route('admin.vendors.show', $vendor) }}" title="View Vendor Details" class="link fw-semibold">{{ $vendor->name }} <small><i class="fas fa-id-card"></i></small></a>
                                        </td>
                                        <td>{{ $vendor->email }}</td>
                                        <td>{{ $vendor->phone }}</td>
                                        <td>
                                            @if ($vendor->status === 'active')
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $vendor->created_at->format('M d, Y') }}</td>
                                        <td>
                                            @if ($vendor->status === 'inactive')
                                                <form method="POST" action="{{ route('admin.vendors.approve', $vendor) }}"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                                </form>
                                            @else
                                                <form method="POST"
                                                    action="{{ route('admin.vendors.deactivate', $vendor) }}"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-danger">Deactivate</button>
                                                </form>
                                            @endif

                                            <form action="{{ route('admin.vendors.delete', $vendor) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button onclick="confirm('Are you sure you want to delete this account ?')" type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            <a href="{{ route('admin.vendors.edit', $vendor) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No vendors found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $vendors->links() }}
                    </div>
                </div>



    </div>
@endsection




@section('css')

@endsection

@section('js')

@endsection
