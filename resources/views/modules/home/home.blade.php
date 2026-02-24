@extends('layouts.app')

@section('title', 'Dashboard - ' . $appName)

@section('content')
    <x-page-header 
        title="🏠 Home" 
        subtitle="Panel de control del evento navideño. Accede rápidamente a cada sección."
    />

    <!-- Stats Grid -->
    <div class="stats-grid">
        <x-stat-card-modern 
            icon="👥"
            :value="$totalUsers"
            label="Usuarios"
            color="primary"
        />

        <x-stat-card-modern 
            icon="🎮"
            :value="$totalPlayers"
            label="Jugadores"
            color="success"
        />

        <x-stat-card-modern 
            icon="🔗"
            :value="$totalUrls"
            label="URLs Generadas"
            color="warning"
        />

        <x-stat-card-modern 
            icon="💰"
            value="${{ number_format($fundraisingPending, 2) }}"
            label="Deuda Pendiente"
            color="danger"
        />
    </div>

    <!-- Tablas de Rankings -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: var(--spacing-xl); margin-bottom: var(--spacing-xl);">
        <!-- Top Morosos -->
        <x-table-container title="⚠️ Principales Morosos">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 10%;">#</th>
                        <th style="width: 50%;">Usuario</th>
                        <th style="width: 40%;">Mora Acumulada</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $topDebtors = collect($usersWithCharges ?? [])->filter(function($userCharge) {
                            return ($userCharge['total_penalty'] ?? 0) > 0;
                        })->sortByDesc('total_penalty')->take(5);
                    @endphp
                    
                    @forelse($topDebtors as $index => $debtor)
                        @php
                            $user = $users->firstWhere('id', $debtor['user_id']);
                        @endphp
                        @if($user)
                            <tr>
                                <td>
                                    <span class="font-bold" style="color: var(--color-slate-500);">{{ $index + 1 }}</span>
                                </td>
                                <td>
                                    <div class="table-cell-user">
                                        <x-avatar :name="$user->name" size="sm" />
                                        <span class="table-user-name">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="font-bold" style="color: var(--color-danger-600);">
                                        ${{ number_format($debtor['total_penalty'], 2) }}
                                    </span>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="3">
                                <div class="empty-state">
                                    <p class="empty-state-text" style="font-size: 0.875rem;">🎉 No hay morosos</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-table-container>

        <!-- Top Saldos Positivos -->
        <x-table-container title="✅ Mayores Saldos">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 10%;">#</th>
                        <th style="width: 50%;">Usuario</th>
                        <th style="width: 40%;">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $topBalances = collect($users)->map(function($user) use ($userBalances) {
                            $balance = $userBalances[$user->id] ?? 0;
                            return [
                                'user' => $user,
                                'balance' => $balance > 0 ? $balance : 0
                            ];
                        })->filter(function($item) {
                            return $item['balance'] > 0;
                        })->sortByDesc('balance')->take(5);
                    @endphp
                    
                    @forelse($topBalances as $index => $item)
                        <tr>
                            <td>
                                <span class="font-bold" style="color: var(--color-slate-500);">{{ $index + 1 }}</span>
                            </td>
                            <td>
                                <div class="table-cell-user">
                                    <x-avatar :name="$item['user']->name" size="sm" />
                                    <span class="table-user-name">{{ $item['user']->name }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="font-bold" style="color: var(--color-success-600);">
                                    ${{ number_format($item['balance'], 2) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                <div class="empty-state">
                                    <p class="empty-state-text" style="font-size: 0.875rem;">No hay saldos positivos</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-table-container>
    </div>

    <!-- Gráficas -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: var(--spacing-xl); margin-bottom: var(--spacing-xl);">
        <!-- Gráfica de Recaudación -->
        <div class="card">
            <div class="card-header">
                <h3 style="font-size: 1.125rem; font-weight: 600;">💰 Estado de Recaudación</h3>
            </div>
            <div class="card-body">
                <canvas id="fundraisingChart" style="max-height: 300px;"></canvas>
            </div>
        </div>

        <!-- Gráfica de Usuarios por Estado -->
        <div class="card">
            <div class="card-header">
                <h3 style="font-size: 1.125rem; font-weight: 600;">� Distribución de Deudas</h3>
            </div>
            <div class="card-body">
                <canvas id="usersChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Módulos de Acceso Rápido -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: var(--spacing-xl);">
        <!-- Módulo Amigo Secreto -->
        <div class="card">
            <div class="card-header">
                <div style="display: flex; align-items: center; gap: var(--spacing-md);">
                    <div style="width: 48px; height: 48px; border-radius: var(--radius-lg); background: var(--color-primary-100); color: var(--color-primary-600); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                        🎮
                    </div>
                    <div>
                        <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.25rem;">Amigo Secreto</h3>
                        <p style="font-size: 0.875rem; color: var(--color-slate-500);">Gestiona URLs y asignaciones</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div style="display: flex; gap: var(--spacing-md); margin-bottom: var(--spacing-md);">
                    <x-badge :color="$gameStarted ? 'success' : 'warning'">
                        {{ $gameStarted ? '✅ Iniciado' : '⏳ No iniciado' }}
                    </x-badge>
                    <x-badge color="slate">{{ $totalUrls }} URLs</x-badge>
                </div>
                <div style="display: flex; gap: var(--spacing-sm);">
                    <a href="{{ route('juego') }}">
                        <x-button variant="primary" size="sm">Ver Juego</x-button>
                    </a>
                    <a href="{{ route('configuracion') }}">
                        <x-button variant="secondary" size="sm">Configurar</x-button>
                    </a>
                </div>
            </div>
        </div>

        <!-- Módulo Recaudaciones -->
        <div class="card">
            <div class="card-header">
                <div style="display: flex; align-items: center; gap: var(--spacing-md);">
                    <div style="width: 48px; height: 48px; border-radius: var(--radius-lg); background: var(--color-success-100); color: var(--color-success-600); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                        💰
                    </div>
                    <div>
                        <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.25rem;">Recaudaciones</h3>
                        <p style="font-size: 0.875rem; color: var(--color-slate-500);">Cobros y moras</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div style="display: flex; gap: var(--spacing-md); margin-bottom: var(--spacing-md);">
                    <x-badge :color="$fundraisingPending > 0 ? 'warning' : 'success'">
                        ${{ number_format($fundraisingCollected, 2) }} recaudado
                    </x-badge>
                    <x-badge color="slate">{{ $fundraisingUsers }} usuarios</x-badge>
                </div>
                <div style="display: flex; gap: var(--spacing-sm);">
                    <a href="{{ route('fundraising.recaudaciones') }}">
                        <x-button variant="primary" size="sm">Ver Recaudaciones</x-button>
                    </a>
                </div>
            </div>
        </div>

        <!-- Módulo Usuarios -->
        <div class="card">
            <div class="card-header">
                <div style="display: flex; align-items: center; gap: var(--spacing-md);">
                    <div style="width: 48px; height: 48px; border-radius: var(--radius-lg); background: var(--color-warning-100); color: var(--color-warning-600); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                        👥
                    </div>
                    <div>
                        <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.25rem;">Usuarios</h3>
                        <p style="font-size: 0.875rem; color: var(--color-slate-500);">Gestión de usuarios</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div style="display: flex; gap: var(--spacing-md); margin-bottom: var(--spacing-md);">
                    <x-badge color="primary">{{ $totalUsers }} usuarios</x-badge>
                </div>
                <div style="display: flex; gap: var(--spacing-sm);">
                    <a href="{{ route('usuarios') }}">
                        <x-button variant="primary" size="sm">Ver Usuarios</x-button>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <x-toast />
@endsection

@section('scripts')
<!-- Chart.js desde CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
    // Datos para las gráficas
    const fundraisingCollected = {{ $fundraisingCollected }};
    const fundraisingPending = {{ $fundraisingPending }};
    
    @php
        // Calcular distribución de deudas
        $usersUpToDate = 0;
        $usersPartialDebt = 0;
        $usersTotalDebt = 0;
        
        foreach($usersWithCharges ?? [] as $userCharge) {
            $balance = $userCharge['balance'] ?? 0;
            $totalOwed = $userCharge['total_owed'] ?? 0;
            
            if ($balance <= 0) {
                $usersUpToDate++;
            } elseif ($balance < $totalOwed) {
                $usersPartialDebt++;
            } else {
                $usersTotalDebt++;
            }
        }
    @endphp
    
    const usersUpToDate = {{ $usersUpToDate }};
    const usersPartialDebt = {{ $usersPartialDebt }};
    const usersTotalDebt = {{ $usersTotalDebt }};

    // Gráfica de Recaudación (Doughnut)
    const fundraisingCtx = document.getElementById('fundraisingChart').getContext('2d');
    new Chart(fundraisingCtx, {
        type: 'doughnut',
        data: {
            labels: ['Recaudado', 'Pendiente'],
            datasets: [{
                data: [fundraisingCollected, fundraisingPending],
                backgroundColor: [
                    '#16a34a', // success
                    '#dc2626'  // danger
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 13
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': $' + context.parsed.toFixed(2);
                        }
                    }
                }
            }
        }
    });

    // Gráfica de Distribución de Deudas (Pie)
    const usersCtx = document.getElementById('usersChart').getContext('2d');
    new Chart(usersCtx, {
        type: 'pie',
        data: {
            labels: ['Al Día', 'Deuda Parcial', 'Deuda Total'],
            datasets: [{
                data: [usersUpToDate, usersPartialDebt, usersTotalDebt],
                backgroundColor: [
                    '#16a34a', // success - Al día
                    '#d97706', // warning - Deuda parcial
                    '#dc2626'  // danger - Deuda total
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 13
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
