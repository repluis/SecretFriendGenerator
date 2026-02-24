<nav class="navbar">
    <div class="navbar-inner">
        <a href="/" class="navbar-brand">{{ $appName }}</a>
        <div class="navbar-links">
            <a href="/" class="{{ ($active ?? '') === 'dashboard' ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('juego') }}" class="{{ ($active ?? '') === 'juego' ? 'active' : '' }}">Juego</a>
            <a href="{{ route('fundraising.pagos') }}" class="{{ ($active ?? '') === 'pagos' ? 'active' : '' }}">Pagos</a>
            <a href="{{ route('fundraising.recaudaciones') }}" class="{{ ($active ?? '') === 'recaudaciones' ? 'active' : '' }}">Recaudaciones</a>
            <a href="{{ route('usuarios') }}" class="{{ ($active ?? '') === 'usuarios' ? 'active' : '' }}">Usuarios</a>
        </div>
        @auth
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <a href="{{ route('perfil') }}" style="font-size: 0.82rem; color: #64748b; text-decoration: none; transition: color 0.15s;" onmouseover="this.style.color='#6366f1'" onmouseout="this.style.color='#64748b'">{{ Auth::user()->name }}</a>
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" style="background: none; border: 1px solid #e2e8f0; border-radius: 6px; padding: 0.3rem 0.75rem; font-size: 0.78rem; color: #64748b; cursor: pointer; font-family: inherit; transition: all 0.2s;">
                    Salir
                </button>
            </form>
        </div>
        @endauth
    </div>
</nav>

