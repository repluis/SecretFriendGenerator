@extends('layouts.festive')

@section('title', $appName . ' - Amigos Secretos')

@section('styles')
<style>
    /* Contenedor principal centrado y simétrico */
    .game-container {
        max-width: 900px;
        margin: 0 auto;
        padding: var(--spacing-2xl) var(--spacing-lg);
    }

    /* Título navideño */
    .game-header {
        text-align: center;
        margin-bottom: var(--spacing-2xl);
    }

    .game-header-actions {
        margin-top: var(--spacing-lg);
        display: flex;
        justify-content: center;
    }

    /* Tarjeta de jugador */
    .player-card {
        position: relative;
        margin-bottom: var(--spacing-xl);
    }

    .player-card-number {
        position: absolute;
        top: -20px;
        left: 20px;
        font-family: 'Mountains of Christmas', cursive;
        font-size: 3.5rem;
        font-weight: 700;
        color: #ffd700;
        text-shadow:
            3px 3px 0px #c41e3a,
            5px 5px 0px #c41e3a,
            0 0 30px rgba(255, 215, 0, 0.8);
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
                0 0 30px rgba(255, 215, 0, 0.8);
        }
        100% {
            text-shadow:
                3px 3px 0px #c41e3a,
                5px 5px 0px #c41e3a,
                0 0 40px rgba(255, 215, 0, 1);
        }
    }

    /* Decoraciones de tarjeta */
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

    /* Tarjeta inválida */
    .christmas-card.invalid {
        background: linear-gradient(135deg, #ffe6e6 0%, #ffcccc 100%);
        border-color: #dc3545;
        box-shadow: 0 10px 30px rgba(220, 53, 69, 0.3);
    }

    .christmas-card.invalid::before {
        content: '⚠️';
    }

    .christmas-card.invalid::after {
        content: '❌';
    }

    /* URL Paper */
    .url-paper {
        background: #fff;
        border: 2px dashed #c41e3a;
        border-radius: var(--radius-md);
        padding: var(--spacing-md);
        margin-bottom: var(--spacing-md);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .url-paper:hover:not(.blocked) {
        background: #fff9e6;
        border-color: #ffd700;
    }

    .url-paper.blocked {
        background: #f5f5f5;
        border-color: #999;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .url-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
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

    .url-hidden {
        display: none;
        word-break: break-all;
        font-family: 'Courier New', monospace;
        font-size: 0.85rem;
        color: #333;
        padding-top: var(--spacing-sm);
    }

    .url-paper.expanded .url-hidden {
        display: block;
    }

    .url-actions {
        display: flex;
        gap: var(--spacing-sm);
        align-items: center;
        margin-top: var(--spacing-sm);
        flex-wrap: wrap;
    }

    .url-link {
        color: var(--color-primary-600);
        text-decoration: none;
        font-size: 0.875rem;
        flex: 1;
        min-width: 200px;
    }

    .url-link:hover {
        text-decoration: underline;
    }

    /* Select navideño */
    .christmas-select {
        width: 100%;
        padding: var(--spacing-md);
        background: white;
        border: 2px solid #c41e3a;
        border-radius: var(--radius-md);
        font-size: 1rem;
        color: #333;
        cursor: pointer;
        transition: all 0.3s ease;
        font-family: var(--font-sans);
    }

    .christmas-select:focus {
        outline: none;
        border-color: #ffd700;
        box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.2);
    }

    .christmas-select:hover:not(:disabled) {
        border-color: #ffd700;
    }

    .christmas-select:disabled {
        background: #f5f5f5;
        border-color: #999;
        color: #999;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .select-hint {
        font-size: 0.75rem;
        color: #64748b;
        margin-top: var(--spacing-xs);
        font-style: italic;
    }

    /* Botones de acción */
    .action-buttons {
        display: flex;
        gap: var(--spacing-md);
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
        margin: var(--spacing-2xl) 0;
    }

    .validate-btn,
    .regenerate-btn {
        padding: var(--spacing-md) var(--spacing-xl);
        border-radius: var(--radius-md);
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid #ffd700;
        font-family: var(--font-sans);
    }

    .validate-btn {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    }

    .validate-btn:hover:not(:disabled) {
        background: linear-gradient(135deg, #20c997 0%, #28a745 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
    }

    .regenerate-btn {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
    }

    .regenerate-btn:hover:not(:disabled) {
        background: linear-gradient(135deg, #0056b3 0%, #007bff 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
    }

    .validate-btn:disabled,
    .regenerate-btn:disabled {
        background: #6c757d;
        cursor: not-allowed;
        opacity: 0.6;
        transform: none;
    }

    /* Mensaje de validación */
    .validation-message {
        margin-top: var(--spacing-lg);
        text-align: center;
    }

    /* Responsive */
    @media (max-width: 640px) {
        .player-card-number {
            font-size: 2.5rem;
            top: -15px;
            left: 15px;
        }

        .url-actions {
            flex-direction: column;
        }

        .url-link {
            min-width: 100%;
        }
    }
</style>
@endsection

@section('content')
<div class="game-container">
    <!-- Header -->
    <div class="game-header">
        <h1 class="christmas-title">🎄 Amigos Secretos 🎄</h1>
        <div class="game-header-actions">
            <a href="{{ route('configuracion') }}" class="christmas-btn">
                ⚙️ Configuración del Juego
            </a>
        </div>
    </div>

    @if($urls->isEmpty())
        <!-- Estado vacío -->
        <div class="christmas-card">
            <div class="christmas-empty">
                <p>No hay URLs generadas aún.</p>
                <p style="font-size: 0.875rem; color: var(--color-slate-500); margin-top: var(--spacing-sm);">
                    Ve a Configuración para generar las URLs de los jugadores.
                </p>
            </div>
        </div>
    @else
        <!-- Lista de jugadores -->
        <div class="players-list">
            @foreach($urls as $url)
                <div class="christmas-card player-card" id="card-{{ $url->id }}" data-url-id="{{ $url->id }}">
                    <!-- Número del jugador -->
                    <div class="player-card-number">{{ $loop->iteration }}</div>

                    <!-- URL Paper -->
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
                                    <div class="url-actions">
                                        <a
                                            href="{{ url('/secret-friend/' . $url->url) }}"
                                            target="_blank"
                                            class="url-link"
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

                    <!-- Selector de jugador -->
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
                            title="{{ $gameStarted ? 'El juego ya ha iniciado, no se pueden modificar las asignaciones' : 'Selecciona el dueño de esta URL' }}"
                        >
                            <option value="">-- Seleccionar Dueño de URL --</option>
                            @foreach($availablePlayers as $player)
                                <option value="{{ $player->id }}" {{ $url->friends == $player->id ? 'selected' : '' }}>
                                    {{ $player->nombre }}
                                </option>
                            @endforeach
                        </select>
                        
                        @if($gameStarted)
                            <p class="select-hint">🔒 El juego ha iniciado, no se pueden modificar las asignaciones</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Botones de acción -->
        <div class="action-buttons">
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
                🔄 Regenerar URLs
            </button>
        </div>

        <!-- Mensaje de validación -->
        <div id="validationMessage" class="validation-message" style="display: none;"></div>
    @endif
</div>
@endsection

@section('scripts')
<!-- Scripts modulares -->
<script src="{{ asset('js/secret-santa/url-manager.js') }}"></script>
<script src="{{ asset('js/secret-santa/player-manager.js') }}"></script>
<script src="{{ asset('js/secret-santa/validation.js') }}"></script>
<script src="{{ asset('js/secret-santa/regenerate.js') }}"></script>

<script>
    // Inicializar al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        // Verificar estado de bloqueo de inputs
        checkInputsLockStatus();
    });
</script>
@endsection
