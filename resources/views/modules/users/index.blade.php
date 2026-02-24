@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
    <x-page-header 
        title="👥 Usuarios" 
        subtitle="Gestión completa de usuarios del sistema"
    />

    <x-table-container title="Lista de Usuarios ({{ $users->count() }})">
        <x-slot name="actions">
            @if(Auth::user()->isAdmin())
                <x-button variant="primary" icon="➕" onclick="openCreateModal()">
                    Nuevo usuario
                </x-button>
            @endif
        </x-slot>

        <table class="table">
            <thead>
                <tr>
                    <th style="width: 25%;">Usuario</th>
                    <th style="width: 18%;">Identificación</th>
                    <th style="width: 17%;">Roles</th>
                    <th style="width: 12%;">Estado</th>
                    <th style="width: 28%;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr id="user-row-{{ $user->id }}">
                        <td>
                            <div class="table-cell-user">
                                <x-avatar :name="$user->name" size="md" />
                                <div class="table-user-info">
                                    <span class="table-user-name">{{ $user->name }}</span>
                                    <span class="table-user-email">{{ $user->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.5rem;" id="display-{{ $user->id }}">
                                <span class="font-semibold" style="color: {{ $user->identification ? 'var(--color-slate-700)' : 'var(--color-slate-400)' }};" id="id-val-{{ $user->id }}">
                                    {{ $user->identification ?? 'Sin identificación' }}
                                </span>
                                @if(Auth::user()->isAdmin())
                                    <button 
                                        class="btn-icon btn-ghost-primary" 
                                        onclick="startEdit({{ $user->id }}, '{{ addslashes($user->identification ?? '') }}')" 
                                        title="Editar identificación"
                                    >
                                        ✏️
                                    </button>
                                @endif
                            </div>
                            @if(Auth::user()->isAdmin())
                                <div style="display: none; align-items: center; gap: 0.5rem;" id="edit-{{ $user->id }}">
                                    <input 
                                        type="text" 
                                        class="form-input" 
                                        id="input-{{ $user->id }}"
                                        value="{{ $user->identification ?? '' }}"
                                        placeholder="Ej: CC-123456"
                                        maxlength="100"
                                    >
                                    <button class="btn-icon btn-ghost-success" onclick="saveIdentification({{ $user->id }})" title="Guardar">✅</button>
                                    <button class="btn-icon btn-ghost" onclick="cancelEdit({{ $user->id }}, '{{ addslashes($user->identification ?? '') }}')" title="Cancelar">✖️</button>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; flex-wrap: wrap; gap: 0.375rem;">
                                @if($user->roles->isNotEmpty())
                                    @foreach($user->roles as $role)
                                        @if($role->name === 'admin')
                                            <x-badge color="warning">👑 Admin</x-badge>
                                        @elseif($role->name === 'finance')
                                            <x-badge color="success">💰 Finance</x-badge>
                                        @else
                                            <x-badge color="primary">👤 User</x-badge>
                                        @endif
                                    @endforeach
                                @else
                                    <x-badge color="slate">Sin rol</x-badge>
                                @endif
                            </div>
                        </td>
                        <td>
                            <x-badge :color="$user->active ? 'success' : 'slate'">
                                {{ $user->active ? '✅ Activo' : '⏸️ Inactivo' }}
                            </x-badge>
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                @if(Auth::user()->isAdmin())
                                    <x-button 
                                        variant="ghost-primary" 
                                        size="sm"
                                        icon="✏️"
                                        onclick="openEditModal({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                    >
                                        Editar
                                    </x-button>
                                    
                                    @if($user->active)
                                        <x-button 
                                            variant="ghost-danger" 
                                            size="sm"
                                            onclick="toggleActive({{ $user->id }})"
                                        >
                                            Desactivar
                                        </x-button>
                                    @else
                                        <x-button 
                                            variant="ghost-success" 
                                            size="sm"
                                            onclick="toggleActive({{ $user->id }})"
                                        >
                                            Activar
                                        </x-button>
                                    @endif
                                    
                                    <x-button 
                                        variant="ghost-warning" 
                                        size="sm"
                                        icon="🔑"
                                        onclick="resetPassword({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                    >
                                        Restablecer
                                    </x-button>
                                @else
                                    <span class="text-muted text-sm" style="font-style: italic;">Solo admin</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <div class="empty-state-icon">👥</div>
                                <p class="empty-state-text">No hay usuarios registrados.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-container>

    <!-- Modal para Crear/Editar Usuario -->
    <div id="userModal" class="modal" style="display: none;">
        <div class="modal-overlay" onclick="closeModal()"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="userModal-title">Nuevo usuario</h3>
                <button class="modal-close" onclick="closeModal()">✕</button>
            </div>
            <form id="userForm" onsubmit="handleSubmit(event)">
                <div class="modal-body">
                    <input type="hidden" id="userId" value="">
                    <div style="margin-bottom: var(--spacing-md);">
                        <label for="userName" style="display: block; margin-bottom: var(--spacing-xs); font-weight: 500; font-size: 0.875rem;">Nombre *</label>
                        <input type="text" id="userName" class="form-input" placeholder="Nombre completo" required>
                    </div>
                    <div id="emailGroup" style="margin-bottom: var(--spacing-md);">
                        <label for="userEmail" style="display: block; margin-bottom: var(--spacing-xs); font-weight: 500; font-size: 0.875rem;">Email (opcional)</label>
                        <input type="email" id="userEmail" class="form-input" placeholder="correo@ejemplo.com">
                    </div>
                </div>
                <div class="modal-footer">
                    <x-button variant="secondary" type="button" onclick="closeModal()">Cancelar</x-button>
                    <x-button variant="primary" type="submit" id="saveBtn">Guardar</x-button>
                </div>
            </form>
        </div>
    </div>

    <x-toast />
@endsection

@section('styles')
<style>
    .modal {
        position: fixed;
        inset: 0;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        position: relative;
        background: white;
        border-radius: var(--radius-lg);
        width: 90%;
        max-width: 500px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .modal-header {
        padding: var(--spacing-lg) var(--spacing-xl);
        border-bottom: 1px solid var(--color-slate-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        font-size: 1.125rem;
        font-weight: 600;
        margin: 0;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--color-slate-400);
        cursor: pointer;
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: var(--radius-md);
        transition: all var(--transition-fast);
    }

    .modal-close:hover {
        background: var(--color-slate-100);
        color: var(--color-slate-600);
    }

    .modal-body {
        padding: var(--spacing-xl);
    }

    .modal-footer {
        padding: var(--spacing-lg) var(--spacing-xl);
        border-top: 1px solid var(--color-slate-200);
        display: flex;
        justify-content: flex-end;
        gap: var(--spacing-sm);
    }
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
    document.getElementById('userModal').style.display = 'flex';
    document.getElementById('userName').focus();
}

function openEditModal(id, name) {
    document.getElementById('userModal-title').textContent = 'Editar usuario';
    document.getElementById('userId').value = id;
    document.getElementById('userName').value = name;
    document.getElementById('emailGroup').style.display = 'none';
    document.getElementById('userModal').style.display = 'flex';
    document.getElementById('userName').focus();
}

function closeModal() {
    document.getElementById('userModal').style.display = 'none';
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
        if (id) {
            url = `/api/users/${id}`;
            method = 'PUT';
            body = JSON.stringify({ name });
        } else {
            url = '/api/users';
            method = 'POST';
            body = JSON.stringify({ name, email: email || undefined });
        }
        
        const response = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body
        });
        
        const data = await response.json();
        
        if (data.success) {
            closeModal();
            showToast(data.message || 'Usuario guardado correctamente', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message || 'Error al guardar', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Error al guardar el usuario', 'error');
    } finally {
        saveBtn.disabled = false;
        saveBtn.textContent = 'Guardar';
    }
}

async function toggleActive(id) {
    try {
        const response = await fetch(`/api/users/${id}/toggle-active`, {
            method: 'PATCH',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast(data.message || 'Estado actualizado', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message || 'Error al cambiar el estado', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Error al cambiar el estado del usuario', 'error');
    }
}

function startEdit(userId, currentVal) {
    document.getElementById('display-' + userId).style.display = 'none';
    const editRow = document.getElementById('edit-' + userId);
    editRow.style.display = 'flex';
    const input = document.getElementById('input-' + userId);
    input.value = currentVal;
    input.focus();
    
    input.addEventListener('keydown', function handler(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            saveIdentification(userId);
        }
        if (e.key === 'Escape') {
            cancelEdit(userId, currentVal);
        }
    });
}

function cancelEdit(userId, originalVal) {
    document.getElementById('edit-' + userId).style.display = 'none';
    document.getElementById('display-' + userId).style.display = 'flex';
    document.getElementById('input-' + userId).value = originalVal;
}

async function saveIdentification(userId) {
    const value = document.getElementById('input-' + userId).value.trim();
    
    if (!value) {
        showToast('La identificación no puede estar vacía.', 'error');
        return;
    }

    try {
        const res = await fetch('/api/users/' + userId + '/identification', {
            method: 'PATCH',
            headers: { 
                'Content-Type': 'application/json', 
                'X-CSRF-TOKEN': csrfToken 
            },
            body: JSON.stringify({ identification: value }),
        });
        
        const json = await res.json();

        if (!json.success) {
            showToast(json.message || 'Error al guardar.', 'error');
            return;
        }

        const span = document.getElementById('id-val-' + userId);
        span.textContent = value;
        span.style.color = 'var(--color-slate-700)';

        cancelEdit(userId, value);
        showToast('Identificación actualizada correctamente.', 'success');
    } catch (error) {
        console.error('Error:', error);
        showToast('Error de conexión.', 'error');
    }
}

async function resetPassword(userId, userName) {
    if (!confirm(`¿Restablecer la contraseña de "${userName}"?\n\nLa nueva contraseña será su nombre de usuario.`)) {
        return;
    }

    try {
        const res = await fetch('/api/users/' + userId + '/reset-password', {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': csrfToken },
        });
        
        const json = await res.json();
        showToast(
            json.success ? json.message : 'Error al restablecer contraseña.', 
            json.success ? 'success' : 'error'
        );
    } catch (error) {
        console.error('Error:', error);
        showToast('Error de conexión.', 'error');
    }
}

// Cerrar modal con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});
</script>
@endsection
