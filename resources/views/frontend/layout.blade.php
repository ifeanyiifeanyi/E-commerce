<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name') }} - @yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <!-- Animate.css -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet" />
    <!-- Custom Styles -->
    <style>
        :root {
            --primary: #fd7e14;
            --secondary: #6c757d;
            --success: #28a745;
            --dark: #343a40;
            --light: #f8f9fa;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 600;
            color: var(--dark);
        }

        .navbar-brand span {
            color: var(--primary);
        }

        .search-form {
            width: 40%;
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background-color: #e67211;
            border-color: #e67211;
        }

        .btn-outline-primary {
            color: var(--primary);
            border-color: var(--primary);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .hero-section {
            background-color: #fff8f0;
            padding: 2rem 0;
            border-radius: 10px;
        }

        .category-icon {
            font-size: 2rem;
            color: var(--primary);
        }

        .product-card {
            transition: transform 0.3s;
            border-radius: 8px;
            overflow: hidden;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .product-title {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            height: 40px;
            overflow: hidden;
        }

        .product-price {
            color: var(--primary);
            font-weight: 700;
        }

        .section-title {
            position: relative;
            display: inline-block;
            margin-bottom: 2rem;
        }

        .section-title:after {
            content: "";
            position: absolute;
            left: 0;
            bottom: -10px;
            width: 60%;
            height: 3px;
            background-color: var(--primary);
        }

        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .banner {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
        }

        .banner-content {
            position: absolute;
            top: 50%;
            left: 10%;
            transform: translateY(-50%);
            color: white;
            max-width: 50%;
        }

        footer {
            background-color: #343a40;
            color: white;
        }

        .footer-links h5 {
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .footer-links ul {
            list-style: none;
            padding-left: 0;
        }

        .footer-links li {
            margin-bottom: 0.75rem;
        }

        .footer-links a {
            color: #bbbbbb;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: white;
        }

        .social-icon {
            color: white;
            font-size: 1.5rem;
            margin-right: 1rem;
            transition: color 0.3s;
        }

        .social-icon:hover {
            color: var(--primary);
        }

        .vendor-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        #notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            font-size: 0.6rem;
        }

        .announcement-bar {
            background-color: var(--primary);
            color: white;
            padding: 5px 0;
            font-size: 0.9rem;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
        }

        .stat-text {
            font-size: 0.9rem;
            color: var(--secondary);
        }

        .featured-section {
            background-color: #f8f9fa;
            padding: 3rem 0;
            margin: 3rem 0;
        }

        .category-pill {
            background-color: #fff8f0;
            border: 1px solid #ffe8cc;
            color: var(--primary);
            padding: 8px 15px;
            margin: 5px;
            border-radius: 20px;
            display: inline-block;
            transition: all 0.3s;
            cursor: pointer;
        }

        .category-pill:hover {
            background-color: var(--primary);
            color: white;
        }

        .category-icon {
            margin-right: 5px;
        }
    </style>
    @yield('styles')
</head>

<body>

    {{-- annoucement and navbar --}}
    @include('frontend.partials.navbar')

    <!-- Hero Section -->
    @include('frontend.partials.hero-section')

    <div>
        @yield('content')
    </div>
    
  {{-- footer section  --}}
    @include('frontend.partials.footer')



    <!-- Quick View Modal and login modal-->
    @include('frontend.partials.modals')

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript for Quantity Input -->
    <script>
        document
            .getElementById("incrementBtn")
            .addEventListener("click", function() {
                let input = document.getElementById("quantityInput");
                input.value = parseInt(input.value) + 1;
            });

        document
            .getElementById("decrementBtn")
            .addEventListener("click", function() {
                let input = document.getElementById("quantityInput");
                if (parseInt(input.value) > 1) {
                    input.value = parseInt(input.value) - 1;
                }
            });
    </script>
    <script type="text/javascript">
        var gk_isXlsx = false;
        var gk_xlsxFileLookup = {};
        var gk_fileData = {};

        function filledCell(cell) {
            return cell !== "" && cell != null;
        }

        function loadFileData(filename) {
            if (gk_isXlsx && gk_xlsxFileLookup[filename]) {
                try {
                    var workbook = XLSX.read(gk_fileData[filename], {
                        type: "base64"
                    });
                    var firstSheetName = workbook.SheetNames[0];
                    var worksheet = workbook.Sheets[firstSheetName];

                    // Convert sheet to JSON to filter blank rows
                    var jsonData = XLSX.utils.sheet_to_json(worksheet, {
                        header: 1,
                        blankrows: false,
                        defval: "",
                    });
                    // Filter out blank rows (rows where all cells are empty, null, or undefined)
                    var filteredData = jsonData.filter((row) => row.some(filledCell));

                    // Heuristic to find the header row by ignoring rows with fewer filled cells than the next row
                    var headerRowIndex = filteredData.findIndex(
                        (row, index) =>
                        row.filter(filledCell).length >=
                        filteredData[index + 1]?.filter(filledCell).length
                    );
                    // Fallback
                    if (headerRowIndex === -1 || headerRowIndex > 25) {
                        headerRowIndex = 0;
                    }

                    // Convert filtered JSON back to CSV
                    var csv = XLSX.utils.aoa_to_sheet(filteredData.slice(
                        headerRowIndex)); // Create a new sheet from filtered array of arrays
                    csv = XLSX.utils.sheet_to_csv(csv, {
                        header: 1
                    });
                    return csv;
                } catch (e) {
                    console.error(e);
                    return "";
                }
            }
            return gk_fileData[filename] || "";
        }
    </script>
    @yield('scripts')
</body>

</html>
