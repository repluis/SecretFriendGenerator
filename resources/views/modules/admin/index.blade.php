@extends('layouts.app')

@section('title', 'Panel de Administración')

@section('styles')
<style>
    .admin-header {
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .admin-header h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.5rem;
    }

    .admin-header p {
        color: #64748b;
        font-size: 0.95rem;
    }

    .admin-table-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }

    .admin-table {
        width: 100%;
        border-collapse: collapse;
    }

    .admin-table thead th {
        text-align: left;
        padding: 0.85rem 1.25rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    .admin-table tbody td {
        padding: 1rem 1.25rem;
        font-size: 0.9rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .admin-table tbody tr:last-child td {
        border-bottom: none;
    }

    .admin-table tbody tr:hover {
        background: #fafbfe;
    }

    .user-cell {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .user-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 700;
        color: white;
        flex-shrink: 0;
    }

    .user-info {
        display: flex;
        flex-direction: column;
    }

    .user-name {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.9rem;
    }

    .user-email {
        font-size: 0.8rem;
        color: #94a3b8;
    }

    .editable-field {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .field-value {
        color: #334155;
        font-weight: 500;
    }

    .btn-edit-icon {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: background 0.15s;
        font-size: 0.9rem;
        color: #6366f1;
    }

    .btn-edit-icon:hover {
        background: #f1f5f9;
    }

    .edit-form {
        display: none;
        align-items: center;
        gap: 0.4rem;
    }

    .edit-form.active {
        display: flex;
    }

    .edit-input {
        border: 1px solid #6366f1;
        border-radius: 6px;
        padding: 0.35rem 0.65rem;
        font-size: 0.85rem;
        font-family: inherit;
        width: 200px;
        outline: none;
        color: #1e293b;
    }

    .btn-save, .btn-cancel {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 4px;
        font-size: 0.9rem;
    }

    .btn-save {
        color: #16a34a;
    }

    .btn-cancel {
        color: #94a3b8;
    }

    .btn-save:hover, .btn-cancel:hover {
        background: #f1f5f9;
    }

    .role-selector {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .role-checkboxes {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .role-checkbox-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        padding: 0.35rem 0.5rem;
        border-radius: 6px;
        transition: background 0.15s;
    }

    .role-checkbox-label:hover {
        background: #f8fafc;
    }

    .role-checkbox-input {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #6366f1;
    }

    .role-checkbox-text {
        font-size: 0.85rem;
        font-weight: 600;
        color: #334155;
    }

    .badge-count {
        background: #f1f5f9;
        color: #64748b;
        padding: 0.25rem 0.65rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-system {
        background: #dbeafe;
        color: #1e40af;
        padding: 0.25rem 0.65rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-custom {
        background: #fef3c7;
        color: #92400e;
        padding: 0.25rem 0.65rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-all-permissions {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: white;
        padding: 0.3rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        border: 1px solid #d97706;
    }

    .btn-add-role {
        background: #6366f1;
        color: white;
        border: none;
    }

    .btn-add-role:hover {
        background: #4f46e5;
    }

    .btn-delete-role {
        background: #fee2e2;
        border: 1px solid #fca5a5;
        color: #dc2626;
    }

    .btn-delete-role:hover {
        background: #fecaca;
        border-color: #f87171;
    }

    /* Modal */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        width: 100%;
        max-width: 450px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .modal-header {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1.5rem;
    }

    .modal-body {
        margin-bottom: 1.5rem;
    }

    .modal-label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 0.5rem;
    }

    .modal-input {
        width: 100%;
        padding: 0.65rem 0.85rem;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.9rem;
        font-family: inherit;
        color: #1e293b;
        outline: none;
        transition: border-color 0.15s;
    }

    .modal-input:focus {
        border-color: #6366f1;
    }

    .modal-hint {
        font-size: 0.75rem;
        color: #94a3b8;
        margin-top: 0.35rem;
    }

    .modal-footer {
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
    }

    .btn-modal {
        padding: 0.55rem 1.25rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.15s;
        border: none;
    }

    .btn-modal-cancel {
        background: #f1f5f9;
        color: #64748b;
    }

    .btn-modal-cancel:hover {
        background: #e2e8f0;
    }

    .btn-modal-submit {
        background: #6366f1;
        color: white;
    }

    .btn-modal-submit:hover {
        background: #4f46e5;
    }

    .btn-action {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.85rem;
        border: none;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.15s;
    }

    .btn-reset-pwd {
        background: #fff7ed;
        border: 1px solid #fed7aa;
        color: #c2410c;
    }

    .btn-reset-pwd:hover {
        background: #ffedd5;
        border-color: #fb923c;
    }

    .toast {
        position: fixed;
        bottom: 1.5rem;
        right: 1.5rem;
        padding: 0.85rem 1.35rem;
        border-radius: 10px;
        font-size: 0.875rem;
        font-weight: 500;
        color: white;
        z-index: 9999;
        opacity: 0;
        transform: translateY(8px);
        transition: all 0.25s;
        pointer-events: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .toast.show {
        opacity: 1;
        transform: translateY(0);
    }

    .toast.success {
        background: #16a34a;
    }

    .toast.error {
        background: #dc2626;
    }
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 2rem 1.5rem;">
    <div class="admin-header">
        <h1>👑 Panel de Administración</h1>
        <p>Gestión completa de usuarios y roles del sistema.</p>
    </div>

    <!-- Tabla de Roles -->
    <div class="admin-table-card" style="margin-bottom: 2.5rem;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 1.1rem; font-weight: 600; color: #1e293b; margin: 0;">🏷️ Roles del Sistema</h2>
            <button class="btn-action btn-add-role" onclick="openAddRoleModal()">
                ➕ Agregar Rol
            </button>
        </div>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre del Rol</th>
                    <th>Usuarios Asignados</th>
                    <th>Permisos (Pestañas)</th>
                    <th>Tipo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                <tr>
                    <td style="color: #94a3b8; font-size: 0.85rem;">{{ $role->id }}</td>
                    <td>
                        <span style="font-weight: 600; color: #1e293b;">
                            @if($role->name === 'admin')
                                👑 Administrador
                            @elseif($role->name === 'finance')
                                💰 Finanzas
                            @elseif($role->name === 'user')
                                👤 Usuario
                            @else
                                🏷️ {{ ucfirst($role->name) }}
                            @endif
                        </span>
                        <span style="color: #94a3b8; font-size: 0.8rem; margin-left: 0.5rem;">({{ $role->name }})</span>
                    </td>
                    <td>
                        <span class="badge badge-count">{{ $role->users()->count() }} usuario(s)</span>
                    </td>
                    <td>
                        @if($role->name === 'admin')
                            <span class="badge badge-all-permissions">✨ Acceso Total</span>
                        @else
                            <div class="permissions-checkboxes" style="display: flex; flex-direction: column; gap: 0.4rem;">
                                @php
                                    $availablePermissions = [
                                        'dashboard' => '📊 Dashboard',
                                        'juego' => '🎮 Juego',
                                        'pagos' => '💳 Pagos',
                                        'recaudaciones' => '💰 Recaudaciones',
                                        'usuarios' => '👥 Usuarios',
                                        'admin' => '👑 Admin'
                                    ];
                                    $rolePermissions = $role->permissions ?? [];
                                @endphp
                                @foreach($availablePermissions as $permKey => $permLabel)
                                    <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.8rem; cursor: pointer;">
                                        <input 
                                            type="checkbox" 
                                            class="permission-checkbox"
                                            data-role-id="{{ $role->id }}"
                                            value="{{ $permKey }}"
                                            {{ in_array($permKey, $rolePermissions) ? 'checked' : '' }}
                                            onchange="updatePermissions({{ $role->id }})"
                                            style="width: 16px; height: 16px; cursor: pointer; accent-color: #6366f1;"
                                        >
                                        <span>{{ $permLabel }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </td>
                    <td>
                        @if(in_array($role->name, ['admin', 'finance', 'user']))
                            <span class="badge badge-system">Sistema</span>
                        @else
                            <span class="badge badge-custom">Personalizado</span>
                        @endif
                    </td>
                    <td>
                        @if(!in_array($role->name, ['admin', 'finance', 'user']))
                            <button class="btn-action btn-delete-role" onclick="deleteRole({{ $role->id }}, '{{ addslashes($role->name) }}')">
                                🗑️ Eliminar
                            </button>
                        @else
                            <span style="color: #94a3b8; font-size: 0.8rem; font-style: italic;">Protegido</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Tabla de Usuarios -->
    <div class="admin-table-card">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0;">
            <h2 style="font-size: 1.1rem; font-weight: 600; color: #1e293b; margin: 0;">👥 Gestión de Usuarios</h2>
        </div>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Roles</th>
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
                        <div class="editable-field">
                            <div class="user-cell" id="name-display-{{ $user->id }}">
                                <div class="user-avatar" style="background: {{ $colors[$loop->index % count($colors)] }};">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div class="user-info">
                                    <span class="user-name">{{ $user->name }}</span>
                                </div>
                                <button class="btn-edit-icon" onclick="editName({{ $user->id }}, '{{ addslashes($user->name) }}')" title="Editar nombre">✏️</button>
                            </div>
                            <div class="edit-form" id="name-edit-{{ $user->id }}">
                                <input type="text" class="edit-input" id="name-input-{{ $user->id }}" value="{{ $user->name }}" maxlength="255">
                                <button class="btn-save" onclick="saveName({{ $user->id }})" title="Guardar">✅</button>
                                <button class="btn-cancel" onclick="cancelEditName({{ $user->id }}, '{{ addslashes($user->name) }}')" title="Cancelar">✖️</button>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="editable-field">
                            <span class="field-value" id="email-display-{{ $user->id }}">{{ $user->email }}</span>
                            <button class="btn-edit-icon" onclick="editEmail({{ $user->id }}, '{{ addslashes($user->email) }}')" title="Editar email">✏️</button>
                            <div class="edit-form" id="email-edit-{{ $user->id }}">
                                <input type="email" class="edit-input" id="email-input-{{ $user->id }}" value="{{ $user->email }}" maxlength="255">
                                <button class="btn-save" onclick="saveEmail({{ $user->id }})" title="Guardar">✅</button>
                                <button class="btn-cancel" onclick="cancelEditEmail({{ $user->id }}, '{{ addslashes($user->email) }}')" title="Cancelar">✖️</button>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="role-checkboxes">
                            @foreach($roles as $role)
                                <label class="role-checkbox-label">
                                    <input 
                                        type="checkbox" 
                                        class="role-checkbox-input"
                                        data-user-id="{{ $user->id }}"
                                        value="{{ $role->id }}"
                                        {{ $user->roles->contains($role->id) ? 'checked' : '' }}
                                        onchange="updateRoles({{ $user->id }})"
                                    >
                                    <span class="role-checkbox-text">
                                        @if($role->name === 'admin')
                                            👑 Admin
                                        @elseif($role->name === 'finance')
                                            💰 Finance
                                        @elseif($role->name === 'user')
                                            👤 User
                                        @else
                                            🏷️ {{ ucfirst($role->name) }}
                                        @endif
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </td>
                    <td>
                        <button class="btn-action btn-reset-pwd" onclick="resetPassword({{ $user->id }}, '{{ addslashes($user->name) }}')">
                            🔑 Restablecer contraseña
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 2.5rem; color: #94a3b8;">
                        No hay usuarios registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para agregar rol -->
<div class="modal-overlay" id="addRoleModal">
    <div class="modal-content">
        <div class="modal-header">➕ Agregar Nuevo Rol</div>
        <div class="modal-body">
            <label class="modal-label">Nombre del Rol</label>
            <input 
                type="text" 
                id="newRoleName" 
                class="modal-input" 
                placeholder="Ej: moderator, editor, viewer"
                maxlength="50"
            >
            <div class="modal-hint">Solo letras minúsculas y guiones bajos. Ej: super_admin</div>
        </div>
        <div class="modal-footer">
            <button class="btn-modal btn-modal-cancel" onclick="closeAddRoleModal()">Cancelar</button>
            <button class="btn-modal btn-modal-submit" onclick="createRole()">Crear Rol</button>
        </div>
    </div>
</div>

<div class="toast" id="toast"></div>
@endsection

@section('scripts')
<script>
// Editar nombre
function editName(userId, currentName) {
    document.getElementById('name-display-' + userId).style.display = 'none';
    document.getElementById('name-edit-' + userId).classList.add('active');
    document.getElementById('name-input-' + userId).focus();
}

function cancelEditName(userId, originalName) {
    document.getElementById('name-edit-' + userId).classList.remove('active');
    document.getElementById('name-display-' + userId).style.display = 'flex';
    document.getElementById('name-input-' + userId).value = originalName;
}

async function saveName(userId) {
    const value = document.getElementById('name-input-' + userId).value.trim();
    if (!value) {
        showToast('El nombre no puede estar vacío.', 'error');
        return;
    }

    try {
        const res = await fetch('/api/admin/users/' + userId + '/name', {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ name: value }),
        });
        const json = await res.json();

        if (!json.success) {
            showToast(json.message || 'Error al guardar.', 'error');
            return;
        }

        // Actualizar UI
        const nameSpan = document.querySelector('#name-display-' + userId + ' .user-name');
        nameSpan.textContent = value;
        
        cancelEditName(userId, value);
        showToast('Nombre actualizado correctamente.', 'success');
    } catch {
        showToast('Error de conexión.', 'error');
    }
}

// Editar email
function editEmail(userId, currentEmail) {
    document.getElementById('email-display-' + userId).style.display = 'none';
    document.getElementById('email-edit-' + userId).classList.add('active');
    document.getElementById('email-input-' + userId).focus();
}

function cancelEditEmail(userId, originalEmail) {
    document.getElementById('email-edit-' + userId).classList.remove('active');
    document.getElementById('email-display-' + userId).style.display = 'inline';
    document.getElementById('email-input-' + userId).value = originalEmail;
}

async function saveEmail(userId) {
    const value = document.getElementById('email-input-' + userId).value.trim();
    if (!value) {
        showToast('El email no puede estar vacío.', 'error');
        return;
    }

    try {
        const res = await fetch('/api/admin/users/' + userId + '/email', {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ email: value }),
        });
        const json = await res.json();

        if (!json.success) {
            showToast(json.message || 'Error al guardar.', 'error');
            return;
        }

        document.getElementById('email-display-' + userId).textContent = value;
        cancelEditEmail(userId, value);
        showToast('Email actualizado correctamente.', 'success');
    } catch {
        showToast('Error de conexión.', 'error');
    }
}

// Actualizar roles (múltiples)
async function updateRoles(userId) {
    // Buscar todos los checkboxes de roles para este usuario usando data-user-id
    const checkboxes = document.querySelectorAll(`.role-checkbox-input[data-user-id="${userId}"]:checked`);
    const roleIds = Array.from(checkboxes).map(cb => parseInt(cb.value));

    console.log('User ID:', userId);
    console.log('Selected role IDs:', roleIds);

    if (roleIds.length === 0) {
        showToast('Debe seleccionar al menos un rol.', 'error');
        return;
    }

    try {
        const res = await fetch('/api/admin/users/' + userId + '/roles', {
            method: 'PATCH',
            headers: { 
                'Content-Type': 'application/json', 
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ role_ids: roleIds }),
        });
        
        console.log('Response status:', res.status);
        
        // Obtener el texto de la respuesta primero
        const responseText = await res.text();
        console.log('Response text:', responseText);
        
        // Intentar parsear como JSON
        let json;
        try {
            json = JSON.parse(responseText);
            console.log('Response JSON:', json);
        } catch (parseError) {
            console.error('Error al parsear JSON:', parseError);
            console.error('Respuesta HTML/texto:', responseText.substring(0, 500));
            showToast('Error del servidor. Ver consola para detalles.', 'error');
            return;
        }

        if (!json.success) {
            showToast(json.message || 'Error al actualizar roles.', 'error');
            console.error('Error del servidor:', json);
            return;
        }

        showToast('Roles actualizados correctamente.', 'success');
        setTimeout(() => location.reload(), 1500);
    } catch (error) {
        console.error('Error completo:', error);
        console.error('Stack trace:', error.stack);
        showToast('Error de conexión: ' + error.message, 'error');
    }
}

// Modal de agregar rol
function openAddRoleModal() {
    document.getElementById('addRoleModal').classList.add('active');
    document.getElementById('newRoleName').value = '';
    document.getElementById('newRoleName').focus();
}

function closeAddRoleModal() {
    document.getElementById('addRoleModal').classList.remove('active');
}

// Crear rol
async function createRole() {
    const name = document.getElementById('newRoleName').value.trim();
    
    if (!name) {
        showToast('El nombre del rol es obligatorio.', 'error');
        return;
    }

    if (!/^[a-z_]+$/.test(name)) {
        showToast('Solo letras minúsculas y guiones bajos.', 'error');
        return;
    }

    try {
        const res = await fetch('/api/admin/roles', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ name: name }),
        });
        const json = await res.json();

        if (!json.success) {
            showToast(json.message || 'Error al crear rol.', 'error');
            return;
        }

        showToast('Rol creado correctamente.', 'success');
        closeAddRoleModal();
        setTimeout(() => location.reload(), 1000);
    } catch {
        showToast('Error de conexión.', 'error');
    }
}

