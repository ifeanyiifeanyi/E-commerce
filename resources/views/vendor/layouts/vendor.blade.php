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

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet"
        type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet"
        type="text/css" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css"
        rel="stylesheet" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.4.1/css/all.min.css" rel="stylesheet" />
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet"
        type="text/css" />

    <link rel="stylesheet" href="{{ asset('vendorsrc/assets/css/main.css') }}">
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



        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize DataTables
            $('#datatables').DataTable({
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search Details...",
                    emptyTable: "Not available"
                },
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'excel', 'pdf', 'print'
                ]
            });

            $('#table').DataTable({
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search ...",
                    emptyTable: "No records found"
                },
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'excel', 'pdf', 'print'
                ],
                order: [
                    [0, 'desc']
                ]
            });
        });
    </script>


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
