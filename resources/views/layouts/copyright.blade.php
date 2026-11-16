<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" href="{{ asset('assets/img/logo_kpum.png') }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sb-admin.min.css') }}">
    <link rel="stylesheet" href="https://naramizaru.github.io/fa-pro/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #16181f;
            color: #e2e8f0;
            min-height: 100vh;
        }
    </style>
    @stack('css')
    <title>{{ config('app.name') }} | @yield('title')</title>
</head>

<body>
    @yield('content')

    @stack('js')
</body>

</html>