// Eliminar rol
async function deleteRole(roleId, roleName) {
    if (!confirm(`¿Eliminar el rol "${roleName}"?\n\nLos usuarios con este rol lo perderán.`)) return;

    try {
        const res = await fetch('/api/admin/roles/' + roleId, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken },
        });
        const json = await res.json();

        if (!json.success) {
            showToast(json.message || 'Error al eliminar rol.', 'error');
            return;
        }

        showToast(json.message, 'success');
        setTimeout(() => location.reload(), 1000);
    } catch {
        showToast('Error de conexión.', 'error');
    }
}

// Actualizar permisos de un rol
async function updatePermissions(roleId) {
    const checkboxes = document.querySelectorAll(`.permission-checkbox[data-role-id="${roleId}"]:checked`);
    const permissions = Array.from(checkboxes).map(cb => cb.value);

    console.log('Role ID:', roleId);
    console.log('Selected permissions:', permissions);

    if (permissions.length === 0) {
        showToast('Debe seleccionar al menos un permiso.', 'error');
        return;
    }

    try {
        const res = await fetch('/api/admin/roles/' + roleId + '/permissions', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ permissions: permissions }),
        });

        const responseText = await res.text();
        console.log('Response text:', responseText);

        let json;
        try {
            json = JSON.parse(responseText);
            console.log('Response JSON:', json);
        } catch (parseError) {
            console.error('Error al parsear JSON:', parseError);
            console.error('Respuesta:', responseText.substring(0, 500));
            showToast('Error del servidor. Ver consola.', 'error');
            return;
        }

        if (!json.success) {
            showToast(json.message || 'Error al actualizar permisos.', 'error');
            return;
        }

        showToast('Permisos actualizados correctamente.', 'success');
    } catch (error) {
        console.error('Error:', error);
        showToast('Error de conexión: ' + error.message, 'error');
    }
}

// Cerrar modal al hacer click fuera
document.addEventListener('click', function(e) {
    const modal = document.getElementById('addRoleModal');
    if (e.target === modal) {
        closeAddRoleModal();
    }
});

// Restablecer contraseña
async function resetPassword(userId, userName) {
    if (!confirm(`¿Restablecer la contraseña de "${userName}"?\n\nLa nueva contraseña será: ${userName}`)) return;

    try {
        const res = await fetch('/api/admin/users/' + userId + '/reset-password', {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': csrfToken },
        });
        const json = await res.json();
        showToast(json.success ? json.message : 'Error al restablecer.', json.success ? 'success' : 'error');
    } catch {
        showToast('Error de conexión.', 'error');
    }
}

// Toast notification
function showToast(msg, type = 'success') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'toast ' + type;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3500);
}
</script>
@endsection
