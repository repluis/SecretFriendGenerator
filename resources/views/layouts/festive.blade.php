<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Secret Santa')</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Mountains+of+Christmas:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Design System -->
    <link rel="stylesheet" href="{{ asset('css/design-system.css') }}">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Christmas Theme Override */
        body {
            background: #0f4c3a;
            background: linear-gradient(135deg, #0f4c3a 0%, #1a5f4a 50%, #0f4c3a 100%);
            background-attachment: fixed;
            position: relative;
            overflow-x: hidden;
        }

        /* Snowflakes */
        .snowflake {
            position: fixed;
            top: -10px;
            color: white;
            animation: fall linear infinite;
            z-index: 1;
            pointer-events: none;
        }
        
        @keyframes fall {
            to {
                transform: translateY(100vh) rotate(360deg);
            }
        }

        /* Floating decorations */
        .floating-decorations {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }
        
        .floating-emoji {
            position: absolute;
            animation: floatEmoji 15s ease-in-out infinite;
            opacity: 0.2;
            font-size: 2rem;
        }
        
        @keyframes floatEmoji {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            50% {
                transform: translateY(-30px) rotate(10deg);
            }
        }

        /* Christmas Title */
        .christmas-title {
            font-family: 'Mountains of Christmas', cursive;
            color: #ffd700;
            text-shadow: 2px 2px 0px #c41e3a, 4px 4px 0px #c41e3a, 0 0 20px rgba(255, 215, 0, 0.5);
            font-size: 3rem;
            margin-bottom: 2.5rem;
            text-align: center;
            font-weight: 700;
        }

        /* Christmas Card */
        .christmas-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: var(--radius-lg);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3), inset 0 0 50px rgba(255, 215, 0, 0.05);
            border: 3px solid #c41e3a;
            position: relative;
            padding: var(--spacing-xl);
            margin-bottom: var(--spacing-xl);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .christmas-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.4);
        }

        /* Christmas Button */
        .christmas-btn {
            background: linear-gradient(135deg, #c41e3a 0%, #8b1538 100%);
            color: white;
            border: 2px solid #ffd700;
            padding: var(--spacing-sm) var(--spacing-lg);
            border-radius: var(--radius-md);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-xs);
            text-decoration: none;
            font-family: var(--font-sans);
            font-size: 0.875rem;
        }
        
        .christmas-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(196, 30, 58, 0.4);
        }

        /* Override main container for festive layout */
        .main-container {
            position: relative;
            z-index: 2;
        }

        /* Empty state for christmas theme */
        .christmas-empty {
            text-align: center;
            padding: var(--spacing-2xl);
            color: var(--color-slate-600);
            font-size: 1.125rem;
        }

        @media (max-width: 640px) {
            .christmas-title {
                font-size: 2rem;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    @include('layouts.partials.navbar', ['active' => $navbarActive ?? 'juego'])

    <!-- Snowflakes -->
    @for($i = 0; $i < 20; $i++)
    <div class="snowflake" style="left: {{ rand(0, 100) }}%; animation-duration: {{ rand(5, 15) }}s; animation-delay: {{ rand(0, 10) / 10 }}s; font-size: {{ rand(8, 20) / 10 }}em; opacity: {{ rand(3, 8) / 10 }};">&#10052;</div>
    @endfor

    <!-- Floating Decorations -->
    <div class="floating-decorations">
        @php $emojis = ['🎄', '⭐', '🎁', '🔔', '❄️']; @endphp
        @for($i = 0; $i < 8; $i++)
        <span class="floating-emoji" style="left: {{ rand(2, 95) }}%; top: {{ rand(5, 90) }}%; animation-delay: {{ $i * 1.5 }}s; animation-duration: {{ rand(12, 25) }}s;">{{ $emojis[$i % count($emojis)] }}</span>
        @endfor
    </div>

    <main class="main-container">
        @yield('content')
    </main>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>
    @yield('scripts')
</body>
</html>
