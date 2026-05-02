<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('LOGO-FINAL.png') }}" type="image/x-icon">
    <title>{{ config('app.name') }}</title>
    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <script src="/js/theme-init.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-[Manrope] bg-gray-100 dark:bg-zinc-900 text-gray-900 dark:text-white transition-colors duration-200">
    <div class="flex min-h-screen">
        <div class="flex min-h-screen">
            @include('admin.partials.sidebar')
        </div>
        <!-- main -->
        <main class="flex-1 flex flex-col h-screen overflow-y-auto lg:ml-64 transition-all duration-200 ease-in-out bg-gray-100 dark:bg-zinc-900">
            @include('admin.partials.header')
            <section class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </section>
        </main>
    </div>
    <script src="/js/admin-dark.js"></script>
    <script src="/js/admin-header.js"></script>
    @stack('admin-scripts')
</body>
</html>
