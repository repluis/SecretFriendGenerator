@extends('layouts.app')

@section('title', 'Recaudaciones - ' . ucfirst($type))

@section('content')
    <x-page-header 
        title="💰 Recaudaciones - {{ ucfirst($type) }}" 
        subtitle="Control de cobros mensuales. Cada 15 se cobra $1.00, mora diaria de $0.05 por atraso"
    />

    <!-- Action Buttons -->
    <div style="display: flex; gap: var(--spacing-sm); margin-bottom: var(--spacing-xl); flex-wrap: wrap;">
        <x-button 
            variant="{{ Auth::user()->isAdmin() ? 'primary' : 'secondary' }}"
            icon="▶️"
            onclick="runFundraisingManual()"
            :disabled="!Auth::user()->isAdmin()"
        >
            Ejecutar cobro manual
        </x-button>

        <x-button 
            variant="success"
            icon="📋"
            onclick="copyResumen()"
            id="btn-copy-resumen"
        >
            Copiar resumen
        </x-button>

        <x-button 
            variant="{{ Auth::user()->isAdmin() ? 'danger' : 'secondary' }}"
            icon="⚠️"
            onclick="openResetModal()"
            :disabled="!Auth::user()->isAdmin()"
        >
            Eliminar todos los datos
        </x-button>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <x-stat-card-modern 
            icon="💵"
            value="${{ number_format($totalFromTransactions, 2) }}"
            label="Total Recaudado"
            color="success"
            footer="Desde transacciones activas"
        />

        <x-stat-card-modern 
            icon="📊"
            value="${{ number_format($summary['total_owed'], 2) }}"
            label="Total Adeudado"
            color="primary"
            footer="Base + moras acumuladas"
        />

        <x-stat-card-modern 
            icon="⏳"
            value="${{ number_format($summary['total_pending'], 2) }}"
            label="Pendiente"
            color="warning"
            footer="{{ $summary['users_with_debt'] }} persona(s) con deuda"
        />

        <x-stat-card-modern 
            icon="⚠️"
            value="${{ number_format($summary['total_penalties'], 2) }}"
            label="Moras Acumuladas"
            color="danger"
            footer="$0.05 diarios por atraso"
        />
    </div>

    <!-- Progress Bar -->
    @if($summary['total_owed'] > 0)
    <div class="card" style="margin-bottom: var(--spacing-xl);">
        <div class="card-body">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-md);">
                <h3 style="font-size: 1rem; font-weight: 600; color: var(--color-slate-900);">Progreso de la recaudación</h3>
                <span style="font-size: 1.25rem; font-weight: 700; color: var(--color-primary-600);">{{ $summary['progress'] }}%</span>
            </div>
            <div style="background: var(--color-slate-200); border-radius: var(--radius-full); height: 12px; overflow: hidden;">
                <div style="background: linear-gradient(90deg, var(--color-primary-500), var(--color-primary-600)); height: 100%; border-radius: var(--radius-full); width: {{ $summary['progress'] }}%; transition: width 0.5s ease;"></div>
            </div>
        </div>
    </div>
    @endif

    <!-- Tabla de Participantes -->
    <x-table-container title="💳 Detalle por Participante">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Participante</th>
                    <th>Debe</th>
                    <th>Pagado</th>
                    <th>Mora</th>
                    <th>Saldo</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    @php
                        $txBalance = $userTransactionBalances[$user['user_id']] ?? 0;
                        $saldo = $user['total_owed'] - $txBalance;
                    @endphp
                    <tr>
                        <td class="text-muted text-sm">{{ $user['user_id'] }}</td>
                        <td>
                            <div class="table-cell-user">
                                <x-avatar :name="$user['user_name']" size="md" />
                                <span class="table-user-name">{{ $user['user_name'] }}</span>
                            </div>
                        </td>
                        <td class="font-semibold">${{ number_format($user['total_owed'], 2) }}</td>
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
                                <x-badge color="success">✅ Al día</x-badge>
                            @elseif($txBalance > 0)
                                <x-badge color="warning">⏳ Parcial</x-badge>
                            @else
                                <x-badge color="danger">❌ Pendiente</x-badge>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: var(--spacing-xs); align-items: center;">
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    min="0.01" 
                                    placeholder="0.00" 
                                    id="pay-amount-{{ $user['user_id'] }}"
                                    class="form-input"
                                    style="width: 90px; padding: 0.375rem 0.5rem; font-size: 0.875rem;"
                                    {{ Auth::user()->isAdmin() ? '' : 'disabled' }}
                                >
                                <x-button 
                                    variant="{{ Auth::user()->isAdmin() ? 'primary' : 'secondary' }}"
                                    size="sm"
                                    onclick="createPayment({{ $user['user_id'] }}, '{{ addslashes($user['user_name']) }}')"
                                    :disabled="!Auth::user()->isAdmin()"
                                >
                                    Pagar
                                </x-button>
                                <a href="{{ route('fundraising.cargos-usuario', ['userId' => $user['user_id'], 'type' => $type]) }}" class="btn btn-ghost-primary btn-sm">
                                    Ver Cargos
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <div class="empty-state-icon">💳</div>
                                <p class="empty-state-text">No hay cobros registrados para el tipo "{{ $type }}".</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-container>

    <!-- Tabla de Transacciones -->
    <x-table-container title="📝 Transacciones">
        <table class="table">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Tipo</th>
                    <th>Monto</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                    <tr id="tx-row-{{ $tx->id }}">
                        <td>
                            <div class="table-cell-user">
                                <x-avatar :name="$tx->user->name ?? '??'" size="sm" />
                                <span class="table-user-name">{{ $tx->user->name ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td>
                            <x-badge :color="$tx->type === 'credit' ? 'success' : 'danger'">
                                {{ $tx->type === 'credit' ? '💰 Crédito' : '💸 Débito' }}
                            </x-badge>
                        </td>
                        <td>
                            <span class="font-semibold" style="color: {{ $tx->type === 'credit' ? 'var(--color-success-600)' : 'var(--color-danger-600)' }};">
                                {{ $tx->type === 'credit' ? '+' : '-' }}${{ number_format($tx->amount, 2) }}
                            </span>
                        </td>
                        <td class="text-sm">{{ $tx->description ?? '—' }}</td>
                        <td>
                            <x-badge :color="$tx->active ? 'success' : 'slate'">
                                {{ $tx->active ? '✅ Activa' : '⏸️ Inactiva' }}
                            </x-badge>
                        </td>
                        <td class="text-sm text-muted">
                            {{ $tx->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td>
                            <x-button 
                                :variant="$tx->active ? 'ghost-danger' : 'ghost-success'"
                                size="sm"
                                onclick="toggleTransaction({{ $tx->id }})"
                                :disabled="!Auth::user()->isAdmin()"
                            >
                                {{ $tx->active ? 'Desactivar' : 'Activar' }}
                            </x-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-state-icon">📝</div>
                                <p class="empty-state-text">No hay transacciones registradas.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-container>

    <!-- Modal de Confirmación -->
    <div class="modal-overlay" id="modal-reset-data">
        <div class="modal">
            <div class="modal-header">⚠️ Confirmar Eliminación de Datos</div>
            <div class="modal-body">
                <p style="color: var(--color-danger-700); margin-bottom: var(--spacing-md);">
                    <strong style="display: block; font-size: 1rem; margin-bottom: var(--spacing-xs);">Esta acción es irreversible.</strong>
                    Se eliminarán <strong>todos</strong> los usuarios, transacciones y cobros registrados.
                    ¿Estás seguro de continuar?
                </p>
            </div>
            <div class="modal-footer">
                <x-button variant="secondary" onclick="closeResetModal()">
                    Cancelar
                </x-button>
                <x-button variant="danger" onclick="confirmReset()">
                    Sí, eliminar todo
                </x-button>
            </div>
        </div>
    </div>

    <x-toast />
@endsection

@section('scripts')
<script>
const resumenData = {
    totalRecaudado: {{ $totalFromTransactions }},
    totalPendiente: {{ $summary['total_pending'] }},
    users: [
        @foreach($users as $user)
        @php
            $txBal = $userTransactionBalances[$user['user_id']] ?? 0;
            $saldoUser = $user['total_owed'] - $txBal;
        @endphp
        {
            name: @json($user['user_name']),
            pago: {{ $txBal }},
            debe: {{ max(0, $saldoUser) }}
        },
        @endforeach
    ]
};

function fmt(n) {
    return '$' + parseFloat(n).toFixed(2);
}

function copyResumen() {
    const morosos = resumenData.users
        .filter(u => u.debe > 0)
        .sort((a, b) => b.debe - a.debe);

    const pagaron = resumenData.users
        .filter(u => u.debe <= 0)
        .sort((a, b) => a.name.localeCompare(b.name));

    const today = new Date().toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
    const sep = '━━━━━━━━━━━━━━━━━━━━';

    let lines = [];
    lines.push('📊 *Resumen de Recaudaciones*');
    lines.push('_' + today + '_');
    lines.push('');
    lines.push('💰 *Total Recaudado:* ' + fmt(resumenData.totalRecaudado));
    lines.push('⏳ *Pendiente:* ' + fmt(resumenData.totalPendiente));

    if (morosos.length > 0) {
        lines.push('');
        lines.push(sep);
        lines.push('🔴 *MOROSOS*');
        lines.push(sep);
        morosos.forEach(u => {
            lines.push('• ' + u.name + ' → pagó ' + fmt(u.pago) + ' | debe ' + fmt(u.debe));
        });
    }

    if (pagaron.length > 0) {
        lines.push('');
        lines.push(sep);
        lines.push('✅ *YA PAGARON*');
        lines.push(sep);
        pagaron.forEach(u => {
            lines.push('• ' + u.name + ' → pagó ' + fmt(u.pago) + ' ✔');
        });
    }

    const text = lines.join('\n');

    navigator.clipboard.writeText(text).then(() => {
        const btn = document.getElementById('btn-copy-resumen');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '✅ Copiado!';
        btn.style.background = 'var(--color-success-600)';
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.style.background = '';
        }, 2000);
    }).catch(() => {
        showToast('No se pudo copiar al portapapeles', 'error');
    });
}

function openResetModal() {
    document.getElementById('modal-reset-data').classList.add('active');
}

function closeResetModal() {
    document.getElementById('modal-reset-data').classList.remove('active');
}

async function confirmReset() {
    try {
        const response = await fetch('/api/fundraising/reset-data', {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });

        const data = await response.json();

        if (data.success) {
            closeResetModal();
            showToast('Datos eliminados correctamente', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(data.message || 'Error al eliminar los datos', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Error de conexión', 'error');
    }
}

async function createPayment(userId, userName) {
    const input = document.getElementById('pay-amount-' + userId);
    const amount = parseFloat(input.value);

    if (!amount || amount <= 0) {
        showToast('Ingrese un monto válido', 'error');
        return;
    }

    const confirmed = confirm(
        '¿Confirmar pago?\n\n' +
        'Participante: ' + userName + '\n' +
        'Monto: $' + amount.toFixed(2)
    );
    if (!confirmed) return;

    try {
        const response = await fetch('/api/transactions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                user_id: userId,
                type: 'credit',
                amount: amount,
                description: 'Pago manual - ' + userName
            })
        });

        const data = await response.json();

        if (data.success) {
            showToast('Pago registrado correctamente', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(data.message || 'Error al registrar el pago', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Error de conexión', 'error');
    }
}

async function runFundraisingManual() {
    if (!confirm('Esto creará cobros para la fecha de hoy y aplicará multas pendientes. ¿Continuar?')) return;
    
    try {
        const response = await fetch('/api/fundraising/run-manual', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
        });
        const data = await response.json();
        
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(data.message || 'Error al ejecutar', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Error de conexión', 'error');
    }
}

async function toggleTransaction(id) {
    try {
        const response = await fetch('/api/transactions/' + id + '/toggle-active', {
            method: 'PATCH',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });

        const data = await response.json();

        if (data.success) {
            showToast('Estado actualizado correctamente', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(data.message || 'Error al cambiar el estado', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Error de conexión', 'error');
    }
}
</script>
@endsection
