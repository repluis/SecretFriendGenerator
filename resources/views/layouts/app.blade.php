<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $appName)</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Design System -->
    <link rel="stylesheet" href="{{ asset('css/design-system.css') }}?v={{ time() }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
    <style>
        /* Estilos específicos de página se pueden agregar aquí */
    </style>
    @yield('styles')
</head>
<body>
    @include('layouts.partials.navbar', ['active' => $navbarActive ?? ''])

    <main class="main-container">
        @yield('content')
    </main>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>
    @yield('scripts')
</body>
</html>
