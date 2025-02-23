<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }} | @yield('title')</title>
    <link rel="stylesheet" type="text/css" href="/auth/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/auth/css/fontawesome-all.min.css">
    <link rel="stylesheet" type="text/css" href="/auth/css/iofrm-style.css">
    <link rel="stylesheet" type="text/css" href="/auth/css/iofrm-theme2.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>
<body>
    <div class="form-body">
        <div class="website-logo">
            <a href="/">
                <div class="logo">
                    <img class="logo-size" src="/auth/images/logo-light.svg" alt="">
                </div>
            </a>
        </div>
        <div class="iofrm-layout">
            <div class="img-holder">
                <div class="bg"></div>
                <div class="info-holder">
                </div>
            </div>
            @yield('auth-content')
        </div>
    </div>
<script src="/auth/js/jquery.min.js"></script>
<script src="/auth/js/popper.min.js"></script>
<script src="/auth/js/bootstrap.bundle.min.js"></script>
<script src="/auth/js/main.js"></script>
</body>
</html>
