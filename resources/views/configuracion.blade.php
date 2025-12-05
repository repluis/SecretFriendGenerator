<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Secret Friend Generator - Configuración</title>
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
            padding: 2rem;
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
        
        /* Input y textarea navideños */
        .christmas-input,
        .christmas-textarea {
            background: white;
            border: 2px solid #c41e3a;
            border-radius: 8px;
            padding: 0.75rem;
            width: 100%;
            font-size: 1rem;
            color: #333;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .christmas-textarea {
            min-height: 200px;
            resize: vertical;
        }
        
        .christmas-input:focus,
        .christmas-textarea:focus {
            outline: none;
            border-color: #ffd700;
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.2);
        }
        
        .christmas-input:hover,
        .christmas-textarea:hover {
            border-color: #ffd700;
        }
        
        /* Botón navideño */
        .christmas-btn {
            background: linear-gradient(135deg, #c41e3a 0%, #8b1538 100%);
            color: white;
            border: 2px solid #ffd700;
            border-radius: 8px;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(196, 30, 58, 0.3);
            margin-top: 1rem;
        }
        
        .buttons-container {
            display: flex;
            gap: 1rem;
            width: 100%;
        }
        
        .buttons-container .christmas-btn {
            flex: 1;
        }
        
        @media (max-width: 640px) {
            .buttons-container {
                flex-direction: column;
            }
        }
        
        .christmas-btn:hover {
            background: linear-gradient(135deg, #8b1538 0%, #c41e3a 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(196, 30, 58, 0.4);
        }
        
        .christmas-btn:active {
            transform: translateY(0);
        }
        
        .christmas-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
            opacity: 0.6;
        }
        
        .christmas-btn:disabled:hover {
            transform: none;
        }
        
        .christmas-btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        
        .christmas-btn-success:hover {
            background: linear-gradient(135deg, #20c997 0%, #28a745 100%);
        }
        
        .christmas-btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }
        
        .christmas-btn-danger:hover {
            background: linear-gradient(135deg, #c82333 0%, #dc3545 100%);
        }
        
        .christmas-btn-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        }
        
        .christmas-btn-primary:hover {
            background: linear-gradient(135deg, #0056b3 0%, #007bff 100%);
        }
        
        .christmas-btn-warning {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
            color: #333;
        }
        
        .christmas-btn-warning:hover {
            background: linear-gradient(135deg, #e0a800 0%, #ffc107 100%);
        }
        
        .christmas-btn-info {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        }
        
        .christmas-btn-info:hover {
            background: linear-gradient(135deg, #138496 0%, #17a2b8 100%);
        }
        
        /* Grid de controles */
        .controls-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }
        
        .control-group {
            display: flex;
            flex-direction: column;
        }
        
        @media (max-width: 640px) {
            .controls-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Estilos para la tabla */
        .table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .table-container table {
            min-width: 100%;
        }
        
        .table-container tbody tr:hover {
            background: #f8f9fa !important;
            transition: background 0.2s ease;
        }
        
        @media (max-width: 768px) {
            .table-container table {
                font-size: 0.9rem;
            }
            
            .table-container th,
            .table-container td {
                padding: 0.5rem !important;
            }
        }
        
        /* Mensaje de resultado */
        .message {
            margin-top: 1.5rem;
            padding: 1rem;
            border-radius: 8px;
            font-weight: 500;
            display: none;
        }
        
        .message.success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border: 2px solid #28a745;
            color: #155724;
            display: block;
        }
        
        .message.error {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            border: 2px solid #dc3545;
            color: #721c24;
            display: block;
        }
        
        /* Label navideño */
        .christmas-label {
            display: block;
            color: #c41e3a;
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }
        
        /* Instrucciones */
        .instructions {
            background: rgba(255, 255, 255, 0.1);
            border: 2px dashed #ffd700;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            color: white;
        }
        
        .instructions h3 {
            color: #ffd700;
            margin-bottom: 0.5rem;
            font-size: 1.2rem;
        }
        
        .instructions ul {
            margin: 0.5rem 0;
            padding-left: 1.5rem;
        }
        
        .instructions li {
            margin: 0.25rem 0;
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
        
        /* Link de navegación */
        .nav-link {
            display: inline-block;
            margin-bottom: 1.5rem;
            color: #ffd700;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            color: white;
            text-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
        }
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
        <div class="max-w-4xl mx-auto">
            <h1 class="christmas-title">
                ⚙️ Configuración de Jugadores ⚙️
            </h1>

            <!-- Tabla de jugadores -->
            <div class="christmas-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h2 style="font-size: 1.5rem; color: #c41e3a; font-weight: 600; margin: 0;">
                        👥 Lista de Jugadores
                    </h2>
                    <button 
                        id="addPlayerBtn" 
                        class="christmas-btn christmas-btn-success"
                        onclick="showAddPlayerForm()"
                        style="padding: 0.75rem 1.5rem; width: auto; margin: 0;"
                    >
                        ➕ Agregar Jugador
                    </button>
                </div>

                <!-- Formulario para agregar jugador (oculto inicialmente) -->
                <div id="addPlayerForm" style="display: none; margin-bottom: 1.5rem; padding: 1rem; background: rgba(255, 255, 255, 0.5); border-radius: 8px; border: 2px dashed #c41e3a;">
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <input 
                            type="text" 
                            id="newPlayerName" 
                            class="christmas-input" 
                            placeholder="Nombre del jugador"
                            style="flex: 1;"
                            onkeypress="if(event.key === 'Enter') addSinglePlayer()"
                        >
                        <button 
                            onclick="addSinglePlayer()" 
                            class="christmas-btn christmas-btn-success"
                            style="padding: 0.75rem 1.5rem; width: auto; margin: 0;"
                        >
                            ✅ Agregar
                        </button>
                        <button 
                            onclick="hideAddPlayerForm()" 
                            class="christmas-btn christmas-btn-danger"
                            style="padding: 0.75rem 1.5rem; width: auto; margin: 0;"
                        >
                            ❌ Cancelar
                        </button>
                    </div>
                </div>

                <!-- Tabla de jugadores -->
                <div class="table-container">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: linear-gradient(135deg, #c41e3a 0%, #8b1538 100%); color: white;">
                                <th style="padding: 1rem; text-align: left; border-radius: 8px 0 0 0;">#</th>
                                <th style="padding: 1rem; text-align: left;">Nombre</th>
                                <th style="padding: 1rem; text-align: left;">Estado</th>
                                <th style="padding: 1rem; text-align: center; border-radius: 0 8px 0 0;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="playersTableBody">
                            @forelse($players as $index => $player)
                                <tr data-player-id="{{ $player->id }}" style="background: white; border-bottom: 1px solid #e0e0e0;">
                                    <td style="padding: 0.75rem 1rem;">{{ $loop->iteration }}</td>
                                    <td style="padding: 0.75rem 1rem; font-weight: 500;">{{ $player->nombre }}</td>
                                    <td style="padding: 0.75rem 1rem;">
                                        <span style="background: #28a745; color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.85rem;">
                                            ✅ Activo
                                        </span>
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: center;">
                                        <button 
                                            onclick="deletePlayer({{ $player->id }}, '{{ $player->nombre }}')" 
                                            class="christmas-btn christmas-btn-danger"
                                            style="padding: 0.5rem 1rem; font-size: 0.85rem; width: auto; margin: 0;"
                                        >
                                            🗑️ Eliminar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="padding: 2rem; text-align: center; color: #666;">
                                        No hay jugadores registrados. Agrega jugadores usando el botón de arriba o el formulario masivo.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Botones para generar URLs, eliminar jugadores e iniciar/detener juego -->
                <div style="margin-top: 1.5rem; display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                    <button 
                        id="generateUrlsBtn" 
                        class="christmas-btn christmas-btn-primary"
                        onclick="confirmGenerateUrls()"
                        style="padding: 1rem 2rem; width: auto; margin: 0;"
                    >
                        🔗 Generar URL con ID de Player
                    </button>
                    
                    <button 
                        id="deleteAllPlayersBtn" 
                        class="christmas-btn christmas-btn-danger"
                        onclick="confirmDeleteAllPlayers()"
                        style="padding: 1rem 2rem; width: auto; margin: 0;"
                    >
                        🗑️ Eliminar Todos los Jugadores
                    </button>
                    
                    <button 
                        id="toggleGameBtn" 
                        class="christmas-btn christmas-btn-primary"
                        onclick="toggleGame()"
                        style="padding: 1rem 2rem; width: auto; margin: 0;"
                    >
                        <span id="toggleGameText">🔄 Cargando...</span>
                    </button>
                </div>
            </div>

            <!-- Tabla de URLs -->
            <div class="christmas-card">
                <h2 style="font-size: 1.5rem; color: #c41e3a; font-weight: 600; margin-bottom: 1.5rem;">
                    🔗 Lista de URLs
                </h2>

                <div class="table-container">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: linear-gradient(135deg, #c41e3a 0%, #8b1538 100%); color: white;">
                                <th style="padding: 1rem; text-align: left; border-radius: 8px 0 0 0;">#</th>
                                <th style="padding: 1rem; text-align: left;">Nombre (Friends)</th>
                                <th style="padding: 1rem; text-align: left;">URL</th>
                                <th style="padding: 1rem; text-align: center;">Vista</th>
                                <th style="padding: 1rem; text-align: center; border-radius: 0 8px 0 0;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="urlsTableBody">
                            @forelse($urls as $index => $url)
                                <tr data-url-id="{{ $url->id }}" style="background: white; border-bottom: 1px solid #e0e0e0;">
                                    <td style="padding: 0.75rem 1rem;">{{ $loop->iteration }}</td>
                                    <td style="padding: 0.75rem 1rem; font-weight: 500;">
                                        {{ $url->friendPlayer ? $url->friendPlayer->nombre : 'Sin asignar' }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem;">
                                        <a 
                                            href="{{ url('/secret-friend/' . $url->url) }}" 
                                            target="_blank"
                                            style="color: #007bff; text-decoration: none; font-weight: 500;"
                                            onmouseover="this.style.textDecoration='underline'"
                                            onmouseout="this.style.textDecoration='none'"
                                        >
                                            URL
                                        </a>
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: center;">
                                        <span id="viewedStatus_{{ $url->id }}">
                                            @if($url->viewed)
                                                <span style="background: #dc3545; color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.85rem;">
                                                    ❌ Vista
                                                </span>
                                            @else
                                                <span style="background: #28a745; color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.85rem;">
                                                    ✅ No Vista
                                                </span>
                                            @endif
                                        </span>
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: center;">
                                        <button 
                                            onclick="resetUrlView({{ $url->id }})" 
                                            class="christmas-btn christmas-btn-info"
                                            style="padding: 0.5rem 1rem; font-size: 0.85rem; width: auto; margin: 0;"
                                        >
                                            🔄 Restablecer Vista
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="padding: 2rem; text-align: center; color: #666;">
                                        No hay URLs generadas. Genera URLs usando el botón de arriba.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Div para mensajes -->
            <div id="message" class="message" style="display: none;"></div>

            <!-- Sección de controles del juego -->
            <div class="christmas-card">
                <h2 class="christmas-title" style="font-size: 1.8rem; margin-bottom: 1.5rem;">
                    🎮 Controles del Juego
                </h2>

                <div class="controls-grid">
                    <!-- Control de bloqueo de inputs -->
                    <div class="control-group">
                        <label class="christmas-label">🔒 Bloquear/Desbloquear Inputs</label>
                        <button 
                            id="toggleInputsBtn" 
                            class="christmas-btn christmas-btn-warning"
                            onclick="toggleInputs()"
                        >
                            <span id="toggleInputsText">🔒 Bloquear Inputs</span>
                        </button>
                    </div>

                    <!-- Habilitar vista a todos -->
                    <div class="control-group">
                        <label class="christmas-label">👥 Habilitar Vista a Todos</label>
                        <button 
                            id="enableViewAllBtn" 
                            class="christmas-btn christmas-btn-info"
                            onclick="enableViewForAll()"
                        >
                            ✅ Habilitar Vista a Todos
                        </button>
                    </div>
                </div>
            </div>
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
        
        // Configurar CSRF token para todas las peticiones AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Cargar estado del juego al iniciar
        let gameStatus = 0;
        let inputsLocked = false;
        
        async function loadGameStatus() {
            try {
                const response = await fetch('/api/game-config/', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                if (data.success) {
                    gameStatus = data.data.startgame;
                    updateToggleGameButton();
                }
            } catch (error) {
                console.error('Error al cargar estado del juego:', error);
                // Si hay error, establecer estado por defecto
                gameStatus = 0;
                updateToggleGameButton();
            }
        }
        
        function updateToggleGameButton() {
            const toggleGameBtn = document.getElementById('toggleGameBtn');
            const toggleGameText = document.getElementById('toggleGameText');
            
            if (!toggleGameBtn || !toggleGameText) {
                return; // Los elementos no existen aún
            }
            
            if (gameStatus === 1) {
                toggleGameText.textContent = '⏸️ Detener Juego';
                toggleGameBtn.className = 'christmas-btn christmas-btn-danger';
            } else {
                toggleGameText.textContent = '▶️ Iniciar Juego';
                toggleGameBtn.className = 'christmas-btn christmas-btn-success';
            }
        }
        
        // Función para iniciar/detener el juego
        async function toggleGame() {
            const toggleGameBtn = document.getElementById('toggleGameBtn');
            const toggleGameText = document.getElementById('toggleGameText');
            const messageDiv = document.getElementById('message');
            
            if (!toggleGameBtn || !toggleGameText || !messageDiv) {
                console.error('Elementos no encontrados');
                return;
            }
            
            const newStatus = gameStatus === 1 ? 0 : 1;
            
            toggleGameBtn.disabled = true;
            toggleGameText.textContent = '⏳ Procesando...';
            
            try {
                const response = await fetch('/api/game-config/', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        startgame: newStatus
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    gameStatus = newStatus;
                    updateToggleGameButton();
                    messageDiv.className = 'message success';
                    messageDiv.innerHTML = `
                        ✅ <strong>${newStatus === 1 ? 'Juego iniciado' : 'Juego detenido'}</strong><br>
                        <small>El estado del juego ha sido actualizado correctamente.</small>
                    `;
                } else {
                    messageDiv.className = 'message error';
                    messageDiv.innerHTML = `
                        ❌ <strong>Error</strong><br>
                        <small>${data.message || 'No se pudo actualizar el estado del juego.'}</small>
                    `;
                }
            } catch (error) {
                console.error('Error:', error);
                messageDiv.className = 'message error';
                messageDiv.innerHTML = `
                    ❌ <strong>Error de conexión</strong><br>
                    <small>No se pudo conectar con el servidor. Verifica que el servidor esté ejecutándose.</small>
                `;
            } finally {
                if (toggleGameBtn) {
                    toggleGameBtn.disabled = false;
                }
                if (toggleGameText) {
                    updateToggleGameButton();
                }
            }
        }
        
        // Función para bloquear/desbloquear inputs
        function toggleInputs() {
            inputsLocked = !inputsLocked;
            const messageDiv = document.getElementById('message');
            
            // Actualizar el botón visualmente
            updateToggleInputsButton();
            
            if (inputsLocked) {
                messageDiv.className = 'message success';
                messageDiv.innerHTML = `
                    ✅ <strong>Inputs bloqueados</strong><br>
                    <small>Los inputs en la página principal están ahora bloqueados.</small>
                `;
            } else {
                messageDiv.className = 'message success';
                messageDiv.innerHTML = `
                    ✅ <strong>Inputs desbloqueados</strong><br>
                    <small>Los inputs en la página principal están ahora desbloqueados.</small>
                `;
            }
            
            // Guardar el estado en localStorage para que persista
            localStorage.setItem('inputsLocked', inputsLocked);
        }
        
        // Función para habilitar vista a todos los jugadores
        async function enableViewForAll() {
            if (!confirm('¿Estás seguro de que deseas habilitar la vista para todos los jugadores?')) {
                return;
            }
            
            const enableViewAllBtn = document.getElementById('enableViewAllBtn');
            const messageDiv = document.getElementById('message');
            
            enableViewAllBtn.disabled = true;
            enableViewAllBtn.textContent = '⏳ Procesando...';
            
            try {
                const response = await fetch('/api/players/enable-view-all', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    messageDiv.className = 'message success';
                    messageDiv.innerHTML = `
                        ✅ <strong>Vista habilitada para todos</strong><br>
                        <small>Se habilitó la vista para ${data.data.updated} jugador(es) de un total de ${data.data.total_urls} URL(s).</small>
                    `;
                } else {
                    messageDiv.className = 'message error';
                    messageDiv.innerHTML = `
                        ❌ <strong>Error</strong><br>
                        <small>${data.message || 'No se pudo habilitar la vista para todos los jugadores.'}</small>
                    `;
                }
            } catch (error) {
                console.error('Error:', error);
                messageDiv.className = 'message error';
                messageDiv.innerHTML = `
                    ❌ <strong>Error de conexión</strong><br>
                    <small>No se pudo conectar con el servidor.</small>
                `;
            } finally {
                enableViewAllBtn.disabled = false;
                enableViewAllBtn.textContent = '✅ Habilitar Vista a Todos';
            }
        }
        
        // Funciones para agregar jugador individual
        function showAddPlayerForm() {
            document.getElementById('addPlayerForm').style.display = 'block';
            document.getElementById('newPlayerName').focus();
        }
        
        function hideAddPlayerForm() {
            document.getElementById('addPlayerForm').style.display = 'none';
            document.getElementById('newPlayerName').value = '';
        }
        
        async function addSinglePlayer() {
            const playerNameInput = document.getElementById('newPlayerName');
            const playerName = playerNameInput.value.trim();
            const messageDiv = document.getElementById('message');
            
            if (!playerName || playerName.length === 0) {
                messageDiv.className = 'message error';
                messageDiv.textContent = '❌ Por favor, ingresa un nombre válido';
                return;
            }
            
            try {
                const response = await fetch('/api/players/', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        nombre: playerName
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Agregar el jugador a la tabla
                    addPlayerToTable(data.data);
                    
                    // Limpiar el input y ocultar el formulario
                    playerNameInput.value = '';
                    hideAddPlayerForm();
                    
                    // Mostrar mensaje de éxito
                    messageDiv.className = 'message success';
                    messageDiv.innerHTML = `
                        ✅ <strong>Jugador agregado</strong><br>
                        <small>El jugador "${data.data.nombre}" ha sido agregado exitosamente.</small>
                    `;
                } else {
                    messageDiv.className = 'message error';
                    messageDiv.innerHTML = `
                        ❌ <strong>Error</strong><br>
                        <small>${data.message || 'No se pudo agregar el jugador.'}</small>
                    `;
                }
            } catch (error) {
                console.error('Error:', error);
                messageDiv.className = 'message error';
                messageDiv.innerHTML = `
                    ❌ <strong>Error de conexión</strong><br>
                    <small>No se pudo conectar con el servidor.</small>
                `;
            }
        }
        
        function addPlayerToTable(player) {
            const tbody = document.getElementById('playersTableBody');
            
            // Si la tabla está vacía, eliminar el mensaje de "no hay jugadores"
            if (tbody.querySelector('td[colspan]')) {
                tbody.innerHTML = '';
            }
            
            // Contar filas existentes para el número
            const rowCount = tbody.querySelectorAll('tr').length + 1;
            
            // Crear nueva fila
            const newRow = document.createElement('tr');
            newRow.setAttribute('data-player-id', player.id);
            newRow.style.background = 'white';
            newRow.style.borderBottom = '1px solid #e0e0e0';
            newRow.innerHTML = `
                <td style="padding: 0.75rem 1rem;">${rowCount}</td>
                <td style="padding: 0.75rem 1rem; font-weight: 500;">${player.nombre}</td>
                <td style="padding: 0.75rem 1rem;">
                    <span style="background: #28a745; color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.85rem;">
                        ✅ Activo
                    </span>
                </td>
                <td style="padding: 0.75rem 1rem; text-align: center;">
                    <button 
                        onclick="deletePlayer(${player.id}, '${player.nombre.replace(/'/g, "\\'")}')" 
                        class="christmas-btn christmas-btn-danger"
                        style="padding: 0.5rem 1rem; font-size: 0.85rem; width: auto; margin: 0;"
                    >
                        🗑️ Eliminar
                    </button>
                </td>
            `;
            
            // Agregar con animación
            newRow.style.opacity = '0';
            tbody.appendChild(newRow);
            
            // Animación de entrada
            setTimeout(() => {
                newRow.style.transition = 'opacity 0.3s ease';
                newRow.style.opacity = '1';
            }, 10);
        }
        
        async function deletePlayer(playerId, playerName) {
            if (!confirm(`¿Estás seguro de que deseas eliminar al jugador "${playerName}"?`)) {
                return;
            }
            
            const messageDiv = document.getElementById('message');
            
            try {
                const response = await fetch('/api/players/by-name', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        nombre: playerName
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Eliminar la fila de la tabla
                    const row = document.querySelector(`tr[data-player-id="${playerId}"]`);
                    if (row) {
                        row.style.transition = 'opacity 0.3s ease';
                        row.style.opacity = '0';
                        setTimeout(() => {
                            row.remove();
                            
                            // Renumerar las filas
                            const rows = document.querySelectorAll('#playersTableBody tr');
                            rows.forEach((r, index) => {
                                r.querySelector('td:first-child').textContent = index + 1;
                            });
                            
                            // Si no hay más jugadores, mostrar mensaje
                            if (rows.length === 0) {
                                const tbody = document.getElementById('playersTableBody');
                                tbody.innerHTML = `
                                    <tr>
                                        <td colspan="4" style="padding: 2rem; text-align: center; color: #666;">
                                            No hay jugadores registrados. Agrega jugadores usando el botón de arriba o el formulario masivo.
                                        </td>
                                    </tr>
                                `;
                            }
                        }, 300);
                    }
                    
                    messageDiv.className = 'message success';
                    messageDiv.innerHTML = `
                        ✅ <strong>Jugador eliminado</strong><br>
                        <small>El jugador "${playerName}" ha sido eliminado exitosamente.</small>
                    `;
                } else {
                    messageDiv.className = 'message error';
                    messageDiv.innerHTML = `
                        ❌ <strong>Error</strong><br>
                        <small>${data.message || 'No se pudo eliminar el jugador.'}</small>
                    `;
                }
            } catch (error) {
                console.error('Error:', error);
                messageDiv.className = 'message error';
                messageDiv.innerHTML = `
                    ❌ <strong>Error de conexión</strong><br>
                    <small>No se pudo conectar con el servidor.</small>
                `;
            }
        }
        
        // Función de confirmación para generar URLs
        function confirmGenerateUrls() {
            const playerRows = document.querySelectorAll('#playersTableBody tr[data-player-id]');
            const playerCount = playerRows.length;
            
            if (playerCount < 2) {
                const messageDiv = document.getElementById('message');
                messageDiv.className = 'message error';
                messageDiv.textContent = '❌ Se necesitan al menos 2 jugadores para generar URLs';
                return;
            }
            
            if (!confirm(`⚠️ ¿Estás seguro de que deseas generar URLs?\n\nEsto:\n- Eliminará todas las URLs existentes\n- Generará nuevas URLs para ${playerCount} jugador(es)\n- Los IDs serán procesados en orden aleatorio\n\n¿Deseas continuar?`)) {
                return;
            }
            
            generateUrlsWithPlayerIds();
        }
        
        // Función para generar URLs con IDs de jugadores mezclados aleatoriamente
        async function generateUrlsWithPlayerIds() {
            const generateUrlsBtn = document.getElementById('generateUrlsBtn');
            const messageDiv = document.getElementById('message');
            
            // Obtener todos los IDs de jugadores de la tabla
            const playerRows = document.querySelectorAll('#playersTableBody tr[data-player-id]');
            const playerIds = Array.from(playerRows).map(row => {
                return parseInt(row.getAttribute('data-player-id'));
            });
            
            if (playerIds.length < 2) {
                messageDiv.className = 'message error';
                messageDiv.textContent = '❌ Se necesitan al menos 2 jugadores para generar URLs';
                return;
            }
            
            // Mezclar los IDs aleatoriamente
            const shuffledIds = [...playerIds];
            for (let i = shuffledIds.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [shuffledIds[i], shuffledIds[j]] = [shuffledIds[j], shuffledIds[i]];
            }
            
            generateUrlsBtn.disabled = true;
            generateUrlsBtn.textContent = '⏳ Generando URLs...';
            messageDiv.className = 'message';
            messageDiv.textContent = '';
            
            try {
                const response = await fetch('/api/players/sync-urls-and-assign-names', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Cambiar el estado del juego a 0
                    try {
                        const gameResponse = await fetch('/api/game-config/', {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                startgame: 0
                            })
                        });
                        
                        const gameData = await gameResponse.json();
                        if (gameData.success) {
                            gameStatus = 0;
                            updateToggleGameButton();
                        }
                    } catch (gameError) {
                        console.error('Error al actualizar estado del juego:', gameError);
                    }
                    
                    messageDiv.className = 'message success';
                    messageDiv.innerHTML = `
                        ✅ <strong>URLs generadas exitosamente</strong><br>
                        <small>Se generaron ${data.data.total_urls} URL(s) para ${data.data.total_players} jugador(es). Los IDs fueron procesados en orden aleatorio. El estado del juego se ha puesto en 0.</small>
                    `;
                    
                    // Recargar la página para actualizar las tablas
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    messageDiv.className = 'message error';
                    messageDiv.innerHTML = `
                        ❌ <strong>Error</strong><br>
                        <small>${data.message || 'No se pudieron generar las URLs.'}</small>
                    `;
                }
            } catch (error) {
                console.error('Error:', error);
                messageDiv.className = 'message error';
                messageDiv.innerHTML = `
                    ❌ <strong>Error de conexión</strong><br>
                    <small>No se pudo conectar con el servidor.</small>
                `;
            } finally {
                generateUrlsBtn.disabled = false;
                generateUrlsBtn.textContent = '🔗 Generar URL con ID de Player';
            }
        }
        
        // Función de confirmación para eliminar todos los jugadores
        function confirmDeleteAllPlayers() {
            const playerRows = document.querySelectorAll('#playersTableBody tr[data-player-id]');
            const playerCount = playerRows.length;
            
            if (playerCount === 0) {
                const messageDiv = document.getElementById('message');
                messageDiv.className = 'message error';
                messageDiv.textContent = '❌ No hay jugadores para eliminar';
                return;
            }
            
            if (!confirm(`⚠️ ¿Estás seguro de que deseas eliminar TODOS los jugadores?\n\nEsto eliminará:\n- ${playerCount} jugador(es)\n\nEsta acción NO se puede deshacer.\n\n¿Deseas continuar?`)) {
                return;
            }
            
            deleteAllPlayers();
        }
        
        // Función para eliminar todos los jugadores
        async function deleteAllPlayers() {
            const deleteAllPlayersBtn = document.getElementById('deleteAllPlayersBtn');
            const messageDiv = document.getElementById('message');
            
            deleteAllPlayersBtn.disabled = true;
            deleteAllPlayersBtn.textContent = '⏳ Eliminando...';
            messageDiv.className = 'message';
            messageDiv.textContent = '';
            
            try {
                const response = await fetch('/api/players/all', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    messageDiv.className = 'message success';
                    messageDiv.innerHTML = `
                        ✅ <strong>Jugadores eliminados exitosamente</strong><br>
                        <small>Se eliminaron todos los jugadores.</small>
                    `;
                    
                    // Limpiar la tabla de jugadores
                    const tbody = document.getElementById('playersTableBody');
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="4" style="padding: 2rem; text-align: center; color: #666;">
                                No hay jugadores registrados. Agrega jugadores usando el botón de arriba.
                            </td>
                        </tr>
                    `;
                } else {
                    messageDiv.className = 'message error';
                    messageDiv.innerHTML = `
                        ❌ <strong>Error</strong><br>
                        <small>${data.message || 'No se pudieron eliminar los jugadores.'}</small>
                    `;
                }
            } catch (error) {
                console.error('Error:', error);
                messageDiv.className = 'message error';
                messageDiv.innerHTML = `
                    ❌ <strong>Error de conexión</strong><br>
                    <small>No se pudo conectar con el servidor.</small>
                `;
            } finally {
                deleteAllPlayersBtn.disabled = false;
                deleteAllPlayersBtn.textContent = '🗑️ Eliminar Todos los Jugadores';
            }
        }
        
        // Función para restablecer la vista de una URL
        async function resetUrlView(urlId) {
            const messageDiv = document.getElementById('message');
            const statusSpan = document.getElementById(`viewedStatus_${urlId}`);
            
            if (!statusSpan) {
                console.error('Elemento no encontrado');
                return;
            }
            
            try {
                const response = await fetch(`/api/players/urls/${urlId}/reset-view`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Actualizar el estado en la tabla
                    statusSpan.innerHTML = `
                        <span style="background: #28a745; color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.85rem;">
                            ✅ No Vista
                        </span>
                    `;
                    
                    messageDiv.className = 'message success';
                    messageDiv.innerHTML = `
                        ✅ <strong>Vista restablecida</strong><br>
                        <small>La vista de la URL ha sido restablecida exitosamente.</small>
                    `;
                } else {
                    messageDiv.className = 'message error';
                    messageDiv.innerHTML = `
                        ❌ <strong>Error</strong><br>
                        <small>${data.message || 'No se pudo restablecer la vista de la URL.'}</small>
                    `;
                }
            } catch (error) {
                console.error('Error:', error);
                messageDiv.className = 'message error';
                messageDiv.innerHTML = `
                    ❌ <strong>Error de conexión</strong><br>
                    <small>No se pudo conectar con el servidor.</small>
                `;
            }
        }
        
        // Cargar estado inicial
        loadGameStatus();
        
        // Función para actualizar el estado visual del botón de inputs
        function updateToggleInputsButton() {
            const toggleInputsText = document.getElementById('toggleInputsText');
            const toggleInputsBtn = document.getElementById('toggleInputsBtn');
            
            if (!toggleInputsText || !toggleInputsBtn) {
                return;
            }
            
            if (inputsLocked) {
                toggleInputsText.textContent = '🔓 Desbloquear Inputs';
                toggleInputsBtn.className = 'christmas-btn christmas-btn-success';
            } else {
                toggleInputsText.textContent = '🔒 Bloquear Inputs';
                toggleInputsBtn.className = 'christmas-btn christmas-btn-warning';
            }
        }
        
        // Cargar estado de bloqueo de inputs desde localStorage
        const savedInputsLocked = localStorage.getItem('inputsLocked');
        if (savedInputsLocked === 'true') {
            inputsLocked = true;
        }
        updateToggleInputsButton();
        
        // Inicializar nieve al cargar
        createSnowflakes();
    </script>
</body>
</html>
