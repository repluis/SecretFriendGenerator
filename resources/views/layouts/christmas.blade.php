<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Secret Friend Generator')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Mountains+of+Christmas:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap');

        body {
            background: linear-gradient(135deg, #0f4c3a 0%, #1a5f4a 50%, #0f4c3a 100%);
            background-attachment: fixed;
            font-family: 'Poppins', sans-serif;
            position: relative;
            overflow-x: hidden;
            min-height: 100vh;
        }

        /* Snowflakes */
        .snowflake {
            position: fixed;
            top: -10px;
            color: white;
            font-size: 1em;
            font-family: Arial;
            text-shadow: 0 0 5px rgba(255,255,255,0.8);
            animation: fall linear infinite;
            z-index: 1;
            pointer-events: none;
        }

        @keyframes fall {
            to { transform: translateY(100vh) rotate(360deg); }
        }

        /* Christmas title */
        .christmas-title {
            font-family: 'Mountains of Christmas', cursive;
            color: #ffd700;
            text-shadow:
                2px 2px 0px #c41e3a,
                4px 4px 0px #c41e3a,
                0 0 20px rgba(255, 215, 0, 0.5);
            font-size: 2.5rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        @media (max-width: 640px) {
            .christmas-title { font-size: 1.8rem; }
        }

        /* Christmas card */
        .christmas-card {
            background: linear-gradient(135deg, #fff9e6 0%, #fff 100%);
            border-radius: 15px;
            box-shadow:
                0 10px 30px rgba(0,0,0,0.3),
                inset 0 0 50px rgba(255, 215, 0, 0.1);
            border: 3px solid #c41e3a;
            position: relative;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .christmas-card:hover {
            transform: translateY(-5px);
            box-shadow:
                0 15px 40px rgba(0,0,0,0.4),
                inset 0 0 60px rgba(255, 215, 0, 0.15);
        }

        /* Christmas buttons */
        .christmas-btn {
            background: linear-gradient(135deg, #c41e3a 0%, #8b1538 100%);
            color: white;
            border: 2px solid #ffd700;
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }

        .christmas-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(196, 30, 58, 0.4);
        }

        .christmas-btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }

        .christmas-btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }

        .christmas-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* Christmas table */
        .christmas-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        .christmas-table thead th {
            background: #c41e3a;
            color: white;
            padding: 0.75rem;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }

        .christmas-table tbody td {
            padding: 0.6rem 0.75rem;
            border-bottom: 1px solid #f0e6d6;
            font-size: 0.85rem;
            color: #333;
        }

        .christmas-table tbody tr:hover {
            background: #fff9e6;
        }

        /* Christmas container */
        .christmas-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 2rem 1rem;
            position: relative;
            z-index: 2;
        }

        /* Badge Christmas */
        .christmas-badge {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 600;
        }

        .christmas-badge-green { background: #dcfce7; color: #16a34a; }
        .christmas-badge-red { background: #fee2e2; color: #dc2626; }
        .christmas-badge-amber { background: #fef3c7; color: #d97706; }

        /* Floating decorations */
        .floating-decorations {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }

        .floating-emoji {
            position: absolute;
            animation: floatEmoji 15s ease-in-out infinite;
            opacity: 0.3;
            font-size: 2rem;
        }

        @keyframes floatEmoji {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(10deg); }
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Snowflakes -->
    @for($i = 0; $i < 30; $i++)
    <div class="snowflake" style="left: {{ rand(0, 100) }}%; animation-duration: {{ rand(5, 15) }}s; animation-delay: {{ rand(0, 10) / 10 }}s; font-size: {{ rand(8, 20) / 10 }}em; opacity: {{ rand(3, 10) / 10 }};">&#10052;</div>
    @endfor

    <!-- Floating decorations -->
    <div class="floating-decorations">
        @php $emojis = ['🎄', '⭐', '🎁', '🔔', '❄️', '🎅', '🦌', '🕯️']; @endphp
        @for($i = 0; $i < 12; $i++)
        <span class="floating-emoji" style="left: {{ rand(2, 95) }}%; top: {{ rand(5, 90) }}%; animation-delay: {{ $i * 1.2 }}s; animation-duration: {{ rand(10, 20) }}s;">{{ $emojis[$i % count($emojis)] }}</span>
        @endfor
    </div>

    @yield('content')

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>
    @yield('scripts')
</body>
</html>
