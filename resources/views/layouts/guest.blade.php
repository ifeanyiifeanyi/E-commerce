<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }} | @yield('title')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        :root {
            --cinta-green: #00b300;
            --cinta-dark: #333333;
            --cinta-light-bg: #f5f5f5;
        }

        body {
            background-color: var(--cinta-light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background-color: white;
            border-bottom: 1px solid #e0e0e0;
        }

        .search-form input,
        .search-form button {
            height: 42px;
        }

        .search-form button {
            background-color: var(--cinta-green);
            border-color: var(--cinta-green);
            color: white;
        }

        .main-nav {
            background-color: var(--cinta-dark);
        }

        .main-nav .nav-link {
            color: white;
            padding: 15px 20px;
            font-weight: 500;
        }

        .main-nav .dropdown-toggle::after {
            margin-left: 8px;
        }

        /* Center the navigation */
        .navbar-nav {
            margin: 0 auto;
        }

        /* Dropdown styling */
        .dropdown-menu {
            background-color: white;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            margin-top: 0;
        }

        .dropdown-menu .dropdown-item {
            padding: 10px 20px;
            color: var(--cinta-dark);
        }

        .dropdown-menu .dropdown-item:hover {
            background-color: var(--cinta-light-bg);
            color: var(--cinta-green);
        }

        /* Responsive styles */
        @media (max-width: 991.98px) {
            .search-form {
                width: 100% !important;
                margin-bottom: 10px;
            }

            .header-actions {
                width: 100% !important;
                justify-content: center;
                margin-top: 10px;
            }

            .navbar-nav {
                text-align: center;
            }

            .main-nav .dropdown-menu {
                background-color: transparent;
                border: none;
                text-align: center;
            }

            .main-nav .dropdown-item {
                color: white;
            }

            .main-nav .dropdown-item:hover {
                background-color: rgba(255, 255, 255, 0.1);
                color: white;
            }

            .navbar-toggler {
                background-color: rgba(255, 255, 255, 0.2);
                border: none;
                padding: 8px 10px;
                margin: 8px 0;
            }

            .navbar-toggler-icon {
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='30' height='30' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
            }
        }

        /* Other existing styles */
        .auth-container {
            max-width: 720px;
            margin: 2rem auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 2rem;
        }

        .progress-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .step {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: var(--cinta-green);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            position: relative;
            z-index: 2;
        }

        .step.active {
            background-color: var(--cinta-green);
        }

        .step.completed {
            background-color: var(--cinta-green);
        }

        .step.incomplete {
            background-color: #ccc;
        }

        .step-connector {
            height: 2px;
            background-color: #ccc;
            flex-grow: 1;
            margin-top: 16px;
            max-width: 80px;
        }

        .step-connector.active {
            background-color: var(--cinta-green);
        }

        .auth-form {
            max-width: 600px;
            margin: 0 auto;
        }

        .auth-title {
            font-size: 24px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 5px;
        }

        .auth-subtitle {
            font-size: 14px;
            color: #666;
            text-align: center;
            margin-bottom: 25px;
        }

        .btn-primary {
            background-color: var(--cinta-green);
            border-color: var(--cinta-green);
            padding: 12px 20px;
            font-weight: 500;
        }

        .btn-primary:hover {
            background-color: #009900;
            border-color: #009900;
        }

        .form-control {
            padding: 12px 15px;
            border-radius: 4px;
        }

        .form-control:focus {
            border-color: var(--cinta-green);
            box-shadow: 0 0 0 0.25rem rgba(0, 179, 0, 0.25);
        }

        .selector-option {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
        }

        .selector-option.selected {
            border-color: var(--cinta-green);
        }

        .back-link {
            color: var(--cinta-green);
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .back-link i {
            margin-right: 5px;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 12px;
            cursor: pointer;
            color: #999;
        }
    </style>
</head>

<body>
    <!-- Header Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <div class="row w-100">
                <div class="col-lg-8 col-md-12">
                    <form class="d-flex search-form w-100">
                        <input class="form-control me-2" type="search" placeholder="Search products, categories etc..."
                            aria-label="Search">
                        <button class="btn" type="submit">Search</button>
                    </form>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div
                        class="d-flex ms-auto justify-content-lg-end justify-content-center header-actions mt-lg-0 mt-3">
                        <a href="#" class="nav-link dropdown-toggle">
                            <i class="fa fa-question-circle me-1"></i> Help
                        </a>
                        <a href="#" class="nav-link">
                            <i class="fa fa-shopping-cart me-1"></i> Cart
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Navigation -->
    <nav class="navbar navbar-expand-lg main-nav">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Shop
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">All Products</a></li>
                            <li><a class="dropdown-item" href="#">Categories</a></li>
                            <li><a class="dropdown-item" href="#">Featured Items</a></li>
                            <li><a class="dropdown-item" href="#">New Arrivals</a></li>
                            <li><a class="dropdown-item" href="#">Sale Items</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Pages
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">FAQ</a></li>
                            <li><a class="dropdown-item" href="#">Privacy Policy</a></li>
                            <li><a class="dropdown-item" href="#">Terms & Conditions</a></li>
                            <li><a class="dropdown-item" href="#">Shipping Information</a></li>
                            <li><a class="dropdown-item" href="#">Return Policy</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contact Us</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content Section -->
    <div class="container mt-4">
        @yield('content')
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script>
        $(document).ready(function() {
            // Toggle password visibility
            $('.password-toggle').click(function() {
                var input = $(this).closest('.position-relative').find('input');
                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    $(this).removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    input.attr('type', 'password');
                    $(this).removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Selector options
            $('.selector-option').click(function() {
                $(this).parent().find('.selector-option').removeClass('selected');
                $(this).addClass('selected');
                $(this).find('input[type="radio"]').prop('checked', true);
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to get user's location
            function getUserLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            // Store coordinates in hidden inputs
                            addHiddenInput('latitude', position.coords.latitude);
                            addHiddenInput('longitude', position.coords.longitude);
                        },
                        function(error) {
                            console.log('Geolocation error:', error.message);
                            // Don't prevent login if geolocation fails
                        }, {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 300000 // 5 minutes
                        }
                    );
                }
            }

            // Function to add hidden input to the form
            function addHiddenInput(name, value) {
                const form = document.querySelector('form');
                if (form) {
                    // Remove existing input if it exists
                    const existing = form.querySelector(`input[name="${name}"]`);
                    if (existing) {
                        existing.remove();
                    }

                    // Create new hidden input
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = name;
                    input.value = value;
                    form.appendChild(input);
                }
            }

            // Function to detect registration source
            function detectRegistrationSource() {
                let source = 'web'; // default

                // Check if it's a mobile device
                if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                    source = 'mobile_web';
                }

                // Check if it's from a social media referrer
                const referrer = document.referrer.toLowerCase();
                if (referrer.includes('facebook.com')) {
                    source = 'facebook';
                } else if (referrer.includes('twitter.com') || referrer.includes('t.co')) {
                    source = 'twitter';
                } else if (referrer.includes('linkedin.com')) {
                    source = 'linkedin';
                } else if (referrer.includes('instagram.com')) {
                    source = 'instagram';
                } else if (referrer.includes('google.com')) {
                    source = 'google_search';
                }

                addHiddenInput('registration_source', source);
            }

            // Get user location when page loads
            getUserLocation();

            // Detect registration source
            detectRegistrationSource();

            // Also try to get location when form is submitted (as a fallback)
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Small delay to allow geolocation to complete
                    setTimeout(() => {
                        // Form will submit normally after this
                    }, 100);
                });
            }
        });
    </script>
</body>

</html>
