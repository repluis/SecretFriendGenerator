<nav class="navbar">
    <div class="navbar-inner">
        <a href="/" class="navbar-brand">SecretFriend</a>
        <div class="navbar-links">
            <a href="/" class="{{ ($active ?? '') === 'dashboard' ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('juego') }}" class="{{ ($active ?? '') === 'juego' ? 'active' : '' }}">Juego</a>
            <a href="{{ route('configuracion') }}" class="{{ ($active ?? '') === 'config' ? 'active' : '' }}">Config</a>
            <a href="{{ route('fundraising.navidad') }}" class="{{ ($active ?? '') === 'navidad' ? 'active' : '' }}">Navidad</a>
            <a href="{{ route('fundraising.recaudaciones') }}" class="{{ ($active ?? '') === 'recaudaciones' ? 'active' : '' }}">Recaudaciones</a>
        </div>
    </div>
</nav>
