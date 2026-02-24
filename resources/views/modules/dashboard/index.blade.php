@extends('layouts.app')

@section('title', 'Dashboard - ' . $appName)

@section('content')
    <x-page-header 
        title="📊 Dashboard" 
        subtitle="Resumen general de pagos y estado del evento"
    />

    <!-- Stats Grid -->
    <div class="stats-grid">
        @php
            $totalPagado = collect($userTransactionBalances)->sum();
            $totalMora = collect($users)->sum('total_penalty');
            $totalAdeudado = collect($users)->sum(function($user) use ($userTransactionBalances) {
                $txBalance = $userTransactionBalances[$user['user_id']] ?? 0;
                return max(0, $user['total_owed'] - $txBalance);
            });
            $participantes = count($users);
        @endphp

        <x-stat-card-modern 
            icon="👥"
            value="{{ $participantes }}"
            label="Participantes"
            color="primary"
        />

        <x-stat-card-modern 
            icon="💰"
            value="${{ number_format($totalPagado, 2) }}"
            label="Total Pagado"
            color="success"
        />

        <x-stat-card-modern 
            icon="⚠️"
            value="${{ number_format($totalMora, 2) }}"
            label="Total Moras"
            color="warning"
        />

        <x-stat-card-modern 
            icon="📊"
            value="${{ number_format($totalAdeudado, 2) }}"
            label="Saldo Pendiente"
            color="danger"
        />
    </div>

    <!-- Tabla de Estado de Pagos -->
    <x-table-container title="💳 Estado de Pagos por Participante ({{ count($users) }})">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 28%;">Participante</th>
                    <th style="width: 14%;">Total Adeudado</th>
                    <th style="width: 14%;">Pagado</th>
                    <th style="width: 14%;">Mora</th>
                    <th style="width: 14%;">Saldo</th>
                    <th style="width: 16%;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    @php
                        $txBalance = $userTransactionBalances[$user['user_id']] ?? 0;
                        $saldo = $user['total_owed'] - $txBalance;
                        $porcentajePagado = $user['total_owed'] > 0 ? ($txBalance / $user['total_owed']) * 100 : 0;
                    @endphp
                    <tr>
                        <td>
                            <div class="table-cell-user">
                                <x-avatar :name="$user['user_name']" size="md" />
                                <div class="table-user-info">
                                    <span class="table-user-name">{{ $user['user_name'] }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="font-semibold" style="color: var(--color-slate-700);">
                                ${{ number_format($user['total_owed'], 2) }}
                            </span>
                        </td>
                        <td>
                            <span class="font-semibold" style="color: var(--color-success-600);">
                                ${{ number_format($txBalance, 2) }}
                            </span>
                        </td>
                        <td>
                            @if($user['total_penalty'] > 0)
                                <span class="font-semibold" style="color: var(--color-danger-600);">
                                    ${{ number_format($user['total_penalty'], 2) }}
                                </span>
                            @else
                                <span class="text-muted">$0.00</span>
                            @endif
                        </td>
                        <td>
                            <span class="font-bold" style="color: {{ $saldo <= 0 ? 'var(--color-success-600)' : 'var(--color-warning-600)' }};">
                                ${{ number_format($saldo, 2) }}
                            </span>
                        </td>
                        <td>
                            @if($saldo <= 0)
                                <x-badge color="success">✅ Pagado</x-badge>
                            @elseif($porcentajePagado >= 50)
                                <x-badge color="warning">⏳ Parcial</x-badge>
                            @else
                                <x-badge color="danger">❌ Pendiente</x-badge>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-state-icon">📭</div>
                                <p class="empty-state-text">No hay cobros registrados aún.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-container>

    <x-toast />
@endsection

@section('scripts')
<script>
    // Animación de entrada para las stats
    document.addEventListener('DOMContentLoaded', function() {
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.4s ease';
                
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 50);
            }, index * 100);
        });
    });
</script>
@endsection
