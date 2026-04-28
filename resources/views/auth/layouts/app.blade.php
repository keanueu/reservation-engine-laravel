<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('LOGO-FINAL.png') }}" type="image/x-icon">
    <title>@yield('title', '')</title>
       <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/build/assets/app.css">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/auth-custom.css">
    <script src="/js/tailwind-config.js"></script>
    @stack('styles')
</head>
<body class="font-[Manrope] antialiased bg-dark-bg">
    @include('auth.partials.cabanas-loader')
    <div id="main-content" class="opacity-0">
        @yield('content')
    </div>
    <script src="/js/auth-loader.js"></script>
</body>
</html>