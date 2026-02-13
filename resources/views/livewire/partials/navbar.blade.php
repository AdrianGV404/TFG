<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top py-1">
    <div class="container d-flex align-items-center justify-content-between">

        {{-- Botón volver a proyectos (izquierda) --}}
        @if (Route::currentRouteName() !== 'projects' &&
                Route::currentRouteName() !== 'landingpage' &&
                Route::currentRouteName() !== 'login' &&
                Route::currentRouteName() !== 'register.personal' &&
                Route::currentRouteName() !== 'register.empresa')
            <a href="{{ route('projects') }}" class="btn btn-outline-light btn-sm d-flex align-items-center">
                <i class="fas fa-arrow-left me-1"></i>
                Volver a proyectos
            </a>
        @else
            <div style="width: 130px;"></div> {{-- Placeholder con el mismo ancho que el botón original --}}
        @endif

        {{-- Nombre de usuario centrado --}}
        <div class="flex-grow-1 text-center">
            <span class="navbar-brand fw-bold fs-5 mb-0">
                {{ auth()->check() ? auth()->user()->name : '' }}
            </span>
        </div>
        @auth
            @if (auth()->user()->role === 'admin')
                <a href="{{ route('users.create') }}" class="btn btn-outline-light btn-sm me-2">
                    <i class="fas fa-user-plus me-1"></i> Nuevo Usuario
                </a>
                <a href="{{ route('users.index') }}" class="btn btn-outline-light btn-sm me-2">
                    <i class="fas fa-users me-1"></i> Gestionar Usuarios
                </a>
            @endif
        @endauth
        @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm d-flex align-items-center w-100">
                    <i class="fas fa-sign-out-alt me-1"></i>
                    Cerrar Sesión
                </button>
            </form>
        @endauth

    </div>
</nav>

{{-- Espacio para que el contenido no quede tapado por el navbar fijo --}}
<div style="height: 56px;"></div>
