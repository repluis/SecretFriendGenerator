@extends('layouts.app')

@section('title', 'Recaudaciones - ' . ucfirst($type))

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">💰 Recaudaciones - {{ ucfirst($type) }}</h1>
        <p class="text-gray-600">Control de cobros mensuales. Cada 15 se cobra $1.00, mora diaria de $0.05 por atraso</p>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-wrap gap-3 mb-8">
        <button 
            onclick="runFundraisingManual()"
            {{ Auth::user()->isAdmin() ? '' : 'disabled' }}
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ Auth::user()->isAdmin() ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'bg-gray-300 text-gray-500 cursor-not-allowed' }}">
            <span>▶️</span>
            <span>Ejecutar cobro manual</span>
        </button>

        <button 
            onclick="copyResumen()"
            id="btn-copy-resumen"
            class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
            <span>📋</span>
            <span>Copiar resumen</span>
        </button>

        <button 
            onclick="openResetModal()"
            {{ Auth::user()->isAdmin() ? '' : 'disabled' }}
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ Auth::user()->isAdmin() ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-gray-300 text-gray-500 cursor-not-allowed' }}">
            <span>⚠️</span>
            <span>Eliminar todos los datos</span>
        </button>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Recaudado -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-2xl">
                    💵
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-1">
                ${{ number_format($totalFromTransactions, 2) }}
            </div>
            <div class="text-sm font-medium text-gray-600 mb-2">Total Recaudado</div>
            <div class="text-xs text-gray-500">Desde transacciones activas</div>
        </div>

        <!-- Total Adeudado -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center text-2xl">
                    📊
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-1">
                ${{ number_format($summary['total_owed'], 2) }}
            </div>
            <div class="text-sm font-medium text-gray-600 mb-2">Total Adeudado</div>
            <div class="text-xs text-gray-500">Base + moras acumuladas</div>
        </div>

        <!-- Pendiente -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center text-2xl">
                    ⏳
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-1">
                ${{ number_format($summary['total_pending'], 2) }}
            </div>
            <div class="text-sm font-medium text-gray-600 mb-2">Pendiente</div>
            <div class="text-xs text-gray-500">{{ $summary['users_with_debt'] }} persona(s) con deuda</div>
        </div>

        <!-- Moras Acumuladas -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center text-2xl">
                    ⚠️
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-1">
                ${{ number_format($summary['total_penalties'], 2) }}
            </div>
            <div class="text-sm font-medium text-gray-600 mb-2">Moras Acumuladas</div>
            <div class="text-xs text-gray-500">$0.05 diarios por atraso</div>
        </div>
    </div>

    <!-- Progress Bar -->
    @if($summary['total_owed'] > 0)
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-8">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-base font-semibold text-gray-900">Progreso de la recaudación</h3>
            <span class="text-xl font-bold text-indigo-600">{{ $summary['progress'] }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 h-full rounded-full transition-all duration-500" style="width: {{ $summary['progress'] }}%"></div>
        </div>
    </div>
    @endif

    <!-- Tabla de Participantes -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">💳 Detalle por Participante</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participante</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Debe</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pagado</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mora</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        @php
                            $txBalance = $userTransactionBalances[$user['user_id']] ?? 0;
                            $saldo = $user['total_owed'] - $txBalance;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4 text-sm text-gray-500">{{ $user['user_id'] }}</td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white text-sm font-semibold">
                                        {{ strtoupper(substr($user['user_name'], 0, 2)) }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $user['user_name'] }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm font-semibold text-gray-900">${{ number_format($user['total_owed'], 2) }}</td>
                            <td class="px-4 py-4 text-sm font-semibold text-green-600">${{ number_format($txBalance, 2) }}</td>
                            <td class="px-4 py-4 text-sm font-semibold {{ $user['total_penalty'] > 0 ? 'text-red-600' : 'text-gray-400' }}">
                                ${{ number_format($user['total_penalty'], 2) }}
                            </td>
                            <td class="px-4 py-4 text-sm font-bold {{ $saldo <= 0 ? 'text-green-600' : 'text-amber-600' }}">
                                ${{ number_format($saldo, 2) }}
                            </td>
                            <td class="px-4 py-4">
                                @if($saldo <= 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        ✅ Al día
                                    </span>
                                @elseif($txBalance > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        ⏳ Parcial
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        ❌ Pendiente
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2">
                                    <input 
                                        type="number" 
                                        step="0.01" 
                                        min="0.01" 
                                        placeholder="0.00" 
                                        id="pay-amount-{{ $user['user_id'] }}"
                                        {{ Auth::user()->isAdmin() ? '' : 'disabled' }}
                                        class="w-24 px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 {{ Auth::user()->isAdmin() ? '' : 'bg-gray-100 cursor-not-allowed' }}">
                                    <button 
                                        onclick="createPayment({{ $user['user_id'] }}, '{{ addslashes($user['user_name']) }}')"
                                        {{ Auth::user()->isAdmin() ? '' : 'disabled' }}
                                        class="px-3 py-1.5 text-sm font-medium rounded-lg transition-colors {{ Auth::user()->isAdmin() ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'bg-gray-300 text-gray-500 cursor-not-allowed' }}">
                                        Pagar
                                    </button>
                                    <a href="{{ route('fundraising.cargos-usuario', ['userId' => $user['user_id'], 'type' => $type]) }}" 
                                       class="px-3 py-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-lg transition-colors">
                                        Ver Cargos
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center">
                                <div class="text-6xl mb-4">💳</div>
                                <p class="text-gray-500">No hay cobros registrados para el tipo "{{ $type }}".</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tabla de Transacciones -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">📝 Transacciones</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions as $tx)
                        <tr id="tx-row-{{ $tx->id }}" class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white text-xs font-semibold">
                                        {{ strtoupper(substr($tx->user->name ?? '??', 0, 2)) }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $tx->user->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $tx->type === 'credit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $tx->type === 'credit' ? '💰 Crédito' : '💸 Débito' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm font-semibold {{ $tx->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $tx->type === 'credit' ? '+' : '-' }}${{ number_format($tx->amount, 2) }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-600">{{ $tx->description ?? '—' }}</td>
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $tx->active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $tx->active ? '✅ Activa' : '⏸️ Inactiva' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500">
                                {{ $tx->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-4">
                                <button 
                                    onclick="toggleTransaction({{ $tx->id }})"
                                    {{ Auth::user()->isAdmin() ? '' : 'disabled' }}
                                    class="px-3 py-1.5 text-sm font-medium rounded-lg transition-colors {{ Auth::user()->isAdmin() ? ($tx->active ? 'text-red-600 hover:bg-red-50' : 'text-green-600 hover:bg-green-50') : 'text-gray-400 cursor-not-allowed' }}">
                                    {{ $tx->active ? 'Desactivar' : 'Activar' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="text-6xl mb-4">📝</div>
                                <p class="text-gray-500">No hay transacciones registradas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal de Confirmación -->
    <div id="modal-reset-data" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-md w-full shadow-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">⚠️ Confirmar Eliminación de Datos</h3>
            </div>
            <div class="px-6 py-4">
                <p class="text-red-700 mb-4">
                    <strong class="block text-base mb-2">Esta acción es irreversible.</strong>
                    Se eliminarán <strong>todos</strong> los usuarios, transacciones y cobros registrados.
                    ¿Estás seguro de continuar?
                </p>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex justify-end gap-3">
                <button 
                    onclick="closeResetModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button 
                    onclick="confirmReset()"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                    Sí, eliminar todo
                </button>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed bottom-4 right-4 z-50"></div>
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

function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    const colors = {
        success: 'bg-green-600',
        error: 'bg-red-600',
        warning: 'bg-amber-600',
        info: 'bg-indigo-600'
    };
    
    const toast = document.createElement('div');
    toast.className = `${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg mb-3 transform transition-all duration-300 translate-x-full`;
    toast.textContent = message;
    
    container.appendChild(toast);
    
    setTimeout(() => toast.classList.remove('translate-x-full'), 100);
    
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
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
        btn.innerHTML = '<span>✅</span><span>Copiado!</span>';
        btn.classList.remove('bg-green-600', 'hover:bg-green-700');
        btn.classList.add('bg-green-700');
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.classList.remove('bg-green-700');
            btn.classList.add('bg-green-600', 'hover:bg-green-700');
        }, 2000);
    }).catch(() => {
        showToast('No se pudo copiar al portapapeles', 'error');
    });
}

function openResetModal() {
    document.getElementById('modal-reset-data').classList.remove('hidden');
}

function closeResetModal() {
    document.getElementById('modal-reset-data').classList.add('hidden');
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
