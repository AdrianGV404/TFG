<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top py-1">
    <div class="container-fluid d-flex align-items-center justify-content-between">

        {{-- COLUMNA IZQUIERDA: Botón Menú --}}
        <div class="d-flex align-items-center" style="flex: 1; flex-basis: 0;">
            @if(auth()->check() && Route::currentRouteName() !== 'login')
                <button class="btn btn-dark me-2" x-on:click="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
            @endif
        </div>

        {{-- COLUMNA CENTRAL: Nombre de usuario --}}
        <div class="text-center" style="flex: 1; flex-basis: 0;">
            <span class="navbar-brand fw-bold fs-5 mb-0 mx-0">
                {{ auth()->check() ? auth()->user()->name : 'ProMaTi' }}
            </span>
        </div>

        {{-- COLUMNA DERECHA: Notificaciones + Logout --}}
        <div class="d-flex align-items-center justify-content-end gap-2" style="flex: 1; flex-basis: 0;">
            @auth

                <livewire:notification-bell />

                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm d-flex align-items-center">
                        <i class="fas fa-sign-out-alt me-1"></i>
                        <span class="d-none d-md-inline">Cerrar Sesión</span>
                    </button>
                </form>

            @endauth
        </div>

    </div>
</nav>