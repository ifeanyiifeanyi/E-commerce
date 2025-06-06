{{-- resources/views/admin/customer-manager/index.blade.php --}}
@extends('admin.layouts.admin')

@section('title', 'Customer Manager Dashboard')

@section('breadcrumb-parent', 'Dashboard')
@section('breadcrumb-parent-route', route('admin.dashboard'))

@section('admin-content')
    <div class="row">
        <!-- Summary Stats Cards -->
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar avatar-lg bg-primary mb-3">
                        <i class="fas fa-users fs-3"></i>
                    </div>
                    <h3 class="fw-bold">{{ number_format($totalCustomers) }}</h3>
                    <p class="text-muted">Total Customers</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar avatar-lg bg-success mb-3">
                        <i class="fas fa-user-check fs-3"></i>
                    </div>
                    <h3 class="fw-bold">{{ number_format($activeCustomers) }}</h3>
                    <p class="text-muted">Active Customers</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar avatar-lg bg-info mb-3">
                        <i class="fas fa-user-plus fs-3"></i>
                    </div>
                    <h3 class="fw-bold">{{ number_format($newCustomersThisMonth) }}</h3>
                    <p class="text-muted">New This Month</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar avatar-lg bg-warning mb-3">
                        <i class="fas fa-globe fs-3"></i>
                    </div>
                    <h3 class="fw-bold">{{ $customersByCountry->count() }}</h3>
                    <p class="text-muted">Countries</p>
                </div>
            </div>
        </div>

        <!-- Customer Table Card -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Customer Management</h5>
                    <div class="card-header-actions">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bulkEmailModal">
                            <i class="bi bi-envelope"></i> Bulk Email
                        </button>
                        <button class="btn btn-success" id="exportCustomersBtn">
                            <i class="bi bi-download"></i> Export
                        </button>
                    </div>
                </div>
                {{-- @dd($customers) --}}
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped" id="datatables">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Registration</th>
                                    <th>Last Login</th>
                                    <th>Orders</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Segment</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($customers as $customer)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $customer?->getProfilePhotoUrlAttribute() }}" alt="{{ $customer->name }}"
                                                    class="rounded-circle me-2" width="40" height="40">
                                                {{ $customer->name }}
                                            </div>
                                        </td>
                                        <td>{{ $customer->created_at?->format('d M Y') }}</td>
                                        <td>{{ $customer->last_login_at ? $customer->last_login_at->diffForHumans() : 'N/A' }}</td>
                                        <td>{{ number_format($customer->orders_count) }}</td>
                                        <td>{{ $customer->country ?: 'Unknown' }}</td>
                                        <td>{{ ucfirst($customer->account_status) }}</td>
                                        <td>{{ ucfirst($customer->customer_segment) }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary send-email-btn" data-id="{{ $customer->id }}">
                                                <i class="fas fa-envelope"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-customer-btn" data-id="{{ $customer->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No customers found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Location Map Card -->
        <div class="col-md-8 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Customer Geographic Distribution</h5>
                </div>
                <div class="card-body">
                    <div id="customerMap" style="height: 400px;"></div>
                </div>
            </div>
        </div>

        <!-- Top Countries Card -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Top Countries</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        {{-- @dd($customersByCountry) --}}
                        @foreach ($customersByCountry as $country)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="flag-icon flag-icon-{{ strtolower($country->country) }}"></span>
                                    {{ $country->country ?: 'Unknown' }}
                                </div>
                                <span class="badge bg-primary rounded-pill">{{ $country->total }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Email Modal -->
    <div class="modal fade" id="bulkEmailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send Bulk Email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="bulkEmailForm">
                        <div class="mb-3">
                            <label class="form-label">Select Customers</label>
                            <select class="form-select" name="customer_segment" required>
                                <option value="all">All Customers</option>
                                <option value="active">Active Customers</option>
                                <option value="inactive">Inactive Customers</option>
                                <option value="new">New Customers (Last 30 Days)</option>
                                <option value="vip">VIP Customers</option>
                                <option value="at_risk">At-Risk Customers</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Type</label>
                            <select class="form-select" name="email_type" required>
                                <option value="general">General Message</option>
                                <option value="promotion">Promotion</option>
                                <option value="product_recommendation">Product Recommendations</option>
                                <option value="account">Account Update</option>
                            </select>
                        </div>
                        <div class="mb-3 product-recommendations-section" style="display: none;">
                            <label class="form-label">Select Products</label>
                            <select class="form-select" name="products[]" multiple>
                                <!-- Products will be loaded via AJAX -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input type="text" class="form-control" name="subject" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea class="form-control" name="message" rows="5" required></textarea>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="track_opens" checked>
                            <label class="form-check-label">Track opens</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="sendBulkEmailBtn">Send Emails</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Single Customer Email Modal -->
    <div class="modal fade" id="singleEmailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send Email to Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="singleEmailForm">
                        <input type="hidden" name="customer_id" id="emailCustomerId">
                        <div class="mb-3">
                            <label class="form-label">Email Type</label>
                            <select class="form-select" name="email_type" required>
                                <option value="general">General Message</option>
                                <option value="promotion">Promotion</option>
                                <option value="product_recommendation">Product Recommendations</option>
                                <option value="account">Account Update</option>
                            </select>
                        </div>
                        <div class="mb-3 single-product-recommendations-section" style="display: none;">
                            <label class="form-label">Select Products</label>
                            <select class="form-select" name="product_ids[]" multiple>
                                <!-- Products will be loaded via AJAX -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input type="text" class="form-control" name="subject" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea class="form-control" name="message" rows="5" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="sendSingleEmailBtn">Send Email</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Customer Confirmation Modal -->
    <div class="modal fade" id="deleteCustomerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this customer? This action cannot be undone.</p>
                    <p class="text-danger">All customer data, including orders, addresses, and activity history will be
                        permanently deleted.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete Customer</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTables
            var customersTable = $('#customersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.customers') }}",
                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'registration_date',
                        name: 'created_at'
                    },
                    {
                        data: 'last_login',
                        name: 'last_login_at'
                    },
                    {
                        data: 'orders',
                        name: 'orders',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'location',
                        name: 'country'
                    },
                    {
                        data: 'status',
                        name: 'account_status'
                    },
                    {
                        data: 'segment',
                        name: 'customer_segment'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [1, 'desc']
                ]
            });

            // Initialize customer map
            var customerMap = L.map('customerMap').setView([20, 0], 2);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(customerMap);

            // Load customer locations via AJAX
            $.ajax({
                url: "{{ route('admin.customers.map-data') }}",
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        response.data.forEach(function(customer) {
                            if (customer.latitude && customer.longitude) {
                                L.marker([customer.latitude, customer.longitude])
                                    .addTo(customerMap)
                                    .bindPopup(
                                        `<b>${customer.name}</b><br>${customer.email}<br>${customer.formatted_location}`
                                    );
                            }
                        });
                    }
                }
            });

            // Load products for email recommendations
            $('select[name="email_type"]').change(function() {
                if ($(this).val() === 'product_recommendation') {
                    $('.product-recommendations-section, .single-product-recommendations-section').show();
                    loadProducts();
                } else {
                    $('.product-recommendations-section, .single-product-recommendations-section').hide();
                }
            });

            function loadProducts() {
                $.ajax({
                    url: "{{ route('admin.products.list') }}",
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            var options = '';
                            response.data.forEach(function(product) {
                                options +=
                                    `<option value="${product.id}">${product.name} - ₦${product.price}</option>`;
                            });
                            $('select[name="products[]"], select[name="product_ids[]"]').html(options);
                        }
                    }
                });
            }

            // Initialize Select2
            $('select[name="products[]"], select[name="product_ids[]"]').select2({
                placeholder: 'Select products',
                width: '100%'
            });

            // Send bulk email
            $('#sendBulkEmailBtn').click(function() {
                var formData = $('#bulkEmailForm').serialize();
                $.ajax({
                    url: "{{ route('admin.customers.bulk-email') }}",
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $('#sendBulkEmailBtn').html(
                            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...'
                        );
                        $('#sendBulkEmailBtn').prop('disabled', true);
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#bulkEmailModal').modal('hide');
                            $('#bulkEmailForm')[0].reset();
                        } else {
                            toastr.error(response.message || 'An error occurred');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('An error occurred while sending emails');
                        console.error(xhr.responseText);
                    },
                    complete: function() {
                        $('#sendBulkEmailBtn').html('Send Emails');
                        $('#sendBulkEmailBtn').prop('disabled', false);
                    }
                });
            });

            // Open single email modal
            $(document).on('click', '.send-email-btn', function() {
                var customerId = $(this).data('id');
                $('#emailCustomerId').val(customerId);
                $('#singleEmailModal').modal('show');
            });

            // Send single email
            $('#sendSingleEmailBtn').click(function() {
                var customerId = $('#emailCustomerId').val();
                var formData = $('#singleEmailForm').serialize();

                $.ajax({
                    url: `/admin/customers/${customerId}/email`,
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $('#sendSingleEmailBtn').html(
                            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...'
                        );
                        $('#sendSingleEmailBtn').prop('disabled', true);
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#singleEmailModal').modal('hide');
                            $('#singleEmailForm')[0].reset();
                        } else {
                            toastr.error(response.message || 'An error occurred');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('An error occurred while sending email');
                        console.error(xhr.responseText);
                    },
                    complete: function() {
                        $('#sendSingleEmailBtn').html('Send Email');
                        $('#sendSingleEmailBtn').prop('disabled', false);
                    }
                });
            });

            // Open delete confirmation modal
            $(document).on('click', '.delete-customer-btn', function() {
                var customerId = $(this).data('id');
                $('#confirmDeleteBtn').data('id', customerId);
                $('#deleteCustomerModal').modal('show');
            });

            // Confirm delete customer
            $('#confirmDeleteBtn').click(function() {
                var customerId = $(this).data('id');

                $.ajax({
                    url: `/admin/customers/${customerId}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $('#confirmDeleteBtn').html(
                            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...'
                        );
                        $('#confirmDeleteBtn').prop('disabled', true);
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#deleteCustomerModal').modal('hide');
                            customersTable.ajax.reload();
                        } else {
                            toastr.error(response.message || 'An error occurred');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('An error occurred while deleting customer');
                        console.error(xhr.responseText);
                    },
                    complete: function() {
                        $('#confirmDeleteBtn').html('Delete Customer');
                        $('#confirmDeleteBtn').prop('disabled', false);
                    }
                });
            });

            // Export customers
            $('#exportCustomersBtn').click(function() {
                window.location.href = "{{ route('admin.customers.export') }}";
            });
        });
    </script>
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/6.6.6/css/flag-icons.min.css">
    <style>
        .avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            color: #fff;
        }

        .fas-user-check,
        .fas-user-plus,
        .fas-globe,
        .fas-users {
            font-size: 1.5rem !important;
            color: #fff !important;

        }

        .avatar-lg {
            width: 64px;
            height: 64px;
        }

        .card-header-actions {
            display: flex;
            gap: 0.5rem;
        }

        #customerMap {
            border-radius: 0.25rem;
        }
    </style>
@endsection
