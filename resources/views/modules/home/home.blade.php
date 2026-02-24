@extends('layouts.app')

@section('title', 'Home - ' . $appName)

@section('content')
    <div class="hero">
        <h1>Dashboard</h1>
        <p>Panel de control del evento navide&ntilde;o. Accede r&aacute;pidamente a cada secci&oacute;n.</p>
    </div>

    <!-- Stats -->
    <div class="stats-row">
        <x-stat-card label="Usuarios" :value="$totalUsers" color="slate" />
        <x-stat-card label="Jugadores" :value="$totalPlayers" color="indigo" />
        <x-stat-card label="URLs" :value="$totalUrls" color="green" />
        <x-stat-card label="Juego" :value="$gameStarted ? 'Activo' : 'Inactivo'" color="amber" />
        <x-stat-card label="Deuda" :value="'$' . number_format($fundraisingPending, 2)" color="red" />
    </div>

    <!-- Users Table -->
    <x-table.table :headers="['Nombre', 'Email', 'Saldo', 'Estado', 'Acciones']" title="Usuarios ({{ $totalUsers }})">
        <x-slot:toolbar>
            <button class="btn btn-primary" onclick="openCreateModal()">+ Nuevo usuario</button>
        </x-slot:toolbar>

        @forelse($users as $user)
        <tr id="user-row-{{ $user->id }}">
            <td>
                <x-avatar :name="$user->name" />
                <span class="user-name">{{ $user->name }}</span>
            </td>
            <td>{{ $user->email }}</td>
            <td>
                @php $balance = $userBalances[$user->id] ?? 0; @endphp
                @if($balance < 0)
                    <span style="color: #dc2626; font-weight: 600;">-${{ number_format(abs($balance), 2) }}</span>
                @elseif($balance > 0)
                    <span style="color: #16a34a; font-weight: 600;">${{ number_format($balance, 2) }}</span>
                @else
                    <span style="color: #16a34a;">$0.00</span>
                @endif
            </td>
            <td>
                <x-badge :color="$user->active ? 'green' : 'red'">{{ $user->active ? 'Activo' : 'Inactivo' }}</x-badge>
            </td>
            <td>
                <div class="actions-cell">
                    <button class="btn btn-sm btn-ghost" onclick="openEditModal({{ $user->id }}, '{{ addslashes($user->name) }}')">Editar</button>
                    @if($user->active)
                        <button class="btn btn-sm btn-danger" onclick="toggleActive({{ $user->id }})">Desactivar</button>
                    @else
                        <button class="btn btn-sm btn-success" onclick="toggleActive({{ $user->id }})">Activar</button>
                    @endif
                </div>
            </td>
        </tr>
        @empty
        <tr id="empty-row">
            <td colspan="5">
                <x-empty-state message="No hay usuarios registrados." />
            </td>
        </tr>
        @endforelse
    </x-table.table>

    <!-- Modules -->
    <div class="modules-grid">
        <div class="module-card">
            <div class="module-card-header">
                <div class="module-icon game">&#127876;</div>
                <h3>Amigo Secreto</h3>
                <p>Gestiona las URLs, visualiza asignaciones y valida el juego.</p>
            </div>
            <div class="module-card-body">
                <div class="module-meta">
                    <div class="module-meta-item">
                        <span class="module-meta-dot {{ $gameStarted ? 'dot-green' : 'dot-amber' }}"></span>
                        {{ $gameStarted ? 'Iniciado' : 'No iniciado' }}
                    </div>
                    <div class="module-meta-item">
                        <span class="module-meta-dot dot-slate"></span>
                        {{ $totalUrls }} URL(s)
                    </div>
                </div>
            </div>
            <div class="module-card-footer">
                <div class="module-btn-group">
                    <a href="{{ route('juego') }}" class="module-btn primary">Ir al juego</a>
                    <a href="{{ route('configuracion') }}" class="module-btn secondary">Configurar</a>
                </div>
            </div>
        </div>

        <div class="module-card">
            <div class="module-card-header">
                <div class="module-icon finance">&#128176;</div>
                <h3>Recaudaciones</h3>
                <p>Cobros mensuales cada 15. Mora de $0.05 diarios por atraso.</p>
            </div>
            <div class="module-card-body">
                <div class="module-meta">
                    <div class="module-meta-item">
                        <span class="module-meta-dot {{ $fundraisingPending > 0 ? 'dot-amber' : 'dot-green' }}"></span>
                        ${{ number_format($fundraisingCollected, 2) }} recaudado
                    </div>
                    <div class="module-meta-item">
                        <span class="module-meta-dot dot-slate"></span>
                        {{ $fundraisingUsers }} usuario(s)
                    </div>
                </div>
            </div>
            <div class="module-card-footer">
                <div class="module-btn-group">
                    <a href="{{ route('fundraising.recaudaciones') }}" class="module-btn primary">Ver recaudaciones</a>
                    <button class="module-btn secondary" id="btnRunFundraising" onclick="runFundraisingManual()">Ejecutar cobro manual</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit User Modal -->
    <x-modal id="userModal" title="Nuevo usuario">
        <form id="userForm" onsubmit="handleSubmit(event)">
            <input type="hidden" id="userId" value="">
            <x-form.input id="userName" label="Nombre" placeholder="Nombre completo" :required="true" />
            <div id="emailGroup">
                <x-form.input id="userEmail" label="Email (opcional)" type="email" placeholder="correo@ejemplo.com" />
            </div>
            <div class="modal-actions">
                <x-form.button variant="cancel" onclick="closeModal()">Cancelar</x-form.button>
                <x-form.button variant="primary" type="submit" id="saveBtn">Guardar</x-form.button>
            </div>
        </form>
    </x-modal>
