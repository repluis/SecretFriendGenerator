@extends('layouts.christmas')

@section('title', 'Secret Friend Generator - Home')

@section('styles')
    <style>
        /* Numero navideno grande */
        .card-number {
            position: absolute;
            top: -20px;
            left: 20px;
            font-family: 'Mountains of Christmas', cursive;
            font-size: 4rem;
            font-weight: 700;
            color: #ffd700;
            text-shadow:
                3px 3px 0px #c41e3a,
                5px 5px 0px #c41e3a,
                0 0 30px rgba(255, 215, 0, 0.8),
                0 0 60px rgba(255, 215, 0, 0.4);
            line-height: 1;
            z-index: 10;
            transform: rotate(-5deg);
            animation: numberGlow 2s ease-in-out infinite alternate;
        }

        @keyframes numberGlow {
            0% {
                text-shadow:
                    3px 3px 0px #c41e3a,
                    5px 5px 0px #c41e3a,
                    0 0 30px rgba(255, 215, 0, 0.8),
                    0 0 60px rgba(255, 215, 0, 0.4);
            }
            100% {
                text-shadow:
                    3px 3px 0px #c41e3a,
                    5px 5px 0px #c41e3a,
                    0 0 40px rgba(255, 215, 0, 1),
                    0 0 80px rgba(255, 215, 0, 0.6);
            }
        }

        @media (max-width: 640px) {
            .card-number {
                font-size: 3rem;
                top: -15px;
                left: 15px;
            }
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

        .url-paper.blocked {
            background: #f5f5f5;
            border-color: #999;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .url-paper.blocked:hover {
            background: #f5f5f5;
            border-color: #999;
        }

        .url-paper.blocked .url-toggle-text {
            color: #999;
        }

        .url-paper.blocked .url-toggle-icon {
            color: #999;
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

        /* Select navideno */
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

        .christmas-select:disabled {
            background: #f5f5f5;
            border-color: #999;
            color: #999;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .christmas-select:disabled:hover {
            border-color: #999;
        }

        .christmas-btn {
            box-shadow: 0 3px 10px rgba(196, 30, 58, 0.3);
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

        /* Decoracion navidena adicional */
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

        /* Tarjeta invalida (roja) */
        .christmas-card.invalid {
            background: linear-gradient(135deg, #ffe6e6 0%, #ffcccc 100%);
            border: 3px solid #dc3545;
            box-shadow:
                0 10px 30px rgba(220, 53, 69, 0.3),
                inset 0 0 50px rgba(220, 53, 69, 0.1);
        }

        .christmas-card.invalid:hover {
            box-shadow:
                0 15px 40px rgba(220, 53, 69, 0.4),
                inset 0 0 60px rgba(220, 53, 69, 0.15);
        }

        .christmas-card.invalid::before {
            content: '⚠️';
        }

        .christmas-card.invalid::after {
            content: '❌';
        }

        /* Boton de validar */
        .validate-btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: 2px solid #ffd700;
            border-radius: 8px;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
            margin: 2rem auto;
            display: block;
        }

        .validate-btn:hover {
            background: linear-gradient(135deg, #20c997 0%, #28a745 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        }

        .validate-btn:active {
            transform: translateY(0);
        }

        .validate-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .validate-btn:disabled:hover {
            transform: none;
        }

        /* Boton de regenerar */
        .regenerate-btn {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border: 2px solid #ffd700;
            border-radius: 8px;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
            margin: 2rem auto;
            display: inline-block;
        }

        .regenerate-btn:hover {
            background: linear-gradient(135deg, #0056b3 0%, #007bff 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
        }

        .regenerate-btn:active {
            transform: translateY(0);
        }

        .regenerate-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .regenerate-btn:disabled:hover {
            transform: none;
        }

        /* Contenedor de botones */
        .buttons-container {
            display: flex;
            gap: 1rem;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            margin: 2rem 0;
        }
    </style>
@endsection

@section('content')
    <!-- Decoraciones navidenas -->
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
                        @foreach($urls as $index => $url)
                            <div class="christmas-card" id="card-{{ $url->id }}" data-url-id="{{ $url->id }}">
                                <!-- Numero navideno grande -->
                                <div class="card-number">{{ $loop->iteration }}</div>

                                <!-- URL oculta tipo papel -->
                                @if($gameStarted)
                                    @if($url->viewed)
                                        <div class="url-paper blocked">
                                            <div class="url-toggle">
                                                <span class="url-toggle-text">🔒 Esta URL ya fue vista</span>
                                                <span class="url-toggle-icon">🔒</span>
                                            </div>
                                        </div>
                                    @else
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
                                    @endif
                                @else
                                    <div class="url-paper blocked">
                                        <div class="url-toggle">
                                            <span class="url-toggle-text">🔒 El juego no ha iniciado</span>
                                            <span class="url-toggle-icon">🔒</span>
                                        </div>
                                    </div>
                                @endif

                                <!-- Select para asignar jugador -->
                                <div>
                                    @php
                                        $selectedPlayerIds = \App\Modules\SecretSanta\Infrastructure\Persistence\Models\UrlModel::whereNotNull('friends')
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
                                        {{ $gameStarted ? 'disabled' : '' }}
                                        title="{{ $gameStarted ? 'El juego ya ha iniciado, no se pueden modificar las asignaciones' : '' }}"
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
                                    @if($gameStarted)
                                        <p class="text-xs text-gray-500 mt-1 italic">🔒 El juego ha iniciado, no se pueden modificar las asignaciones</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Botones de validacion y regenerar -->
                <div class="text-center mt-6">
                    <div class="buttons-container">
                        <button
                            id="validateBtn"
                            class="validate-btn"
                            onclick="validateAssignments()"
                        >
                            ✅ Validar Asignaciones
                        </button>
                        <button
                            id="regenerateBtn"
                            class="regenerate-btn"
                            onclick="regenerateUrls()"
                            style="display: none;"
                        >
                            🔄 Regenerar URL
                        </button>
                    </div>
                    <div id="validationMessage" class="mt-4 text-center" style="display: none;"></div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Toggle para mostrar/ocultar URL
        function toggleUrl(urlId) {
            const urlPaper = event.currentTarget;
            // Verificar si esta bloqueado
            if (urlPaper.classList.contains('blocked')) {
                return;
            }
            urlPaper.classList.toggle('expanded');
        }

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
                    // Actualizar todos los selects con la informacion de todas las URLs
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

        // Funcion para validar asignaciones
        async function validateAssignments() {
            const validateBtn = document.getElementById('validateBtn');
            const validationMessage = document.getElementById('validationMessage');

            // Deshabilitar boton mientras se valida
            validateBtn.disabled = true;
            validateBtn.textContent = '⏳ Validando...';
            validationMessage.style.display = 'none';

            // Ocultar boton de regenerar mientras se valida
            const regenerateBtn = document.getElementById('regenerateBtn');
            if (regenerateBtn) {
                regenerateBtn.style.display = 'none';
            }

            // Remover todas las marcas de invalidacion previas
            document.querySelectorAll('.christmas-card').forEach(card => {
                card.classList.remove('invalid');
            });

            try {
                const response = await fetch('/api/players/validate-assignments', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                const data = await response.json();

                if (data.success) {
                    if (data.data.is_valid) {
                        // Todas las asignaciones son validas
                        validationMessage.innerHTML = `
                            <div style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
                                        border: 2px solid #28a745;
                                        border-radius: 8px;
                                        padding: 1rem;
                                        color: #155724;
                                        font-weight: 500;">
                                ✅ <strong>Todas las asignaciones son válidas</strong><br>
                                <small>Ningún jugador tiene a sí mismo como amigo secreto.</small>
                            </div>
                        `;
                        validationMessage.style.display = 'block';
                        // Ocultar boton de regenerar
                        document.getElementById('regenerateBtn').style.display = 'none';
                    } else {
                        // Hay asignaciones invalidas
                        const invalidCount = data.data.invalid_count;
                        validationMessage.innerHTML = `
                            <div style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
                                        border: 2px solid #dc3545;
                                        border-radius: 8px;
                                        padding: 1rem;
                                        color: #721c24;
                                        font-weight: 500;">
                                ❌ <strong>Se encontraron ${invalidCount} asignación(es) inválida(s)</strong><br>
                                <small>Las tarjetas marcadas en rojo tienen jugadores que se asignaron a sí mismos.</small>
                            </div>
                        `;
                        validationMessage.style.display = 'block';

                        // Mostrar boton de regenerar
                        document.getElementById('regenerateBtn').style.display = 'inline-block';

                        // Marcar las tarjetas invalidas en rojo
                        data.data.invalid_urls.forEach(invalidUrl => {
                            const card = document.getElementById(`card-${invalidUrl.id}`);
                            if (card) {
                                card.classList.add('invalid');
                                // Scroll suave a la primera tarjeta invalida
                                if (invalidUrl.id === data.data.invalid_urls[0].id) {
                                    card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                }
                            }
                        });
                    }
                } else {
                    validationMessage.innerHTML = `
                        <div style="background: #f8d7da;
                                    border: 2px solid #dc3545;
                                    border-radius: 8px;
                                    padding: 1rem;
                                    color: #721c24;">
                            ❌ Error: ${data.message || 'No se pudo validar las asignaciones'}
                        </div>
                    `;
                    validationMessage.style.display = 'block';
                }
            } catch (error) {
                console.error('Error:', error);
                validationMessage.innerHTML = `
                    <div style="background: #f8d7da;
                                border: 2px solid #dc3545;
                                border-radius: 8px;
                                padding: 1rem;
                                color: #721c24;">
                        ❌ Error al validar las asignaciones. Por favor, intenta de nuevo.
                    </div>
                `;
                validationMessage.style.display = 'block';
            } finally {
                // Restaurar boton
                validateBtn.disabled = false;
                validateBtn.textContent = '✅ Validar Asignaciones';
            }
        }

        // Funcion para regenerar URLs (misma logica que en configuracion)
        async function regenerateUrls() {
            if (!confirm('⚠️ ¿Estás seguro de que deseas regenerar las URLs?\n\nEsto:\n- Eliminará todas las URLs existentes\n- Generará nuevas URLs para todos los jugadores\n- Los IDs serán procesados en orden aleatorio\n- El estado del juego se pondrá en 0\n\n¿Deseas continuar?')) {
                return;
            }

            const regenerateBtn = document.getElementById('regenerateBtn');
            const validationMessage = document.getElementById('validationMessage');

            regenerateBtn.disabled = true;
            regenerateBtn.textContent = '⏳ Regenerando URLs...';
            validationMessage.style.display = 'none';

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
                            console.log('Estado del juego actualizado a 0');
                        }
                    } catch (gameError) {
                        console.error('Error al actualizar estado del juego:', gameError);
                    }

                    validationMessage.innerHTML = `
                        <div style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
                                    border: 2px solid #28a745;
                                    border-radius: 8px;
                                    padding: 1rem;
                                    color: #155724;
                                    font-weight: 500;">
                            ✅ <strong>URLs regeneradas exitosamente</strong><br>
                            <small>Se generaron ${data.data.total_urls} URL(s) para ${data.data.total_players} jugador(es). Los IDs fueron procesados en orden aleatorio. El estado del juego se ha puesto en 0.</small>
                        </div>
                    `;
                    validationMessage.style.display = 'block';

                    // Ocultar boton de regenerar
                    regenerateBtn.style.display = 'none';

                    // Recargar la pagina para actualizar las URLs
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    validationMessage.innerHTML = `
                        <div style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
                                    border: 2px solid #dc3545;
                                    border-radius: 8px;
                                    padding: 1rem;
                                    color: #721c24;
                                    font-weight: 500;">
                            ❌ <strong>Error</strong><br>
                            <small>${data.message || 'No se pudieron regenerar las URLs.'}</small>
                        </div>
                    `;
                    validationMessage.style.display = 'block';
                }
            } catch (error) {
                console.error('Error:', error);
                validationMessage.innerHTML = `
                    <div style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
                                border: 2px solid #dc3545;
                                border-radius: 8px;
                                padding: 1rem;
                                color: #721c24;
                                font-weight: 500;">
                        ❌ <strong>Error de conexión</strong><br>
                        <small>No se pudo conectar con el servidor.</small>
                    </div>
                `;
                validationMessage.style.display = 'block';
            } finally {
                regenerateBtn.disabled = false;
                regenerateBtn.textContent = '🔄 Regenerar URL';
            }
        }

        // Verificar estado de bloqueo de inputs desde localStorage
        function checkInputsLockStatus() {
            const inputsLocked = localStorage.getItem('inputsLocked');
            if (inputsLocked === 'true') {
                // Deshabilitar todos los selects
                document.querySelectorAll('.christmas-select').forEach(select => {
                    select.disabled = true;
                    select.title = 'Los inputs están bloqueados desde la página de configuración';
                });
            }
        }

        // Verificar estado de bloqueo al cargar
        checkInputsLockStatus();
    </script>
@endsection
