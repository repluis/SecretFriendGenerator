<nav class="navbar">
    <div class="navbar-container">
        <!-- Brand -->
        <a href="{{ route('home') }}" class="navbar-brand">
            <span class="navbar-brand-icon">🎁</span>
            <span>{{ $appName }}</span>
        </a>

        <!-- Navigation Links -->
        @auth
        <div class="navbar-nav">
            @if(Auth::user()->hasPermission('dashboard'))
                <a href="{{ route('home') }}" class="nav-link {{ ($active ?? '') === 'dashboard' ? 'active' : '' }}">
                    <span class="nav-link-icon">🏠</span>
                    <span class="nav-link-text">Home</span>
                </a>
            @endif

            @if(Auth::user()->hasPermission('juego'))
                <a href="{{ route('juego') }}" class="nav-link {{ ($active ?? '') === 'juego' ? 'active' : '' }}">
                    <span class="nav-link-icon">🎮</span>
                    <span class="nav-link-text">Juego</span>
                </a>
            @endif

            @if(Auth::user()->hasPermission('pagos'))
                <a href="{{ route('fundraising.pagos') }}" class="nav-link {{ ($active ?? '') === 'pagos' ? 'active' : '' }}">
                    <span class="nav-link-icon">💳</span>
                    <span class="nav-link-text">Pagos</span>
                </a>
            @endif

            @if(Auth::user()->hasPermission('recaudaciones'))
                <a href="{{ route('fundraising.recaudaciones') }}" class="nav-link {{ ($active ?? '') === 'recaudaciones' ? 'active' : '' }}">
                    <span class="nav-link-icon">💰</span>
                    <span class="nav-link-text">Recaudaciones</span>
                </a>
            @endif

            @if(Auth::user()->hasPermission('usuarios'))
                <a href="{{ route('usuarios') }}" class="nav-link {{ ($active ?? '') === 'usuarios' ? 'active' : '' }}">
                    <span class="nav-link-icon">👥</span>
                    <span class="nav-link-text">Usuarios</span>
                </a>
            @endif

            @if(Auth::user()->hasPermission('admin'))
                <a href="{{ route('admin') }}" class="nav-link {{ ($active ?? '') === 'admin' ? 'active' : '' }}">
                    <span class="nav-link-icon">👑</span>
                    <span class="nav-link-text">Admin</span>
                </a>
            @endif
        </div>
        @endauth

        <!-- User Menu -->
        @auth
        <div class="navbar-user">
            <a href="{{ route('perfil') }}" class="user-menu-trigger">
                <div class="user-avatar-small">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <span>{{ Auth::user()->name }}</span>
            </a>

            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="btn-logout">
                    Salir
                </button>
            </form>
        </div>
        @endauth
    </div>
</nav>
