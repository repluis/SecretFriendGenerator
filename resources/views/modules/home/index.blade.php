<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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

        .login-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1.5rem;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            text-decoration: none;
            transition: all 0.2s;
            margin-top: 1rem;
        }

        .login-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.35);
        }

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
            <a href="{{ route('login') }}" class="login-btn">🔐 Iniciar Sesi&oacute;n</a>
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
</body>
</html>
