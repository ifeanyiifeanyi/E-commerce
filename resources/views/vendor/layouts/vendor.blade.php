<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>{{ config('app.name', 'Laravel') }} | @yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Vendor dashboard for managing your store and products" />
    <meta name="author" content="{{ config('app.name', 'Laravel') }}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="shortcut icon" href="">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css"
        rel="stylesheet" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.4.1/css/all.min.css" rel="stylesheet" />
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .sidebar {
            background-color: white;
            min-height: 100vh;
            border-right: 1px solid #dee2e6;
            transition: all 0.3s;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            width: 250px;
        }

        .sidebar-collapsed {
            margin-left: -250px;
        }

        .sidebar .nav-link {
            color: #6c757d;
            font-size: 0.9rem;
            padding: 0.625rem 1rem;
            border-radius: 0.25rem;
            margin: 0.125rem 0;
        }

        .sidebar .nav-link:hover {
            background-color: #f8f9fa;
            color: #212529;
        }

        .sidebar .nav-link.active {
            background-color: #f0f4ff;
            color: #0d6efd;
            font-weight: 500;
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            font-size: 1.1rem;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s;
        }

        .main-content-expanded {
            margin-left: 0;
        }

        .topbar {
            background-color: #212529;
            padding: 0.75rem 1.5rem;
            color: white;
            width: 100% !important;
        }

        .search-bar {
            max-width: 400px;
        }

        .stat-card {
            border-radius: 0.5rem;
            padding: 1rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .stat-card h3 {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 0;
        }

        .stat-card p {
            font-size: 0.85rem;
            margin-bottom: 0;
            color: #6c757d;
        }

        .stat-card i {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .pink-bg {
            background-color: #fdd8e0;
        }

        .orange-bg {
            background-color: #ffecd0;
        }

        .green-bg {
            background-color: #d1f5ea;
        }

        .purple-bg {
            background-color: #e2d9f3;
        }

        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .table th {
            font-weight: 500;
            border-top: none;
        }

        .table td {
            vertical-align: middle;
        }

        .support-chat {
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 1000;
        }

        .support-chat-button {
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            font-weight: 500;
            box-shadow: 0 0.25rem 0.5rem rgba(40, 167, 69, 0.3);
        }

        .support-chat-button:hover {
            background-color: #218838;
        }

        .footer {
            background-color: #212529;
            color: white;
            padding: 1rem;
            text-align: center;
            font-size: 0.85rem;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 0.5rem;
        }

        .footer a {
            color: white;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .profile-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 500;
        }

        @media (max-width: 992px) {
            .sidebar {
                margin-left: -250px;
            }

            .sidebar-expanded {
                margin-left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .hamburger-menu {
                display: block;
            }
        }

        /* --- Responsive Adjustments --- */

        /* Prevent body scrollbars as a last resort */
        body {
            overflow-x: hidden;
        }

        /* Ensure main content doesn't cause overflow */
        .main-content {
            overflow-x: hidden;
            padding: 1.25rem;
            /* Default padding */
        }

        /* Adjust topbar padding */
        .topbar {
            padding-left: 1rem;
            /* Default padding */
            padding-right: 1rem;
        }

        /* Allow tables to scroll horizontally if needed */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            /* Smooth scrolling on iOS */
        }

        /* --- Medium Screens (Tablets, etc.) --- */
        @media (max-width: 991.98px) {
            .main-content {
                padding: 1rem;
                /* Slightly less padding */
            }

            /* Ensure search bar is hidden below large screens if needed */
            .search-bar-container {
                /* Removed display: none here, rely on d-md-flex */
            }
        }

        /* --- Small Screens (Landscape Phones, etc.) --- */
        @media (max-width: 767.98px) {

            /* No specific changes here, relying on d-md-flex and flex properties */
            .topbar {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            .search-bar-container {
                display: none !important;
                /* Explicitly hide search on smaller than md */
            }
        }

        /* --- Extra Small Screens (Portrait Phones) --- */
        @media (max-width: 575.98px) {
            .main-content {
                padding: 0.75rem;
                /* Even less padding */
            }

            .topbar {
                padding-left: 0.5rem;
                /* Minimal padding */
                padding-right: 0.5rem;
            }

            .topbar .btn {
                /* Reduce padding on all topbar buttons */
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
            }

            .topbar .profile-circle {
                /* Slightly smaller profile circle */
                width: 32px;
                height: 32px;
                font-size: 0.85rem;
            }

            .topbar .bi-heart,
            .topbar .bi-cart3 {
                font-size: 1.1rem;
                /* Adjust icon size */
                margin-right: 0.5rem !important;
                /* Reduce margin */
            }

            .stat-card h3 {
                font-size: 1.5rem;
                /* Slightly smaller font size for stats */
            }

            .table th,
            .table td {
                padding: 0.5rem 0.4rem;
                /* Reduce table cell padding */
                white-space: nowrap;
                /* Prevent wrapping that might cause issues */
                font-size: 0.85rem;
                /* Smaller font in tables */
            }

            h1.h3 {
                /* Reduce main heading size */
                font-size: 1.5rem;
            }
        }
    </style>

    @yield('css')
</head>

<body>
    <div id="app-layout">
        <!-- Top Navigation Bar -->
        @include('vendor.layouts.partials.navbar')

        <!-- Sidebar Navigation -->
        @include('vendor.layouts.partials.sidebar')

        <!-- Main Content Area -->
        <!-- Main Content -->
        <div class="main-content" id="main-content" style="height: 100%">

            @yield('vendor')

        </div>
        <!-- Footer -->
        @include('vendor.layouts.partials.footer')
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

    <script>
        // Sidebar Toggle Functionality
        document.addEventListener("DOMContentLoaded", function() {
            const sidebar = document.getElementById("sidebar");
            const mainContent = document.getElementById("main-content");
            const sidebarToggle = document.getElementById("sidebar-toggle");

            // Check screen size on load
            checkScreenSize();

            // Toggle sidebar on button click
            sidebarToggle.addEventListener("click", function() {
                sidebar.classList.toggle("sidebar-expanded");
            });

            // Check screen size on window resize
            window.addEventListener("resize", checkScreenSize);

            function checkScreenSize() {
                if (window.innerWidth < 992) {
                    sidebar.classList.remove("sidebar-expanded");
                    mainContent.classList.add("main-content-expanded");
                } else {
                    sidebar.classList.remove("sidebar-expanded");
                    mainContent.classList.remove("main-content-expanded");
                }
            }

            // Sales Chart
            const ctx = document.getElementById("salesChart").getContext("2d");
            const salesChart = new Chart(ctx, {
                type: "line",
                data: {
                    labels: ["Week 1", "Week 2", "Week 3", "Week 4"],
                    datasets: [{
                            label: "Sales",
                            data: [15000, 21000, 18000, 24000],
                            borderColor: "rgba(40, 167, 69, 1)",
                            backgroundColor: "rgba(40, 167, 69, 0.1)",
                            tension: 0.4,
                            fill: true,
                        },
                        {
                            label: "Visits",
                            data: [25000, 32000, 28000, 35000],
                            borderColor: "rgba(13, 110, 253, 1)",
                            backgroundColor: "rgba(13, 110, 253, 0.1)",
                            tension: 0.4,
                            fill: true,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: "bottom",
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return "$" + value / 1000 + "k";
                                },
                            },
                        },
                    },
                },
            });
        });
    </script>







    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
        // SweetAlert Notifications
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: "{{ session('success') }}",
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: "{{ session('error') }}",
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        @if (session('warning'))
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: "{{ session('warning') }}",
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: "{!! implode('\n', $errors->all()) !!}",
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        @if (session('exception'))
            Swal.fire({
                icon: 'error',
                title: 'Exception Occurred!',
                text: "{{ session('exception') }}",
                timer: 3000,
                timerProgressBar: true
            });
        @endif
    </script>

    @yield('js')
</body>

</html>
