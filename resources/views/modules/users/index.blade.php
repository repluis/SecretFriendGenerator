@extends('layouts.app')

@section('title', 'Usuarios')

@section('styles')
<style>
    .page-header {
        margin-bottom: 2rem;
    }
    .page-header h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
    }
    .page-header p {
        color: #64748b;
        font-size: 0.9rem;
        margin-top: 0.25rem;
    }

    .users-table-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }

    .users-table {
        width: 100%;
        border-collapse: collapse;
    }

    .users-table thead th {
        text-align: left;
        padding: 0.75rem 1.25rem;
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    .users-table tbody td {
        padding: 0.85rem 1.25rem;
        font-size: 0.875rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .users-table tbody tr:last-child td {
        border-bottom: none;
    }

    .users-table tbody tr:hover {
        background: #fafbfe;
    }

    .user-name-cell {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        font-weight: 500;
    }

    .avatar-circle {
        width: 34px; height: 34px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.7rem; font-weight: 700;
        color: white;
        flex-shrink: 0;
    }

    /* Identification inline edit */
    .id-display {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .id-value {
        color: #334155;
        font-weight: 500;
        min-width: 80px;
    }

    .id-value.empty {
        color: #94a3b8;
        font-style: italic;
    }

    .id-edit-form {
        display: none;
        align-items: center;
        gap: 0.4rem;
    }

    .id-input {
        border: 1px solid #6366f1;
        border-radius: 6px;
        padding: 0.3rem 0.6rem;
        font-size: 0.85rem;
        font-family: inherit;
        width: 130px;
        outline: none;
        color: #1e293b;
    }

    .btn-icon {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.3rem;
        border-radius: 5px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: background 0.15s;
        font-size: 0.85rem;
    }

    .btn-icon:hover { background: #f1f5f9; }
    .btn-icon.edit { color: #6366f1; }
    .btn-icon.save { color: #16a34a; }
    .btn-icon.cancel { color: #94a3b8; }

    .btn-reset {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.75rem;
        background: #fff7ed;
        border: 1px solid #fed7aa;
        border-radius: 7px;
        color: #c2410c;
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.15s;
    }

    .btn-reset:hover {
        background: #ffedd5;
        border-color: #fb923c;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 0.2rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.72rem;
        font-weight: 600;
    }
    .badge-active { background: #dcfce7; color: #15803d; }
    .badge-inactive { background: #f1f5f9; color: #64748b; }
    .badge-admin { 
        background: linear-gradient(135deg, #fef3c7, #fde68a); 
        color: #92400e; 
        border: 1px solid #fbbf24;
    }
    .badge-finance {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
        border: 1px solid #10b981;
    }
    .badge-user { 
        background: #dbeafe; 
        color: #1e40af; 
        border: 1px solid #93c5fd;
    }
    .badge-empty { 
        background: #f1f5f9; 
        color: #94a3b8; 
        font-style: italic;
    }

    .toast {
        position: fixed;
        bottom: 1.5rem;
        right: 1.5rem;
        padding: 0.75rem 1.25rem;
        border-radius: 10px;
        font-size: 0.875rem;
        font-weight: 500;
        color: white;
        z-index: 9999;
        opacity: 0;
        transform: translateY(8px);
        transition: all 0.25s;
        pointer-events: none;
    }
    .toast.show { opacity: 1; transform: translateY(0); }
    .toast.success { background: #16a34a; }
    .toast.error { background: #dc2626; }
</style>
@endsection

@section('content')
    <div style="max-width: 900px; margin: 0 auto; padding: 2rem 1.5rem;">
        <div class="page-header">
            <h1>Usuarios</h1>
            <p>Gestión de identificaciones y contraseñas de los usuarios del sistema.</p>
        </div>

        <div class="users-table-card">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Identificación</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    @php
                        $colors = ['#6366f1','#8b5cf6','#a855f7','#ec4899','#f43f5e','#f97316','#eab308','#22c55e','#14b8a6','#06b6d4'];
                    @endphp
                    <tr>
                        <td>
                            <div class="user-name-cell">
                                <div class="avatar-circle" style="background: {{ $colors[$loop->index % count($colors)] }};">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                {{ $user->name }}
                            </div>
                        </td>
                        <td>
                            <div class="id-display" id="display-{{ $user->id }}">
                                <span class="id-value {{ $user->identification ? '' : 'empty' }}" id="id-val-{{ $user->id }}">
                                    {{ $user->identification ?? 'Sin identificación' }}
                                </span>
                                @if(Auth::user()->isAdmin())
                                    <button class="btn-icon edit" onclick="startEdit({{ $user->id }}, '{{ addslashes($user->identification ?? '') }}')" title="Editar">✏️</button>
                                @endif
                            </div>
                            @if(Auth::user()->isAdmin())
                                <div class="id-edit-form" id="edit-{{ $user->id }}">
                                    <input type="text" class="id-input" id="input-{{ $user->id }}"
                                        value="{{ $user->identification ?? '' }}"
                                        placeholder="Ej: CC-123456"
                                        maxlength="100">
                                    <button class="btn-icon save" onclick="saveIdentification({{ $user->id }})" title="Guardar">✅</button>
                                    <button class="btn-icon cancel" onclick="cancelEdit({{ $user->id }}, '{{ addslashes($user->identification ?? '') }}')" title="Cancelar">✖️</button>
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($user->roles->isNotEmpty())
                                @foreach($user->roles as $role)
                                    @if($role->name === 'admin')
                                        <span class="badge badge-admin">👑 Administrador</span>
                                    @elseif($role->name === 'finance')
                                        <span class="badge badge-finance">💰 Finanzas</span>
                                    @else
                                        <span class="badge badge-user">👤 Usuario</span>
                                    @endif
                                @endforeach
                            @else
                                <span class="badge badge-empty">Sin rol</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $user->active ? 'badge-active' : 'badge-inactive' }}">
                                {{ $user->active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td>
                            @if(Auth::user()->isAdmin())
                                <button class="btn-reset" onclick="resetPassword({{ $user->id }}, '{{ addslashes($user->name) }}')">
                                    🔑 Restablecer contraseña
                                </button>
                            @else
                                <span style="color: #94a3b8; font-size: 0.85rem; font-style: italic;">Solo administradores</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2.5rem; color: #94a3b8;">
                            No hay usuarios registrados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="toast" id="toast"></div>
@endsection

@section('scripts')
<script>
function startEdit(userId, currentVal) {
    document.getElementById('display-' + userId).style.display = 'none';
    const editRow = document.getElementById('edit-' + userId);
    editRow.style.display = 'flex';
    const input = document.getElementById('input-' + userId);
    input.value = currentVal;
    input.focus();
    input.addEventListener('keydown', function handler(e) {
        if (e.key === 'Enter') saveIdentification(userId);
        if (e.key === 'Escape') cancelEdit(userId, currentVal);
    }, { once: true });
}

function cancelEdit(userId, originalVal) {
    document.getElementById('edit-' + userId).style.display = 'none';
    document.getElementById('display-' + userId).style.display = 'flex';
}

async function saveIdentification(userId) {
    const value = document.getElementById('input-' + userId).value.trim();
    if (!value) { showToast('La identificación no puede estar vacía.', 'error'); return; }

    try {
        const res = await fetch('/api/users/' + userId + '/identification', {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ identification: value }),
        });
        const json = await res.json();

        if (!json.success) {
            showToast(json.message || 'Error al guardar.', 'error');
            return;
        }

        const span = document.getElementById('id-val-' + userId);
        span.textContent = value;
        span.classList.remove('empty');

        // Update edit button with new value
        const btn = document.querySelector('#display-' + userId + ' .btn-icon.edit');
        btn.setAttribute('onclick', `startEdit(${userId}, '${value.replace(/'/g, "\\'")}')`);

        cancelEdit(userId, value);
        showToast('Identificación actualizada.', 'success');
    } catch {
        showToast('Error de conexión.', 'error');
    }
}

async function resetPassword(userId, userName) {
    if (!confirm(`¿Restablecer la contraseña de "${userName}"?\nLa nueva contraseña será su nombre de usuario.`)) return;

    try {
        const res = await fetch('/api/users/' + userId + '/reset-password', {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': csrfToken },
        });
        const json = await res.json();
        showToast(json.success ? json.message : 'Error al restablecer.', json.success ? 'success' : 'error');
    } catch {
        showToast('Error de conexión.', 'error');
    }
}

function showToast(msg, type = 'success') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'toast ' + type;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
}
</script>
@endsection
