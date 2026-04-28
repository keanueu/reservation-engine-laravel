<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('LOGO-FINAL.png') }}" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <script src="/js/theme-init.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/output.css') }}" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-[Manrope] bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <div class="flex min-h-screen">
        @include('frontdesk.partials.sidebar')
        <main class="flex-1 flex flex-col h-screen overflow-y-auto lg:ml-64 transition-all duration-300 ease-in-out">
            @include('frontdesk.partials.header')
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>
        </main>
    </div>
    <script>
        // Inject server setting into page for frontdesk JS
        window.DEPOSIT_PERCENT = @json((float) \App\Models\Setting::get('deposit_percentage', config('booking.deposit_percentage', 50)));
    </script>
    <script src="/js/frontdesk-dark.js"></script>
    <script src="/js/frontdesk-header.js"></script>
    <script src="/js/frontdesk-booking.js"></script>
    <script src="/js/frontdesk-index.js"></script>
</body>

</html>