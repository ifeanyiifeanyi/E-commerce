@extends('vendor.layouts.vendor')

@section('title', 'Dashboard')

@section('vendor')


    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Hello, {{ auth()->user()->full_name }}</h1>
            <p class="text-muted">Welcome to your dashboard! <i class="bi bi-info-circle"></i></p>
        </div>
        <div>
            <a href="" class="btn btn-light btn-sm me-2">
                <i class="bi bi-plus-circle"></i> Add Product
            </a>
            <a href="" class="btn btn-light btn-sm">
                <i class="bi bi-gear"></i> Settings
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <h5 class="mb-3">Today's Stats</h5>
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="stat-card pink-bg">
                <div>
                    <i class="bi bi-bag text-danger"></i>
                    <p>Sales</p>
                </div>
                <h3>$15k</h3>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="stat-card orange-bg">
                <div>
                    <i class="bi bi-currency-dollar text-warning"></i>
                    <p>Earnings</p>
                </div>
                <h3>$28k</h3>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="stat-card green-bg">
                <div>
                    <i class="bi bi-eye text-success"></i>
                    <p>Page Views</p>
                </div>
                <h3>16k</h3>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="stat-card purple-bg">
                <div>
                    <i class="bi bi-person-add text-primary"></i>
                    <p>New Customers</p>
                </div>
                <h3>2.4k</h3>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Orders Table -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Orders</h5>
                    <button class="btn btn-sm btn-outline-secondary">View All</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <td><span class="badge bg-success">Completed</span></td>
                                    <td>7</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-warning">Processing</span></td>
                                    <td>3</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-primary">Shipping</span></td>
                                    <td>2</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-secondary">Backorder</span></td>
                                    <td>5</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-danger">Returned</span></td>
                                    <td>1</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Chart -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Sales of the Month</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            This Month
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="#">This Week</a></li>
                            <li><a class="dropdown-item" href="#">This Month</a></li>
                            <li><a class="dropdown-item" href="#">This Year</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Selling Products Table -->
    <div class="card mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Top Selling Products</h5>
            <div class="text-muted small">Showing 5 of top selling products</div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Sold</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded p-2 me-3">
                                        <i class="bi bi-shoe"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">Running Shoes</div>
                                        <div class="text-muted small">Sports</div>
                                    </div>
                                </div>
                            </td>
                            <td>$129.99</td>
                            <td>75 units</td>
                            <td>$9,749.25</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded p-2 me-3">
                                        <i class="bi bi-headphones"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">Bluetooth Headphones</div>
                                        <div class="text-muted small">Electronics</div>
                                    </div>
                                </div>
                            </td>
                            <td>$89.99</td>
                            <td>120 units</td>
                            <td>$10,798.80</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded p-2 me-3">
                                        <i class="bi bi-card-text"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">Slim-Fit T-Shirt</div>
                                        <div class="text-muted small">Clothing</div>
                                    </div>
                                </div>
                            </td>
                            <td>$24.99</td>
                            <td>240 units</td>
                            <td>$5,997.60</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('css')

@endsection

@section('js')

@endsection
