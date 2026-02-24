@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('styles')
<style>
    .profile-container {
        max-width: 680px;
        margin: 0 auto;
        padding: 2rem 1.5rem;
    }

    .profile-header {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .profile-avatar {
        width: 60px; height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem; font-weight: 700;
        color: white;
        flex-shrink: 0;
    }

    .profile-header-info h1 {
        font-size: 1.4rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.2rem;
    }

    .profile-header-info p {
        font-size: 0.85rem;
        color: #64748b;
    }

    .profile-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 1.75rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }

    .profile-card h2 {
        font-size: 1rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 0.4rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .form-group input {
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.6rem 0.85rem;
        font-size: 0.9rem;
        font-family: inherit;
        color: #1e293b;
        transition: border-color 0.15s;
        outline: none;
        box-sizing: border-box;
    }

    .form-group input:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
    }

    .form-group input.is-invalid {
        border-color: #dc2626;
    }

    .error-msg {
        font-size: 0.78rem;
        color: #dc2626;
        margin-top: 0.3rem;
    }

    .btn-save {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.55rem 1.25rem;
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        transition: opacity 0.15s;
        margin-top: 0.5rem;
    }

    .btn-save:hover { opacity: 0.9; }

    .alert-success {
        background: #dcfce7;
        border: 1px solid #bbf7d0;
        color: #15803d;
        border-radius: 8px;
        padding: 0.65rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 1rem;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid #f8fafc;
        font-size: 0.875rem;
    }

    .info-row:last-child { border-bottom: none; }
    .info-row .label { color: #64748b; }
    .info-row .value { font-weight: 500; color: #1e293b; }
</style>
@endsection

@section('content')
<div class="profile-container">

    <div class="profile-header">
        <div class="profile-avatar">
            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
        </div>
        <div class="profile-header-info">
            <h1>{{ Auth::user()->name }}</h1>
            <p>{{ Auth::user()->identification ?? 'Sin identificación' }}</p>
        </div>
    </div>

    {{-- Datos de cuenta --}}
    <div class="profile-card">
        <h2>Información de cuenta</h2>
        <div class="info-row">
            <span class="label">Email</span>
            <span class="value">{{ Auth::user()->email ?? '—' }}</span>
        </div>
        <div class="info-row">
            <span class="label">Estado</span>
            <span class="value" style="color: #16a34a;">Activo</span>
        </div>
    </div>

    {{-- Cambiar nombre --}}
    <div class="profile-card">
        <h2>Cambiar nombre</h2>

        @if(session('name_success'))
            <div class="alert-success">{{ session('name_success') }}</div>
        @endif

        <form method="POST" action="{{ route('perfil.nombre') }}">
            @csrf
            <div class="form-group">
                <label for="name">Nombre</label>
                <input type="text" id="name" name="name"
                    value="{{ old('name', Auth::user()->name) }}"
                    class="{{ $errors->has('name') ? 'is-invalid' : '' }}"
                    placeholder="Tu nombre completo"
                    maxlength="255">
                @error('name')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn-save">Guardar nombre</button>
        </form>
    </div>

    {{-- Cambiar identificación --}}
    <div class="profile-card">
        <h2>Cambiar identificación</h2>

        @if(session('identification_success'))
            <div class="alert-success">{{ session('identification_success') }}</div>
        @endif

        <form method="POST" action="{{ route('perfil.identificacion') }}">
            @csrf
            <div class="form-group">
                <label for="identification">Identificación</label>
                <input type="text" id="identification" name="identification"
                    value="{{ old('identification', Auth::user()->identification) }}"
                    class="{{ $errors->has('identification') ? 'is-invalid' : '' }}"
                    placeholder="Ej: CC-123456"
                    maxlength="100">
                @error('identification')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn-save">Guardar identificación</button>
        </form>
    </div>

    {{-- Cambiar contraseña --}}
    <div class="profile-card">
        <h2>Cambiar contraseña</h2>

        @if(session('password_success'))
            <div class="alert-success">{{ session('password_success') }}</div>
        @endif

        <form method="POST" action="{{ route('perfil.contrasena') }}">
            @csrf
            <div class="form-group">
                <label for="current_password">Contraseña actual</label>
                <input type="password" id="current_password" name="current_password"
                    class="{{ $errors->has('current_password') ? 'is-invalid' : '' }}"
                    placeholder="••••••••"
                    maxlength="255">
                @error('current_password')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="new_password">Nueva contraseña</label>
                <input type="password" id="new_password" name="new_password"
                    class="{{ $errors->has('new_password') ? 'is-invalid' : '' }}"
                    placeholder="Mínimo 6 caracteres"
                    maxlength="255">
                @error('new_password')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="new_password_confirmation">Confirmar nueva contraseña</label>
                <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                    placeholder="Repite la nueva contraseña"
                    maxlength="255">
            </div>
            <button type="submit" class="btn-save">Cambiar contraseña</button>
        </form>
    </div>

</div>
@endsection
