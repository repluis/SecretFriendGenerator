@extends('layouts.app')

@section('title', 'Cargos de ' . $user->name)

@section('content')
    <div style="margin-bottom: var(--spacing-lg);">
        <a href="{{ route('fundraising.recaudaciones', ['type' => $type]) }}" 
           style="display: inline-flex; align-items: center; gap: 0.5rem; color: var(--color-primary-600); text-decoration: none; font-weight: 500;">
            ← Volver a recaudaciones
        </a>
    </div>

    <div style="display: flex; align-items: center; gap: var(--spacing-md); margin-bottom: var(--spacing-md);">
        <x-avatar :name="$user->name" size="xl" />
        <div>
            <h1 style="font-size: 1.875rem; font-weight: 700; color: var(--color-slate-900); margin: 0;">
                {{ $user->name }}
            </h1>
            <p style="color: var(--color-slate-500); margin: 0;">
                Registros de cobros — tipo: <strong>{{ ucfirst($type) }}</strong>
            </p>
        </div>
    </div>

    @php
        $totalBase     = $charges->sum(fn($c) => $c->baseAmount);
        $totalMora     = $charges->sum(fn($c) => $c->penaltyAmount);
        $totalPagado   = $charges->sum(fn($c) => $c->paidAmount);
        $totalDebe     = max(0, ($totalBase + $totalMora) - $totalPagado);
    @endphp

    <!-- Stats Grid -->
    <div class="stats-grid">
        <x-stat-card-modern 
            icon="💵"
            value="${{ number_format($totalBase, 2) }}"
            label="Total Acumulado"
            color="primary"
        />
        <x-stat-card-modern 
            icon="⚠️"
            value="${{ number_format($totalMora, 2) }}"
            label="Total Mora"
            color="danger"
        />
        <x-stat-card-modern 
            icon="✅"
            value="${{ number_format($totalPagado, 2) }}"
            label="Total Pagado"
            color="success"
        />
        <x-stat-card-modern 
            icon="💰"
            value="${{ number_format($totalDebe, 2) }}"
            label="Total que Debe"
            :color="$totalDebe > 0 ? 'warning' : 'success'"
        />
    </div>

    <!-- Tabla de Cargos -->
    <x-table-container title="📋 Cargos del participante">
        <table class="table" style="table-layout: fixed;">
            <thead>
                <tr>
                    <th style="width: 5%;">ID</th>
                    <th style="width: 12%;">Fecha cobro</th>
                    <th style="width: 10%;">Base</th>
                    <th style="width: 12%;">Mora actual</th>
                    <th style="width: 10%;">Pagado</th>
                    <th style="width: 12%;">Total debe</th>
                    <th style="width: 12%;">Estado</th>
                    <th style="width: 27%;">Editar mora</th>
                </tr>
            </thead>
            <tbody>
                @forelse($charges as $charge)
                <tr id="charge-row-{{ $charge->id }}">
                    <td class="text-muted text-sm">{{ $charge->id }}</td>
                    <td class="text-sm">{{ \Carbon\Carbon::parse($charge->chargeDate)->format('d/m/Y') }}</td>
                    <td class="font-semibold">${{ number_format($charge->baseAmount, 2) }}</td>
                    <td>
                        @if($charge->penaltyAmount > 0)
                            <span class="font-semibold" style="color: var(--color-danger-600);">
                                ${{ number_format($charge->penaltyAmount, 2) }}
                            </span>
                        @else
                            <span class="text-muted">$0.00</span>
                        @endif
                    </td>
                    <td class="font-semibold" style="color: var(--color-success-600);">
                        ${{ number_format($charge->paidAmount, 2) }}
                    </td>
                    <td class="font-bold">
                        ${{ number_format($charge->baseAmount + $charge->penaltyAmount, 2) }}
                    </td>
                    <td>
                        @if($charge->isFullyPaid)
                            <x-badge color="success">✅ Pagado</x-badge>
                        @elseif($charge->paidAmount > 0)
                            <x-badge color="warning">⏳ Parcial</x-badge>
                        @else
                            <x-badge color="danger">❌ Pendiente</x-badge>
                        @endif
                    </td>
                    <td>
                        @if(!$charge->isFullyPaid)
                        <div style="display: flex; gap: var(--spacing-xs); align-items: center;">
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-input"
                                style="width: 90px; padding: 0.375rem 0.5rem; font-size: 0.875rem;"
                                id="mora-{{ $charge->id }}"
                                value="{{ number_format($charge->penaltyAmount, 2, '.', '') }}"
                                {{ Auth::user()->isAdmin() ? '' : 'disabled' }}
                            >
                            <x-button 
                                variant="{{ Auth::user()->isAdmin() ? 'primary' : 'secondary' }}"
                                size="sm"
                                onclick="saveMora({{ $charge->id }}, '{{ $type }}')"
                                :disabled="!Auth::user()->isAdmin()"
                            >
                                Guardar
                            </x-button>
                        </div>
                        @else
                            <span class="text-muted text-sm">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <div class="empty-state-icon">📋</div>
                            <p class="empty-state-text">No hay cargos registrados para este participante.</p>
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
    async function saveMora(chargeId, type) {
        const input = document.getElementById('mora-' + chargeId);
        const amount = parseFloat(input.value);

        if (isNaN(amount) || amount < 0) {
            showToast('Ingrese un valor de mora válido (0 o mayor)', 'error');
            return;
        }

        try {
            const response = await fetch('/api/fundraising/charges/' + chargeId + '/penalty', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ penalty_amount: amount, type: type })
            });

            const data = await response.json();

            if (data.success) {
                showToast('Mora actualizada correctamente', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast(data.message || 'No se pudo actualizar la mora', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error al actualizar la mora', 'error');
        }
    }
</script>
@endsection
