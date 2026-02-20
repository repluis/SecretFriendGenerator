@extends('layouts.app')

@section('title', 'Cargos de ' . $user->name)

@section('styles')
<style>
    .page-header { margin-bottom: 2rem; }
    .page-header h1 { font-size: 2rem; font-weight: 700; margin-bottom: 0.25rem; }
    .page-header p { color: #64748b; }
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .summary-card {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 0.75rem;
        padding: 1.25rem 1.5rem;
    }
    .summary-card .label { font-size: 0.8rem; color: #64748b; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.4rem; }
    .summary-card .value { font-size: 1.6rem; font-weight: 700; line-height: 1; }
    .summary-card .value.green { color: #16a34a; }
    .summary-card .value.red { color: #dc2626; }
    .summary-card .value.amber { color: #d97706; }
    .back-link {
        display: inline-flex; align-items: center; gap: 0.35rem;
        color: #6366f1; font-size: 0.88rem; font-weight: 500;
        text-decoration: none; margin-bottom: 1.25rem;
    }
    .back-link:hover { text-decoration: underline; }
    .mora-input {
        width: 90px; padding: 0.35rem 0.5rem;
        border: 1px solid #e2e8f0; border-radius: 6px;
        font-size: 0.85rem; text-align: right;
    }
    .mora-input:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 2px rgba(99,102,241,0.1); }
    .btn-save-mora {
        padding: 0.35rem 0.85rem; background: #6366f1; color: white;
        border: none; border-radius: 6px; font-size: 0.8rem;
        font-weight: 500; cursor: pointer; transition: background .15s;
    }
    .btn-save-mora:hover { background: #4f46e5; }
    .btn-save-mora:disabled { background: #cbd5e1; cursor: not-allowed; }
    .mora-form { display: flex; align-items: center; gap: 0.5rem; }
</style>
@endsection

@section('content')
    <a href="{{ route('fundraising.recaudaciones', ['type' => $type]) }}" class="back-link">
        &#8592; Volver a recaudaciones
    </a>

    <div class="page-header">
        <h1>
            <x-avatar :name="$user->name" />
            {{ $user->name }}
        </h1>
        <p>Registros de cobros &mdash; tipo: <strong>{{ ucfirst($type) }}</strong></p>
    </div>

    @php
        $totalBase     = $charges->sum(fn($c) => $c->baseAmount);
        $totalMora     = $charges->sum(fn($c) => $c->penaltyAmount);
        $totalPagado   = $charges->sum(fn($c) => $c->paidAmount);
        $totalDebe     = max(0, ($totalBase + $totalMora) - $totalPagado);
    @endphp

    <div class="summary-grid">
        <div class="summary-card">
            <div class="label">Total Acumulado</div>
            <div class="value green">${{ number_format($totalBase, 2) }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Total Mora</div>
            <div class="value red">${{ number_format($totalMora, 2) }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Total Pagado</div>
            <div class="value green">${{ number_format($totalPagado, 2) }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Total que Debe</div>
            <div class="value {{ $totalDebe > 0 ? 'amber' : 'green' }}">${{ number_format($totalDebe, 2) }}</div>
        </div>
    </div>

    <x-table.table
        :headers="['ID', 'Fecha cobro', 'Base', 'Mora actual', 'Pagado', 'Total debe', 'Estado', 'Editar mora']"
        title="Cargos del participante"
    >
        @forelse($charges as $charge)
        <tr id="charge-row-{{ $charge->id }}">
            <td style="color: #94a3b8; font-size: 0.82rem;">{{ $charge->id }}</td>
            <td>{{ \Carbon\Carbon::parse($charge->chargeDate)->format('d/m/Y') }}</td>
            <td>${{ number_format($charge->baseAmount, 2) }}</td>
            <td>
                @if($charge->penaltyAmount > 0)
                    <span style="color: #dc2626; font-weight: 600;">${{ number_format($charge->penaltyAmount, 2) }}</span>
                @else
                    <span style="color: #94a3b8;">$0.00</span>
                @endif
            </td>
            <td>${{ number_format($charge->paidAmount, 2) }}</td>
            <td style="font-weight: 600;">${{ number_format($charge->baseAmount + $charge->penaltyAmount, 2) }}</td>
            <td>
                @if($charge->isFullyPaid)
                    <x-badge color="green">Pagado</x-badge>
                @elseif($charge->paidAmount > 0)
                    <x-badge color="amber">Parcial</x-badge>
                @else
                    <x-badge color="red">Pendiente</x-badge>
                @endif
            </td>
            <td>
                @if(!$charge->isFullyPaid)
                <div class="mora-form">
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        class="mora-input"
                        id="mora-{{ $charge->id }}"
                        value="{{ number_format($charge->penaltyAmount, 2, '.', '') }}"
                    >
                    <button
                        class="btn-save-mora"
                        onclick="saveMora({{ $charge->id }}, '{{ $type }}')"
                    >
                        Guardar
                    </button>
                </div>
                @else
                    <span style="color: #94a3b8; font-size: 0.82rem;">Pagado</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8">
                <x-empty-state message="No hay cargos registrados para este participante." />
            </td>
        </tr>
        @endforelse
    </x-table.table>
@endsection

@section('scripts')
<script>
    async function saveMora(chargeId, type) {
        const input = document.getElementById('mora-' + chargeId);
        const amount = parseFloat(input.value);

        if (isNaN(amount) || amount < 0) {
            alert('Ingrese un valor de mora valido (0 o mayor)');
            return;
        }

        const btn = input.nextElementSibling;
        btn.disabled = true;
        btn.textContent = 'Guardando...';

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
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'No se pudo actualizar la mora'));
                btn.disabled = false;
                btn.textContent = 'Guardar';
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al actualizar la mora');
            btn.disabled = false;
            btn.textContent = 'Guardar';
        }
    }
</script>
@endsection