@endsection

@section('styles')
<style>
    .hero { text-align: center; margin-bottom: 3rem; }
    .hero h1 { font-size: 2.25rem; font-weight: 700; margin-bottom: 0.5rem; }
    .hero p { color: #64748b; font-size: 1.05rem; max-width: 500px; margin: 0 auto; }
    .stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1.25rem; margin-bottom: 3rem; }
    .modules-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; margin-bottom: 3rem; }
    .module-card { background: white; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; transition: box-shadow 0.2s, transform 0.2s; }
    .module-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.08); transform: translateY(-2px); }
    .module-card-header { padding: 1.75rem 1.75rem 0; }
    .module-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 1rem; }
    .module-icon.game { background: #ede9fe; }
    .module-icon.finance { background: #dcfce7; }
    .module-card h3 { font-size: 1.15rem; font-weight: 600; margin-bottom: 0.35rem; }
    .module-card p { color: #64748b; font-size: 0.88rem; line-height: 1.5; }
    .module-card-body { padding: 1.25rem 1.75rem; }
    .module-meta { display: flex; gap: 1.5rem; margin-bottom: 1rem; }
    .module-meta-item { display: flex; align-items: center; gap: 0.35rem; font-size: 0.82rem; color: #64748b; }
    .module-meta-dot { width: 8px; height: 8px; border-radius: 50%; }
    .dot-green { background: #16a34a; } .dot-amber { background: #d97706; } .dot-slate { background: #94a3b8; }
    .module-card-footer { padding: 0 1.75rem 1.75rem; }
    .module-btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; border-radius: 8px; font-size: 0.88rem; font-weight: 500; text-decoration: none; transition: background 0.2s; }
    .module-btn.primary { background: #6366f1; color: white; } .module-btn.primary:hover { background: #4f46e5; }
    .module-btn.secondary { background: #f1f5f9; color: #475569; } .module-btn.secondary:hover { background: #e2e8f0; }
    .module-btn-group { display: flex; gap: 0.75rem; flex-wrap: wrap; }
    @media (max-width: 768px) { .hero h1 { font-size: 1.75rem; } .modules-grid { grid-template-columns: 1fr; } }
    @media (max-width: 640px) { .stats-row { grid-template-columns: repeat(2, 1fr); } }
</style>
@endsection

@section('scripts')
<script>
    function openCreateModal() {
        document.getElementById('userModal-title').textContent = 'Nuevo usuario';
        document.getElementById('userId').value = '';
        document.getElementById('userName').value = '';
        document.getElementById('userEmail').value = '';
        document.getElementById('emailGroup').style.display = 'block';
        document.getElementById('userModal').classList.add('open');
        document.getElementById('userName').focus();
    }

    function openEditModal(id, name) {
        document.getElementById('userModal-title').textContent = 'Editar usuario';
        document.getElementById('userId').value = id;
        document.getElementById('userName').value = name;
        document.getElementById('emailGroup').style.display = 'none';
        document.getElementById('userModal').classList.add('open');
        document.getElementById('userName').focus();
    }

    function closeModal() {
        document.getElementById('userModal').classList.remove('open');
    }

    async function handleSubmit(e) {
        e.preventDefault();
        const id = document.getElementById('userId').value;
        const name = document.getElementById('userName').value.trim();
        const email = document.getElementById('userEmail').value.trim();
        if (!name) return;
        const saveBtn = document.getElementById('saveBtn');
        saveBtn.disabled = true;
        saveBtn.textContent = 'Guardando...';
        try {
            let url, method, body;
            if (id) { url = `/api/users/${id}`; method = 'PUT'; body = JSON.stringify({ name }); }
            else { url = '/api/users'; method = 'POST'; body = JSON.stringify({ name, email: email || undefined }); }
            const response = await fetch(url, { method, headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body });
            const data = await response.json();
            if (data.success) { closeModal(); location.reload(); }
            else { alert('Error: ' + (data.message || 'No se pudo guardar')); }
        } catch (error) { console.error('Error:', error); alert('Error al guardar el usuario'); }
        finally { saveBtn.disabled = false; saveBtn.textContent = 'Guardar'; }
    }

    async function toggleActive(id) {
        try {
            const response = await fetch(`/api/users/${id}/toggle-active`, { method: 'PATCH', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
            const data = await response.json();
            if (data.success) { location.reload(); } else { alert('Error: ' + (data.message || 'No se pudo cambiar el estado')); }
        } catch (error) { console.error('Error:', error); alert('Error al cambiar el estado del usuario'); }
    }

    async function runFundraisingManual() {
        const btn = document.getElementById('btnRunFundraising');
        if (!confirm('Esto creara cobros para la fecha de hoy y aplicara multas pendientes. Continuar?')) return;
        btn.disabled = true; btn.textContent = 'Ejecutando...';
        try {
            const response = await fetch('/api/fundraising/run-manual', { method: 'POST', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
            const data = await response.json();
            if (data.success) { alert(data.message); location.reload(); } else { alert('Error: ' + (data.message || 'No se pudo ejecutar')); }
        } catch (error) { console.error('Error:', error); alert('Error al ejecutar el cobro manual'); }
        finally { btn.disabled = false; btn.textContent = 'Ejecutar cobro manual'; }
    }

    document.getElementById('userModal').addEventListener('click', function(e) { if (e.target === this) closeModal(); });
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeModal(); });
</script>
@endsection
