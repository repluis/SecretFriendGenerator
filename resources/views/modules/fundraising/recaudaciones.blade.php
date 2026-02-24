@extends('layouts.app')

@section('title', 'Recaudaciones - ' . ucfirst($type))

@section('styles')
<style>
    .page-header { margin-bottom: 2.5rem; }
    .page-header h1 { font-size: 2rem; font-weight: 700; margin-bottom: 0.25rem; }
    .page-header p { color: #64748b; margin-bottom: 1rem; }
    .page-header-row { display: flex; flex-direction: column; gap: 0; }
    .btn-reset-data {
        display: inline-flex; align-items: center; gap: 0.4rem;
        background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5;
        padding: 0.45rem 1rem; border-radius: 0.5rem; font-size: 0.85rem;
        font-weight: 600; cursor: pointer; white-space: nowrap; transition: background .15s;
    }
    .btn-reset-data:hover { background: #fca5a5; }
    .btn-copy-resumen {
        display: inline-flex; align-items: center; gap: 0.4rem;
        background: #dcfce7; color: #166534; border: 1px solid #86efac;
        padding: 0.45rem 1rem; border-radius: 0.5rem; font-size: 0.85rem;
        font-weight: 600; cursor: pointer; white-space: nowrap; transition: background .15s;
    }
    .btn-copy-resumen:hover { background: #bbf7d0; }
    .btn-copy-resumen.copied { background: #166534; color: #fff; border-color: #166534; }
    .btn-run-fundraising {
        display: inline-flex; align-items: center; gap: 0.4rem;
        background: #ede9fe; color: #4f46e5; border: 1px solid #c4b5fd;
        padding: 0.45rem 1rem; border-radius: 0.5rem; font-size: 0.85rem;
        font-weight: 600; cursor: pointer; white-space: nowrap; transition: background .15s;
    }
    .btn-run-fundraising:hover { background: #ddd6fe; }
    .btn-run-fundraising:disabled { opacity: 0.6; cursor: not-allowed; }
    .header-actions { display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; }
    .modal-warning { color: #7f1d1d; font-size: 0.9rem; margin: 0.5rem 0 1.25rem; line-height: 1.5; }
    .modal-warning strong { display: block; font-size: 1rem; margin-bottom: 0.35rem; }
    .modal-actions { display: flex; gap: 0.75rem; justify-content: flex-end; }
</style>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-row">
            <h1>Recaudaciones &mdash; {{ ucfirst($type) }}</h1>
            <p>Control de cobros mensuales. Cada 15 se cobra $1.00, mora diaria de $0.05 por atraso.</p>
            <div class="header-actions">
                <button class="btn-run-fundraising" id="btn-run-fundraising" onclick="runFundraisingManual()">
                    &#9654; Ejecutar cobro manual
                </button>
                <button class="btn-copy-resumen" id="btn-copy-resumen" onclick="copyResumen()">
                    &#128203; Copiar resumen
                </button>
                <button class="btn-reset-data" onclick="openResetModal()">
                    &#9888; Eliminar todos los datos
                </button>
            </div>
        </div>
    </div>

    {{-- Modal de confirmación para reset --}}
    <x-modal id="modal-reset-data" title="Confirmar eliminacion de datos">
        <p class="modal-warning">
            <strong>Esta accion es irreversible.</strong>
            Se eliminar&aacute;n <strong>todos</strong> los usuarios, transacciones y cobros registrados.
            &iquest;Est&aacute;s seguro de continuar?
        </p>
        <div class="modal-actions">
            <x-form.button variant="cancel" type="button" onclick="closeResetModal()">
                Cancelar
            </x-form.button>
            <x-form.button variant="danger" type="button" onclick="confirmReset()">
                S&iacute;, eliminar todo
            </x-form.button>
        </div>
    </x-modal>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="label">Total Recaudado</div>
            <div class="value green">${{ number_format($totalFromTransactions, 2) }}</div>
            <div class="detail">Calculado desde transacciones activas</div>
        </div>
        <div class="summary-card">
            <div class="label">Total Adeudado</div>
            <div class="value blue">${{ number_format($summary['total_owed'], 2) }}</div>
            <div class="detail">Base + moras acumuladas</div>
        </div>
        <div class="summary-card">
            <div class="label">Pendiente</div>
            <div class="value amber">${{ number_format($summary['total_pending'], 2) }}</div>
            <div class="detail">{{ $summary['users_with_debt'] }} persona(s) con deuda</div>
        </div>
        <div class="summary-card">
            <div class="label">Moras Acumuladas</div>
            <div class="value red">${{ number_format($summary['total_penalties'], 2) }}</div>
            <div class="detail">$0.05 diarios por atraso</div>
        </div>
    </div>

    @if($summary['total_owed'] > 0)
    <div class="progress-section">
        <div class="progress-header">
            <h3>Progreso de la recaudaci&oacute;n</h3>
            <span>{{ $summary['progress'] }}%</span>
        </div>
        <div class="progress-bar-bg">
            <div class="progress-bar-fill" style="width: {{ $summary['progress'] }}%;"></div>
        </div>
    </div>
    @endif

    <x-table.table :headers="['ID', 'Participante', 'Debe', 'Pagado', 'Mora', 'Saldo', 'Estado', 'Acci&oacute;n']" title="Detalle por participante">
        @forelse($users as $user)
        @php
            $txBalance = $userTransactionBalances[$user['user_id']] ?? 0;
            $saldo = $user['total_owed'] - $txBalance;
        @endphp
        <tr>
            <td style="color: #94a3b8; font-size: 0.82rem;">{{ $user['user_id'] }}</td>
            <td>
                <x-avatar :name="$user['user_name']" />
                {{ $user['user_name'] }}
            </td>
            <td>${{ number_format($user['total_owed'], 2) }}</td>
            <td>${{ number_format($txBalance, 2) }}</td>
            <td>
                @if($user['total_penalty'] > 0)
                    <span style="color: #dc2626;">${{ number_format($user['total_penalty'], 2) }}</span>
                @else
                    <span style="color: #94a3b8;">$0.00</span>
                @endif
            </td>
            <td>
                <span class="amount-main">${{ number_format($saldo, 2) }}</span>
            </td>
            <td>
                @if($saldo <= 0)
                    <x-badge color="green">Al d&iacute;a</x-badge>
                @elseif($txBalance > 0)
                    <x-badge color="amber">Parcial</x-badge>
                @else
                    <x-badge color="red">Pendiente</x-badge>
                @endif
            </td>
            <td>
                <div class="pay-form">
                    <input type="number" step="0.01" min="0.01" placeholder="0.00" id="pay-amount-{{ $user['user_id'] }}">
                    <button class="btn-pay" onclick="createPayment({{ $user['user_id'] }}, '{{ addslashes($user['user_name']) }}')">
                        Pagar
                    </button>
                    <a
                        href="{{ route('fundraising.cargos-usuario', ['userId' => $user['user_id'], 'type' => $type]) }}"
                        class="btn-toggle inactive"
                        title="Ver cargos detallados"
                    >
                        Cargos
                    </a>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8">
                <x-empty-state message="No hay cobros registrados para el tipo &quot;{{ $type }}&quot;." submessage="Los cobros se generan autom&aacute;ticamente cada 15 del mes." />
            </td>
        </tr>
        @endforelse
    </x-table.table>

    <!-- Transactions Table -->
    <x-table.table :headers="['Usuario', 'Tipo', 'Monto', 'Descripci&oacute;n', 'Estado', 'Fecha', 'Acci&oacute;n']" title="Transacciones">
        @forelse($transactions as $tx)
        <tr id="tx-row-{{ $tx->id }}">
            <td>
                <x-avatar :name="$tx->user->name ?? '??'" />
                {{ $tx->user->name ?? 'N/A' }}
            </td>
            <td>
                @if($tx->type === 'credit')
                    <x-badge color="green">Cr&eacute;dito</x-badge>
                @else
                    <x-badge color="red">D&eacute;bito</x-badge>
                @endif
            </td>
            <td>
                <span style="font-weight: 600; color: {{ $tx->type === 'credit' ? '#16a34a' : '#dc2626' }};">
                    {{ $tx->type === 'credit' ? '+' : '-' }}${{ number_format($tx->amount, 2) }}
                </span>
            </td>
            <td>{{ $tx->description ?? '&mdash;' }}</td>
            <td>
                <x-badge :color="$tx->active ? 'green' : 'slate'">{{ $tx->active ? 'Activa' : 'Inactiva' }}</x-badge>
            </td>
            <td style="font-size: 0.82rem; color: #64748b;">
                {{ $tx->created_at->format('d/m/Y H:i') }}
            </td>
            <td>
                <button
                    class="btn-toggle {{ $tx->active ? 'active' : 'inactive' }}"
                    onclick="toggleTransaction({{ $tx->id }})"
                >
                    {{ $tx->active ? 'Desactivar' : 'Activar' }}
                </button>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7">
                <x-empty-state message="No hay transacciones registradas." />
            </td>
        </tr>
        @endforelse
    </x-table.table>
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
            btn.textContent = '✔ Copiado!';
            btn.classList.add('copied');
            setTimeout(() => {
                btn.innerHTML = '&#128203; Copiar resumen';
                btn.classList.remove('copied');
            }, 2000);
        }).catch(() => {
            alert('No se pudo copiar al portapapeles. Intenta desde un navegador moderno.');
        });
    }

    function openResetModal() {
        document.getElementById('modal-reset-data').classList.add('open');
    }

    function closeResetModal() {
        document.getElementById('modal-reset-data').classList.remove('open');
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
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'No se pudo eliminar los datos'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al eliminar los datos');
        }
    }

    async function createPayment(userId, userName) {
        const input = document.getElementById('pay-amount-' + userId);
        const amount = parseFloat(input.value);

        if (!amount || amount <= 0) {
            alert('Ingrese un monto valido');
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
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'No se pudo registrar el pago'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al registrar el pago');
        }
    }

    async function runFundraisingManual() {
        const btn = document.getElementById('btn-run-fundraising');
        if (!confirm('Esto creará cobros para la fecha de hoy y aplicará multas pendientes. ¿Continuar?')) return;
        btn.disabled = true; btn.textContent = 'Ejecutando...';
        try {
            const response = await fetch('/api/fundraising/run-manual', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });
            const data = await response.json();
            if (data.success) { alert(data.message); location.reload(); }
            else { alert('Error: ' + (data.message || 'No se pudo ejecutar')); }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al ejecutar el cobro manual');
        } finally {
            btn.disabled = false; btn.innerHTML = '&#9654; Ejecutar cobro manual';
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
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'No se pudo cambiar el estado'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al cambiar el estado de la transaccion');
        }
    }
</script>
@endsection
