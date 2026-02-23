@extends('layouts.app')

@section('title', 'Pagos')

@section('styles')
<style>
    .page-header { margin-bottom: 2rem; }
    .page-header h1 { font-size: 2rem; font-weight: 700; margin-bottom: 0.25rem; }
    .page-header p { color: #64748b; }

    /* Card-based mobile layout */
    .pagos-table { width: 100%; border-collapse: collapse; }

    .pagos-table thead th {
        text-align: left; padding: 0.75rem 1.5rem;
        font-size: 0.75rem; font-weight: 600;
        text-transform: uppercase; letter-spacing: 0.05em;
        color: #64748b; background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    .pagos-table tbody td {
        padding: 0.85rem 1.5rem; font-size: 0.9rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .pagos-table tbody tr:last-child td { border-bottom: none; }
    .pagos-table tbody tr:hover { background: #f8fafc; }

    /* Desktop-only columns */
    .desktop-only { /* visible by default */ }

    /* Mobile card layout */
    @media (max-width: 768px) {
        .desktop-only { display: none !important; }

        .table-section { overflow: visible !important; border: none !important; }
        .pagos-table-wrapper { overflow: visible !important; }

        .pagos-table,
        .pagos-table thead,
        .pagos-table tbody,
        .pagos-table tr,
        .pagos-table td { display: block !important; width: 100% !important; }

        .pagos-table { min-width: 0 !important; }
        .pagos-table thead { display: none !important; }

        .pagos-table tbody tr {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }

        .pagos-table tbody tr:hover { background: white; }

        .pagos-table tbody td {
            display: flex !important;
            justify-content: space-between;
            align-items: center;
            padding: 0.4rem 0 !important;
            border: none !important;
        }

        .pagos-table tbody td::before {
            content: attr(data-label);
            font-size: 0.75rem; font-weight: 600;
            color: #64748b; text-transform: uppercase;
            letter-spacing: 0.04em;
            min-width: 60px;
        }

        .mobile-name-cell {
            font-size: 1.05rem !important;
            font-weight: 600;
            padding-bottom: 0.5rem !important;
            border-bottom: 1px solid #f1f5f9 !important;
            margin-bottom: 0.25rem;
        }

        .mobile-name-cell::before { display: none !important; }

        .mobile-actions {
            flex-direction: column !important;
            align-items: stretch !important;
            padding-top: 0.5rem !important;
            border-top: 1px solid #f1f5f9 !important;
        }

        .mobile-actions::before { display: none !important; }

        .pay-form-mobile {
            display: flex;
            gap: 0.5rem;
            width: 100%;
        }

        .pay-form-mobile input {
            flex: 1;
            min-width: 60px;
            width: auto !important;
        }

        .pay-form-mobile .btn-pay,
        .pay-form-mobile .btn-cargos {
            white-space: nowrap;
        }
    }

    /* Pay form */
    .pay-form { display: flex; align-items: center; gap: 0.5rem; }
    .pay-form input {
        width: 80px; padding: 0.35rem 0.5rem;
        border: 1px solid #e2e8f0; border-radius: 6px;
        font-size: 0.8rem; text-align: right;
    }
    .pay-form input:focus {
        outline: none; border-color: #6366f1;
        box-shadow: 0 0 0 2px rgba(99,102,241,0.1);
    }

    .btn-pay {
        display: inline-flex; align-items: center; gap: 0.35rem;
        padding: 0.4rem 1rem; background: #6366f1; color: white;
        border: none; border-radius: 8px; font-size: 0.8rem;
        font-weight: 500; cursor: pointer; transition: background 0.2s;
        white-space: nowrap;
    }
    .btn-pay:hover { background: #4f46e5; }

    .btn-cargos {
        display: inline-flex; align-items: center; gap: 0.35rem;
        padding: 0.35rem 0.85rem; border: none; border-radius: 8px;
        font-size: 0.78rem; font-weight: 500; cursor: pointer;
        transition: background 0.2s;
        background: #dcfce7; color: #16a34a;
        text-decoration: none;
    }
    .btn-cargos:hover { background: #bbf7d0; }

    /* Summary strip */
    .summary-strip {
        display: flex; gap: 1rem; margin-bottom: 2rem;
        flex-wrap: wrap;
    }
    .summary-strip .chip {
        background: white; border: 1px solid #e2e8f0;
        border-radius: 10px; padding: 0.75rem 1.25rem;
        flex: 1; min-width: 140px; text-align: center;
    }
    .chip .chip-label { font-size: 0.72rem; color: #64748b; text-transform: uppercase; font-weight: 600; letter-spacing: 0.04em; }
    .chip .chip-value { font-size: 1.5rem; font-weight: 700; margin-top: 0.25rem; }
    .chip .chip-value.green { color: #16a34a; }
    .chip .chip-value.amber { color: #d97706; }
    .chip .chip-value.red { color: #dc2626; }
</style>
@endsection

@section('content')
    <div class="page-header">
        <h1>💳 Pagos</h1>
        <p>Registra pagos r&aacute;pidamente. Mora diaria de $0.05 por atraso.</p>
    </div>

    <div class="summary-strip">
        <div class="chip">
            <div class="chip-label">Recaudado</div>
            <div class="chip-value green">${{ number_format($totalFromTransactions, 2) }}</div>
        </div>
        <div class="chip">
            <div class="chip-label">Pendiente</div>
            <div class="chip-value amber">${{ number_format($summary['total_pending'], 2) }}</div>
        </div>
        <div class="chip">
            <div class="chip-label">Moras</div>
            <div class="chip-value red">${{ number_format($summary['total_penalties'], 2) }}</div>
        </div>
    </div>

    <div class="table-section">
        <div class="table-toolbar">
            <h3>Detalle por participante</h3>
        </div>
        <div class="pagos-table-wrapper">
            <table class="pagos-table">
                <thead>
                    <tr>
                        <th>Participante</th>
                        <th class="desktop-only">Debe</th>
                        <th class="desktop-only">Pagado</th>
                        <th>Mora</th>
                        <th>Saldo</th>
                        <th class="desktop-only">Estado</th>
                        <th>Acci&oacute;n</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    @php
                        $txBalance = $userTransactionBalances[$user['user_id']] ?? 0;
                        $saldo = $user['total_owed'] - $txBalance;
                    @endphp
                    <tr>
                        <td class="mobile-name-cell" data-label="">
                            <x-avatar :name="$user['user_name']" />
                            {{ $user['user_name'] }}
                        </td>
                        <td class="desktop-only" data-label="Debe">${{ number_format($user['total_owed'], 2) }}</td>
                        <td class="desktop-only" data-label="Pagado">${{ number_format($txBalance, 2) }}</td>
                        <td data-label="Mora">
                            @if($user['total_penalty'] > 0)
                                <span style="color: #dc2626; font-weight: 600;">${{ number_format($user['total_penalty'], 2) }}</span>
                            @else
                                <span style="color: #94a3b8;">$0.00</span>
                            @endif
                        </td>
                        <td data-label="Saldo">
                            <span style="font-weight: 700; color: {{ $saldo <= 0 ? '#16a34a' : '#1e293b' }};">
                                ${{ number_format($saldo, 2) }}
                            </span>
                        </td>
                        <td class="desktop-only" data-label="Estado">
                            @if($saldo <= 0)
                                <x-badge color="green">Al d&iacute;a</x-badge>
                            @elseif($txBalance > 0)
                                <x-badge color="amber">Parcial</x-badge>
                            @else
                                <x-badge color="red">Pendiente</x-badge>
                            @endif
                        </td>
                        <td class="mobile-actions" data-label="">
                            <div class="pay-form pay-form-mobile">
                                <input type="number" step="0.01" min="0.01" placeholder="0.00" id="pay-amount-{{ $user['user_id'] }}">
                                <button class="btn-pay" onclick="createPayment({{ $user['user_id'] }}, '{{ addslashes($user['user_name']) }}')">
                                    Pagar
                                </button>
                                <a
                                    href="{{ route('fundraising.cargos-usuario', ['userId' => $user['user_id'], 'type' => $type]) }}"
                                    class="btn-cargos"
                                    title="Ver cargos detallados"
                                >
                                    Cargos
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <x-empty-state message="No hay cobros registrados." submessage="Los cobros se generan autom&aacute;ticamente cada 15 del mes." />
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    async function createPayment(userId, userName) {
        const input = document.getElementById('pay-amount-' + userId);
        const amount = parseFloat(input.value);

        if (!amount || amount <= 0) {
            alert('Ingrese un monto válido');
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
</script>
@endsection
