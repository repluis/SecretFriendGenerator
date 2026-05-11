<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $appName }} - Resumen de Pagos</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #f0f4ff 0%, #e8ecf8 50%, #f5f3ff 100%);
            color: #1e293b;
        }

        .landing-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }

        .landing-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .landing-logo {
            width: 56px; height: 56px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin-bottom: 1rem;
            box-shadow: 0 6px 16px rgba(99, 102, 241, 0.25);
        }

        .landing-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.35rem;
        }

        .landing-header p {
            color: #64748b;
            font-size: 0.92rem;
        }

        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.6rem;
            margin-top: 1rem;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.25rem;
            border: none;
            border-radius: 10px;
            font-size: 0.88rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }

        .action-btn-primary {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: white;
        }

        .action-btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.35);
        }

        .action-btn-green {
            background: linear-gradient(135deg, #16a34a, #15803d);
            color: white;
        }

        .action-btn-green:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(22, 163, 74, 0.35);
        }

        .toast-container {
            position: fixed;
            bottom: 1.25rem;
            right: 1.25rem;
            z-index: 9999;
        }

        .toast {
            background: #1e293b;
            color: white;
            padding: 0.75rem 1.25rem;
            border-radius: 10px;
            font-size: 0.88rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            box-shadow: 0 4px 16px rgba(0,0,0,0.18);
            transform: translateX(120%);
            transition: transform 0.3s ease;
        }

        .toast.show { transform: translateX(0); }

        /* Table card */
        .table-card {
            background: white;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }

        .table-card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-card-header h2 {
            font-size: 1.1rem;
            font-weight: 600;
        }

        .public-table {
            width: 100%;
            border-collapse: collapse;
        }

        .public-table thead th {
            text-align: left;
            padding: 0.7rem 1.5rem;
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .public-table tbody td {
            padding: 0.8rem 1.5rem;
            font-size: 0.88rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .public-table tbody tr:last-child td { border-bottom: none; }
        .public-table tbody tr:hover { background: #fafbfe; }

        .user-name-cell {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-weight: 500;
        }

        .avatar-circle {
            width: 32px; height: 32px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem; font-weight: 600;
            color: white;
        }

        .text-green { color: #16a34a; font-weight: 600; }
        .text-red { color: #dc2626; font-weight: 600; }
        .text-amber { color: #d97706; font-weight: 600; }
        .text-muted { color: #94a3b8; }

        .footer-text {
            text-align: center;
            margin-top: 2rem;
            color: #94a3b8;
            font-size: 0.78rem;
        }

        /* Mobile responsive */
        @media (max-width: 640px) {
            .landing-header h1 { font-size: 1.4rem; }

            .public-table thead { display: none; }

            .public-table tbody tr {
                display: block;
                background: white;
                border: 1px solid #e2e8f0;
                border-radius: 10px;
                padding: 1rem;
                margin: 0 1rem 0.75rem;
            }

            .public-table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.3rem 0;
                border: none;
            }

            .public-table tbody td::before {
                content: attr(data-label);
                font-size: 0.72rem;
                font-weight: 600;
                color: #64748b;
                text-transform: uppercase;
            }

            .mobile-name::before { display: none; }
            .mobile-name { font-weight: 600; font-size: 0.95rem; padding-bottom: 0.4rem !important; border-bottom: 1px solid #f1f5f9; margin-bottom: 0.2rem; }

            .table-card { border-radius: 12px; overflow: visible; }
            .table-card-header { padding: 1rem; }
        }
    </style>
</head>
<body>
    <div class="landing-container">
        <div class="landing-header">
            <div class="landing-logo">🎄</div>
            <h1>{{ $appName }}</h1>
            <p>Resumen de pagos del evento navide&ntilde;o</p>
            <div class="action-buttons">
                <button onclick="runFundraisingManual()" class="action-btn action-btn-primary">
                    <span>▶️</span>
                    <span>Actualizar cobros y moras</span>
                </button>
                <button onclick="copyResumen()" id="btn-copy-resumen" class="action-btn action-btn-green">
                    <span>📋</span>
                    <span>Copiar resumen</span>
                </button>
            </div>
        </div>

        <div class="table-card">
            <div class="table-card-header">
                <h2>Estado de Pagos</h2>
            </div>
            <table class="public-table">
                <thead>
                    <tr>
                        <th>Participante</th>
                        <th>Pagado</th>
                        <th>Mora</th>
                        <th>Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    @php
                        $txBalance = $userTransactionBalances[$user['user_id']] ?? 0;
                        $saldo = $user['total_owed'] - $txBalance;
                    @endphp
                    <tr>
                        <td class="mobile-name" data-label="">
                            <div class="user-name-cell">
                                <div class="avatar-circle" style="background: {{ ['#6366f1','#8b5cf6','#a855f7','#ec4899','#f43f5e','#f97316','#eab308','#22c55e','#14b8a6','#06b6d4'][$loop->index % 10] }};">
                                    {{ strtoupper(substr($user['user_name'], 0, 2)) }}
                                </div>
                                {{ $user['user_name'] }}
                            </div>
                        </td>
                        <td data-label="Pagado">
                            <span class="{{ $txBalance > 0 ? 'text-green' : 'text-muted' }}">${{ number_format($txBalance, 2) }}</span>
                        </td>
                        <td data-label="Mora">
                            @if($user['total_penalty'] > 0)
                                <span class="text-red">${{ number_format($user['total_penalty'], 2) }}</span>
                            @else
                                <span class="text-muted">$0.00</span>
                            @endif
                        </td>
                        <td data-label="Saldo">
                            <span class="{{ $saldo <= 0 ? 'text-green' : 'text-amber' }}" style="font-weight: 700;">
                                ${{ number_format($saldo, 2) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 2rem; color: #94a3b8;">
                            No hay cobros registrados a&uacute;n.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <p class="footer-text">{{ $appName }} &copy; {{ date('Y') }}</p>
    </div>

    <div class="toast-container" id="toast-container"></div>

    <script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

    const resumenData = {
        totalRecaudado: {{ collect($userTransactionBalances)->sum() }},
        totalPendiente: {{ collect($users)->sum(fn($u) => max(0, $u['total_owed'] - ($userTransactionBalances[$u['user_id']] ?? 0))) }},
        users: [
            @foreach($users as $user)
            @php $txBal = $userTransactionBalances[$user['user_id']] ?? 0; $saldoUser = $user['total_owed'] - $txBal; @endphp
            { name: @json($user['user_name']), pago: {{ $txBal }}, debe: {{ max(0, $saldoUser) }} },
            @endforeach
        ]
    };

    function fmt(n) { return '$' + parseFloat(n).toFixed(2); }

    function showToast(message) {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.textContent = message;
        container.appendChild(toast);
        setTimeout(() => toast.classList.add('show'), 50);
        setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 300); }, 3000);
    }

    function copyResumen() {
        const morosos = resumenData.users.filter(u => u.debe > 0).sort((a, b) => b.debe - a.debe);
        const pagaron = resumenData.users.filter(u => u.debe <= 0).sort((a, b) => a.name.localeCompare(b.name));
        const today = new Date().toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
        const sep = '━━━━━━━━━━━━━━━━━━━━';
        let lines = ['📊 *Resumen de Recaudaciones*', '_' + today + '_', '', '💰 *Total Recaudado:* ' + fmt(resumenData.totalRecaudado), '⏳ *Pendiente:* ' + fmt(resumenData.totalPendiente)];
        if (morosos.length > 0) { lines.push('', sep, '🔴 *MOROSOS*', sep); morosos.forEach(u => lines.push('• ' + u.name + ' → pagó ' + fmt(u.pago) + ' | debe ' + fmt(u.debe))); }
        if (pagaron.length > 0) { lines.push('', sep, '✅ *YA PAGARON*', sep); pagaron.forEach(u => lines.push('• ' + u.name + ' → pagó ' + fmt(u.pago) + ' ✔')); }
        navigator.clipboard.writeText(lines.join('\n')).then(() => {
            const btn = document.getElementById('btn-copy-resumen');
            const orig = btn.innerHTML;
            btn.innerHTML = '<span>✅</span><span>Copiado!</span>';
            setTimeout(() => btn.innerHTML = orig, 2000);
        }).catch(() => showToast('No se pudo copiar al portapapeles'));
    }

    async function runFundraisingManual() {
        if (!confirm('Se crearán los cobros del mes si no existen, se sincronizarán los pagos y se calcularán las moras del día. ¿Continuar?')) return;
        try {
            const response = await fetch('/api/fundraising/run-manual', { method: 'POST', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
            const data = await response.json();
            if (data.success) { showToast(data.message); setTimeout(() => location.reload(), 1500); }
            else { showToast(data.message || 'Error al ejecutar'); }
        } catch (error) { showToast('Error de conexión'); }
    }
    </script>
</body>
</html>
