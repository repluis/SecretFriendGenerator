<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SecretFriend')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
        }

        /* Navbar */
        .navbar {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .navbar-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .navbar-brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: #6366f1;
            text-decoration: none;
        }

        .navbar-links { display: flex; gap: 1.5rem; }

        .navbar-links a {
            color: #64748b;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.2s;
        }

        .navbar-links a:hover,
        .navbar-links a.active { color: #6366f1; }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2.5rem 1.5rem;
        }

        /* Buttons */
        .btn {
            display: inline-flex; align-items: center; gap: 0.35rem;
            padding: 0.45rem 1rem; border-radius: 8px;
            font-size: 0.82rem; font-weight: 500;
            border: none; cursor: pointer; transition: background 0.2s;
        }

        .btn-primary { background: #6366f1; color: white; }
        .btn-primary:hover { background: #4f46e5; }
        .btn-sm { padding: 0.3rem 0.7rem; font-size: 0.78rem; }
        .btn-ghost { background: transparent; color: #64748b; }
        .btn-ghost:hover { background: #f1f5f9; }
        .btn-danger { background: transparent; color: #dc2626; }
        .btn-danger:hover { background: #fee2e2; }
        .btn-success { background: transparent; color: #16a34a; }
        .btn-success:hover { background: #dcfce7; }

        .btn-pay {
            display: inline-flex; align-items: center; gap: 0.35rem;
            padding: 0.4rem 1rem; background: #6366f1; color: white;
            border: none; border-radius: 8px; font-size: 0.8rem;
            font-weight: 500; cursor: pointer; transition: background 0.2s;
        }

        .btn-pay:hover { background: #4f46e5; }
        .btn-pay:disabled { background: #cbd5e1; cursor: not-allowed; }

        .btn-toggle {
            display: inline-flex; align-items: center; gap: 0.35rem;
            padding: 0.35rem 0.85rem; border: none; border-radius: 8px;
            font-size: 0.78rem; font-weight: 500; cursor: pointer; transition: background 0.2s;
        }

        .btn-toggle.active { background: #fee2e2; color: #dc2626; }
        .btn-toggle.active:hover { background: #fecaca; }
        .btn-toggle.inactive { background: #dcfce7; color: #16a34a; }
        .btn-toggle.inactive:hover { background: #bbf7d0; }

        .btn-cancel {
            padding: 0.5rem 1.1rem; border-radius: 8px;
            font-size: 0.88rem; font-weight: 500;
            background: #f1f5f9; color: #475569;
            border: none; cursor: pointer;
        }

        .btn-cancel:hover { background: #e2e8f0; }

        .btn-save {
            padding: 0.5rem 1.1rem; border-radius: 8px;
            font-size: 0.88rem; font-weight: 500;
            background: #6366f1; color: white;
            border: none; cursor: pointer;
        }

        .btn-save:hover { background: #4f46e5; }

        /* Tables */
        .table-section {
            background: white;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            margin-bottom: 2.5rem;
        }

        .table-toolbar {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .table-toolbar h3 { font-size: 1.05rem; font-weight: 600; }

        .table-header {
            padding: 1.5rem 2rem 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .table-header h3 { font-size: 1.1rem; font-weight: 600; }

        table { width: 100%; border-collapse: collapse; }

        thead th {
            text-align: left; padding: 0.75rem 1.5rem;
            font-size: 0.75rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.05em;
            color: #64748b; background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        tbody td {
            padding: 0.85rem 1.5rem; font-size: 0.9rem;
            border-bottom: 1px solid #f1f5f9;
        }

        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover { background: #f8fafc; }

        /* Avatar */
        .avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: #ede9fe; color: #6366f1;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 0.75rem; font-weight: 600;
            margin-right: 0.75rem; vertical-align: middle;
        }

        /* Badges */
        .badge {
            display: inline-block; padding: 0.15rem 0.55rem;
            border-radius: 999px; font-size: 0.72rem; font-weight: 600;
        }

        .badge-green { background: #dcfce7; color: #16a34a; }
        .badge-red { background: #fee2e2; color: #dc2626; }
        .badge-amber { background: #fef3c7; color: #d97706; }
        .badge-blue { background: #dbeafe; color: #2563eb; }
        .badge-slate { background: #f1f5f9; color: #64748b; }

        /* Summary / Stat cards */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2.5rem;
        }

        .summary-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
        }

        .summary-card .label {
            font-size: 0.8rem; font-weight: 500; color: #64748b;
            text-transform: uppercase; letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .summary-card .value { font-size: 1.75rem; font-weight: 700; }

        .summary-card .value.green { color: #16a34a; }
        .summary-card .value.blue { color: #6366f1; }
        .summary-card .value.amber { color: #d97706; }
        .summary-card .value.red { color: #dc2626; }
        .summary-card .value.slate { color: #475569; }

        .summary-card .detail {
            font-size: 0.8rem; color: #94a3b8; margin-top: 0.25rem;
        }

        /* Stat cards (dashboard variant) */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.25rem;
            border: 1px solid #e2e8f0;
            text-align: center;
        }

        .stat-card .stat-value { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.15rem; }
        .stat-card .stat-label { font-size: 0.8rem; color: #64748b; font-weight: 500; text-transform: uppercase; letter-spacing: 0.04em; }

        .stat-value.indigo { color: #6366f1; }
        .stat-value.green { color: #16a34a; }
        .stat-value.amber { color: #d97706; }
        .stat-value.red { color: #dc2626; }
        .stat-value.slate { color: #475569; }

        /* Forms */
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; font-size: 0.82rem; font-weight: 500; color: #475569; margin-bottom: 0.35rem; }

        .form-input {
            width: 100%; padding: 0.6rem 0.85rem;
            border: 1px solid #e2e8f0; border-radius: 8px;
            font-size: 0.9rem; transition: border-color 0.2s;
        }

        .form-input:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }

        /* Modal */
        .modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.4); z-index: 100;
            align-items: center; justify-content: center;
        }

        .modal-overlay.open { display: flex; }

        .modal {
            background: white; border-radius: 16px;
            padding: 2rem; width: 100%; max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }

        .modal h3 { font-size: 1.1rem; font-weight: 600; margin-bottom: 1.25rem; }
        .modal-actions { display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 1.5rem; }

        /* Utilities */
        .actions-cell { display: flex; gap: 0.25rem; }
        .empty-state { text-align: center; padding: 2.5rem; color: #94a3b8; }
        .empty-state p { font-size: 0.95rem; }
        .amount-main { font-weight: 600; color: #1e293b; }
        .amount-penalty { font-size: 0.75rem; color: #dc2626; }

        .pay-form { display: flex; align-items: center; gap: 0.5rem; }
        .pay-form input {
            width: 80px; padding: 0.35rem 0.5rem;
            border: 1px solid #e2e8f0; border-radius: 6px;
            font-size: 0.8rem; text-align: right;
        }
        .pay-form input:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 2px rgba(99,102,241,0.1); }

        /* Progress bar */
        .progress-section {
            background: white; border-radius: 12px; padding: 2rem;
            border: 1px solid #e2e8f0; margin-bottom: 2.5rem;
        }
        .progress-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
        .progress-header h3 { font-size: 1rem; font-weight: 600; }
        .progress-header span { font-size: 0.9rem; color: #6366f1; font-weight: 600; }
        .progress-bar-bg { background: #e2e8f0; border-radius: 999px; height: 12px; overflow: hidden; }
        .progress-bar-fill { background: linear-gradient(90deg, #6366f1, #8b5cf6); height: 100%; border-radius: 999px; transition: width 0.5s ease; }

        /* Responsive */
        @media (max-width: 768px) {
            .table-section { overflow-x: auto; }
            table { min-width: 600px; }
            thead th, tbody td { padding: 0.65rem 1rem; }
        }

        @media (max-width: 640px) {
            .navbar-links { gap: 0.75rem; }
            .navbar-links a { font-size: 0.8rem; }
        }
    </style>
    @yield('styles')
</head>
<body>
    @include('layouts.partials.navbar', ['active' => $navbarActive ?? ''])

    <div class="container">
        @yield('content')
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>
    @yield('scripts')
</body>
</html>
