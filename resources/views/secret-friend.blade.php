<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="5;url=/">
    <title>Tu Amigo Secreto - Secret Friend Generator</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .reveal-container {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
            max-width: 600px;
            width: 90%;
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .friend-name {
            font-size: 3rem;
            font-weight: bold;
            color: #667eea;
            margin: 2rem 0;
            text-transform: uppercase;
            letter-spacing: 2px;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }
        .countdown {
            font-size: 1.2rem;
            color: #666;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <div class="reveal-container">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Felicitaciones tu amigo secreto es</h1>
        <div class="friend-name">{{ $friendName }}</div>
        <div class="countdown" id="countdown">Esta página se cerrará automáticamente en <span id="seconds">5</span> segundos</div>
    </div>

    <script>
        let seconds = 5;
        const countdownElement = document.getElementById('seconds');
        
        const interval = setInterval(() => {
            seconds--;
            if (seconds > 0) {
                countdownElement.textContent = seconds;
            } else {
                clearInterval(interval);
                // Redirigir a la página principal
                window.location.href = '/';
            }
        }, 1000);
    </script>
</body>
</html>
