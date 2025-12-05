<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Secret Friend Generator - Home</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Mountains+of+Christmas:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap');
        
        body {
            background: linear-gradient(135deg, #0f4c3a 0%, #1a5f4a 50%, #0f4c3a 100%);
            background-attachment: fixed;
            font-family: 'Poppins', sans-serif;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Efecto de nieve */
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
            to {
                transform: translateY(100vh) rotate(360deg);
            }
        }
        
        /* Título navideño */
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
            .christmas-title {
                font-size: 1.8rem;
            }
        }
        
        /* Tarjeta tipo papel navideño */
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
        
        .christmas-card::before {
            content: '🎄';
            position: absolute;
            top: -15px;
            right: -15px;
            font-size: 2rem;
            transform: rotate(15deg);
            filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.3));
        }
        
        .christmas-card::after {
            content: '⭐';
            position: absolute;
            bottom: -10px;
            left: -10px;
            font-size: 1.5rem;
            transform: rotate(-15deg);
            filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.3));
        }
        
        /* URL oculta tipo papel doblado */
        .url-paper {
            background: #fff;
            border: 2px dashed #c41e3a;
            border-radius: 8px;
            padding: 0.75rem;
            margin-bottom: 1rem;
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .url-paper:hover {
            background: #fff9e6;
            border-color: #ffd700;
        }
        
        .url-paper .url-hidden {
            display: none;
            word-break: break-all;
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
            color: #333;
            padding-top: 0.5rem;
        }
        
        .url-paper.expanded .url-hidden {
            display: block;
        }
        
        .url-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
        }
        
        .url-toggle-text {
            color: #c41e3a;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .url-toggle-icon {
            transition: transform 0.3s ease;
            color: #c41e3a;
        }
        
        .url-paper.expanded .url-toggle-icon {
            transform: rotate(180deg);
        }
        
        /* Select navideño */
        .christmas-select {
            background: white;
            border: 2px solid #c41e3a;
            border-radius: 8px;
            padding: 0.75rem;
            width: 100%;
            font-size: 1rem;
            color: #333;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .christmas-select:focus {
            outline: none;
            border-color: #ffd700;
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.2);
        }
        
        .christmas-select:hover {
            border-color: #ffd700;
        }
        
        /* Botón navideño */
        .christmas-btn {
            background: linear-gradient(135deg, #c41e3a 0%, #8b1538 100%);
            color: white;
            border: 2px solid #ffd700;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(196, 30, 58, 0.3);
        }
        
        .christmas-btn:hover {
            background: linear-gradient(135deg, #8b1538 0%, #c41e3a 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(196, 30, 58, 0.4);
        }
        
        .christmas-btn:active {
            transform: translateY(0);
        }
        
        /* Tabla responsiva */
        .table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .table-container table {
            min-width: 100%;
        }
        
        @media (max-width: 768px) {
            .table-container {
                display: block;
            }
            
            .table-container table {
                display: block;
                width: 100%;
            }
            
            .table-container thead {
                display: none;
            }
            
            .table-container tbody {
                display: block;
            }
            
            .table-container tr {
                display: block;
                margin-bottom: 1rem;
                background: white;
                border-radius: 10px;
                padding: 1rem;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            }
            
            .table-container td {
                display: block;
                text-align: left;
                padding: 0.5rem 0;
                border: none;
            }
            
            .table-container td:before {
                content: attr(data-label);
                font-weight: 600;
                color: #c41e3a;
                display: block;
                margin-bottom: 0.25rem;
            }
        }
        
        /* Decoración navideña adicional */
        .decoration {
            position: absolute;
            font-size: 1.5rem;
            opacity: 0.6;
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .decoration-1 { top: 10%; left: 5%; animation-delay: 0s; }
        .decoration-2 { top: 20%; right: 5%; animation-delay: 1s; }
        .decoration-3 { bottom: 10%; left: 10%; animation-delay: 2s; }
        .decoration-4 { bottom: 20%; right: 10%; animation-delay: 1.5s; }
    </style>
</head>
<body>
    <!-- Efecto de nieve -->
    <div id="snowflakes"></div>
    
    <!-- Decoraciones navideñas -->
    <div class="decoration decoration-1">🎁</div>
    <div class="decoration decoration-2">🎄</div>
    <div class="decoration decoration-3">⭐</div>
    <div class="decoration decoration-4">🎅</div>
    
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <h1 class="christmas-title">
                🎄 Amigos Secretos 🎄
            </h1>

            @if($urls->isEmpty())
                <div class="christmas-card text-center">
                    <p class="text-gray-600 text-lg">
                        No hay URLs generadas aún.
                    </p>
                </div>
            @else
                <div class="table-container">
                    <div class="space-y-4">
                        @foreach($urls as $url)
                            <div class="christmas-card">
                                <!-- URL oculta tipo papel -->
                                <div class="url-paper" onclick="toggleUrl({{ $url->id }})">
                                    <div class="url-toggle">
                                        <span class="url-toggle-text">📄 Ver URL</span>
                                        <span class="url-toggle-icon">▼</span>
                                    </div>
                                    <div class="url-hidden" id="url-{{ $url->id }}">
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mt-2">
                                            <a 
                                                href="{{ url('/secret-friend/' . $url->url) }}" 
                                                target="_blank"
                                                class="text-blue-600 hover:underline text-sm font-mono break-all"
                                            >
                                                {{ url('/secret-friend/' . $url->url) }}
                                            </a>
                                            <button 
                                                onclick="event.stopPropagation(); copyToClipboard('{{ url('/secret-friend/' . $url->url) }}')"
                                                class="christmas-btn"
                                            >
                                                📋 Copiar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Select para asignar jugador -->
                                <div>
                                    @php
                                        $selectedPlayerIds = \App\Models\Url::whereNotNull('friends')
                                            ->where('id', '!=', $url->id)
                                            ->pluck('friends')
                                            ->toArray();
                                        
                                        $availablePlayers = $allPlayers->filter(function($player) use ($url, $selectedPlayerIds) {
                                            return !in_array($player->id, $selectedPlayerIds) || $url->friends == $player->id;
                                        });
                                    @endphp
                                    
                                    <select 
                                        id="playerSelect_{{ $url->id }}" 
                                        class="christmas-select"
                                        onchange="updateUrlPlayer({{ $url->id }}, this.value)"
                                    >
                                        <option value="">-- Seleccionar Dueño de URL --</option>
                                        @foreach($availablePlayers as $player)
                                            @php
                                                $isSelected = $url->friends == $player->id;
                                            @endphp
                                            <option value="{{ $player->id }}" {{ $isSelected ? 'selected' : '' }}>
                                                {{ $player->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Crear efecto de nieve
        function createSnowflakes() {
            const snowflakesContainer = document.getElementById('snowflakes');
            const snowflakeSymbols = ['❄', '❅', '❆'];
            
            for (let i = 0; i < 30; i++) {
                const snowflake = document.createElement('div');
                snowflake.className = 'snowflake';
                snowflake.textContent = snowflakeSymbols[Math.floor(Math.random() * snowflakeSymbols.length)];
                snowflake.style.left = Math.random() * 100 + '%';
                snowflake.style.animationDuration = (Math.random() * 3 + 2) + 's';
                snowflake.style.animationDelay = Math.random() * 2 + 's';
                snowflake.style.opacity = Math.random() * 0.5 + 0.5;
                snowflakesContainer.appendChild(snowflake);
            }
        }
        
        // Toggle para mostrar/ocultar URL
        function toggleUrl(urlId) {
            const urlPaper = event.currentTarget;
            urlPaper.classList.toggle('expanded');
        }
        
        // Configurar CSRF token para todas las peticiones AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        async function updateUrlPlayer(urlId, playerId) {
            try {
                const response = await fetch(`/api/players/urls/${urlId}/player`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        player_id: playerId || null
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Actualizar todos los selects con la información de todas las URLs
                    updateAllSelects(data.data.urls);
                } else {
                    alert('Error: ' + (data.message || 'No se pudo actualizar el jugador'));
                    location.reload();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al actualizar el jugador. Por favor, intenta de nuevo.');
                location.reload();
            }
        }
        
        function updateAllSelects(urlsData) {
            urlsData.forEach(urlData => {
                const select = document.getElementById(`playerSelect_${urlData.id}`);
                if (select) {
                    const currentValue = urlData.friends ? urlData.friends.toString() : '';
                    updateSelectOptions(select, urlData.available_players, currentValue);
                }
            });
        }
        
        function updateSelectOptions(select, availablePlayers, currentValue) {
            const selectedValue = currentValue || select.value;
            
            while (select.options.length > 1) {
                select.remove(1);
            }
            
            availablePlayers.forEach(player => {
                const option = document.createElement('option');
                option.value = player.id;
                option.textContent = player.nombre;
                if (player.id.toString() === selectedValue) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        }
        
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                const btn = event.target;
                const originalText = btn.textContent;
                btn.textContent = '✅ ¡Copiado!';
                btn.style.background = 'linear-gradient(135deg, #28a745 0%, #20c997 100%)';
                setTimeout(() => {
                    btn.textContent = originalText;
                    btn.style.background = '';
                }, 2000);
            }).catch(err => {
                console.error('Error al copiar:', err);
                alert('Error al copiar la URL');
            });
        }
        
        // Inicializar nieve al cargar
        createSnowflakes();
    </script>
</body>
</html>
