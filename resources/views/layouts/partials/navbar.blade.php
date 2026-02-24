<nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between h-16">
            <!-- Brand -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center gap-2 text-indigo-600 hover:text-indigo-700 transition-colors">
                    <span class="text-2xl">🎁</span>
                    <span class="font-bold text-xl hidden sm:block">{{ $appName }}</span>
                </a>
            </div>

            @auth
            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-1">
                @if(Auth::user()->hasPermission('dashboard'))
                    <a href="{{ route('home') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ ($active ?? '') === 'dashboard' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-indigo-50' }}">
                        <span class="text-lg">🏠</span>
                        <span>Home</span>
                    </a>
                @endif

                @if(Auth::user()->hasPermission('juego'))
                    <a href="{{ route('juego') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ ($active ?? '') === 'juego' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-indigo-50' }}">
                        <span class="text-lg">🎮</span>
                        <span>Juego</span>
                    </a>
                @endif

                @if(Auth::user()->hasPermission('pagos'))
                    <a href="{{ route('fundraising.pagos') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ ($active ?? '') === 'pagos' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-indigo-50' }}">
                        <span class="text-lg">💳</span>
                        <span>Pagos</span>
                    </a>
                @endif

                @if(Auth::user()->hasPermission('recaudaciones'))
                    <a href="{{ route('fundraising.recaudaciones') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ ($active ?? '') === 'recaudaciones' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-indigo-50' }}">
                        <span class="text-lg">💰</span>
                        <span>Recaudaciones</span>
                    </a>
                @endif

                @if(Auth::user()->hasPermission('usuarios'))
                    <a href="{{ route('usuarios') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ ($active ?? '') === 'usuarios' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-indigo-50' }}">
                        <span class="text-lg">👥</span>
                        <span>Usuarios</span>
                    </a>
                @endif

                @if(Auth::user()->hasPermission('admin'))
                    <a href="{{ route('admin') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ ($active ?? '') === 'admin' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-indigo-50' }}">
                        <span class="text-lg">👑</span>
                        <span>Admin</span>
                    </a>
                @endif
            </div>

            <!-- Desktop User Menu -->
            <div class="hidden md:flex items-center gap-3">
                <a href="{{ route('perfil') }}" class="flex items-center gap-2 px-3 py-1.5 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white text-xs font-semibold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <span class="text-sm text-gray-700 hidden lg:block">{{ Auth::user()->name }}</span>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:bg-red-50 hover:border-red-200 hover:text-red-600 transition-colors">
                        Salir
                    </button>
                </form>
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center">
                <button type="button" id="mobileMenuBtn" class="p-2 rounded-lg text-gray-600 hover:text-indigo-600 hover:bg-indigo-50">
                    <svg class="h-6 w-6" id="menuIcon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg class="h-6 w-6 hidden" id="closeIcon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            @endauth
        </div>
    </div>

    <!-- Mobile Menu -->
    @auth
    <div class="hidden md:hidden" id="mobileMenu">
        <div class="px-2 pt-2 pb-3 space-y-1 bg-white border-t border-gray-200">
            @if(Auth::user()->hasPermission('dashboard'))
                <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium {{ ($active ?? '') === 'dashboard' ? 'text-indigo-600 bg-indigo-50 border-l-4 border-indigo-600' : 'text-gray-600 hover:text-indigo-600 hover:bg-indigo-50' }}">
                    <span class="text-xl">🏠</span>
                    <span>Home</span>
                </a>
            @endif

            @if(Auth::user()->hasPermission('juego'))
                <a href="{{ route('juego') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium {{ ($active ?? '') === 'juego' ? 'text-indigo-600 bg-indigo-50 border-l-4 border-indigo-600' : 'text-gray-600 hover:text-indigo-600 hover:bg-indigo-50' }}">
                    <span class="text-xl">🎮</span>
                    <span>Juego</span>
                </a>
            @endif

            @if(Auth::user()->hasPermission('pagos'))
                <a href="{{ route('fundraising.pagos') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium {{ ($active ?? '') === 'pagos' ? 'text-indigo-600 bg-indigo-50 border-l-4 border-indigo-600' : 'text-gray-600 hover:text-indigo-600 hover:bg-indigo-50' }}">
                    <span class="text-xl">💳</span>
                    <span>Pagos</span>
                </a>
            @endif

            @if(Auth::user()->hasPermission('recaudaciones'))
                <a href="{{ route('fundraising.recaudaciones') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium {{ ($active ?? '') === 'recaudaciones' ? 'text-indigo-600 bg-indigo-50 border-l-4 border-indigo-600' : 'text-gray-600 hover:text-indigo-600 hover:bg-indigo-50' }}">
                    <span class="text-xl">💰</span>
                    <span>Recaudaciones</span>
                </a>
            @endif

            @if(Auth::user()->hasPermission('usuarios'))
                <a href="{{ route('usuarios') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium {{ ($active ?? '') === 'usuarios' ? 'text-indigo-600 bg-indigo-50 border-l-4 border-indigo-600' : 'text-gray-600 hover:text-indigo-600 hover:bg-indigo-50' }}">
                    <span class="text-xl">👥</span>
                    <span>Usuarios</span>
                </a>
            @endif

            @if(Auth::user()->hasPermission('admin'))
                <a href="{{ route('admin') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium {{ ($active ?? '') === 'admin' ? 'text-indigo-600 bg-indigo-50 border-l-4 border-indigo-600' : 'text-gray-600 hover:text-indigo-600 hover:bg-indigo-50' }}">
                    <span class="text-xl">👑</span>
                    <span>Admin</span>
                </a>
            @endif

            <!-- Mobile User Section -->
            <div class="pt-4 border-t border-gray-200">
                <a href="{{ route('perfil') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium text-gray-600 hover:text-indigo-600 hover:bg-indigo-50">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white text-sm font-semibold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <span>{{ Auth::user()->name }}</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium text-red-600 hover:bg-red-50">
                        <span class="text-xl">🚪</span>
                        <span>Salir</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endauth
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('mobileMenuBtn');
    const menu = document.getElementById('mobileMenu');
    const menuIcon = document.getElementById('menuIcon');
    const closeIcon = document.getElementById('closeIcon');
    
    if (btn && menu) {
        btn.addEventListener('click', function() {
            menu.classList.toggle('hidden');
            menuIcon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
        });
    }
});
</script>
