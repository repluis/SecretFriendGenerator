<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Secret Santa')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Mountains+of+Christmas:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap');

        /* Base Admin Styles from app.blade.php context */
        body {
            font-family: 'Inter', sans-serif;
            background: #0f4c3a; /* Christmas dark green base */
            background: linear-gradient(135deg, #0f4c3a 0%, #1a5f4a 50%, #0f4c3a 100%);
            background-attachment: fixed;
            color: #1e293b;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Navbar — identical to app.blade.php */
        .navbar {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .navbar-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .navbar-brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: #6366f1;
            text-decoration: none;
        }

        .navbar-links {
            display: flex;
            gap: 1.5rem;
        }

        .navbar-links a {
            color: #64748b;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.2s;
        }

        .navbar-links a:hover,
        .navbar-links a.active { color: #6366f1; }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2.5rem 1.5rem;
            position: relative;
            z-index: 2;
        }

        /* Snowflakes */
        .snowflake {
            position: fixed; top: -10px; color: white; animation: fall linear infinite; z-index: 1; pointer-events: none;
        }
        @keyframes fall { to { transform: translateY(100vh) rotate(360deg); } }

        /* Floating decorations */
        .floating-decorations {
            position: fixed; inset: 0; pointer-events: none; z-index: 0; overflow: hidden;
        }
        .floating-emoji { position: absolute; animation: floatEmoji 15s ease-in-out infinite; opacity: 0.2; font-size: 2rem; }
        @keyframes floatEmoji { 0%, 100% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-30px) rotate(10deg); } }

        /* Christmas Elements */
        .christmas-title {
            font-family: 'Mountains of Christmas', cursive;
            color: #ffd700;
            text-shadow: 2px 2px 0px #c41e3a, 4px 4px 0px #c41e3a, 0 0 20px rgba(255, 215, 0, 0.5);
            font-size: 3rem;
            margin-bottom: 2.5rem;
            text-align: center;
        }

        .christmas-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3), inset 0 0 50px rgba(255, 215, 0, 0.05);
            border: 3px solid #c41e3a;
            position: relative;
            padding: 2rem;
            margin-bottom: 2rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .christmas-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.4);
        }

        /* Buttons adaptation */
        .christmas-btn {
            background: linear-gradient(135deg, #c41e3a 0%, #8b1538 100%);
            color: white; border: 2px solid #ffd700; padding: 0.6rem 1.5rem; border-radius: 8px;
            font-weight: 600; cursor: pointer; transition: all 0.3s ease;
        }
        .christmas-btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(196, 30, 58, 0.4); }

    </style>
    @yield('styles')
</head>
<body>
    @include('layouts.partials.navbar', ['active' => $navbarActive ?? 'game'])

    <!-- Snowflakes -->
    @for($i = 0; $i < 20; $i++)
    <div class="snowflake" style="left: {{ rand(0, 100) }}%; animation-duration: {{ rand(5, 15) }}s; animation-delay: {{ rand(0, 10) / 10 }}s; font-size: {{ rand(8, 20) / 10 }}em; opacity: {{ rand(3, 8) / 10 }};">&#10052;</div>
    @endfor

    <div class="floating-decorations">
        @php $emojis = ['🎄', '⭐', '🎁', '🔔', '❄️']; @endphp
        @for($i = 0; $i < 8; $i++)
        <span class="floating-emoji" style="left: {{ rand(2, 95) }}%; top: {{ rand(5, 90) }}%; animation-delay: {{ $i * 1.5 }}s; animation-duration: {{ rand(12, 25) }}s;">{{ $emojis[$i % count($emojis)] }}</span>
        @endfor
    </div>

    <div class="container">
        @yield('content')
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>
    @yield('scripts')
</body>
</html>
