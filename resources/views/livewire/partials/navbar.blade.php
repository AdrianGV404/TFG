<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top py-1">
    <div class="container d-flex align-items-center justify-content-between">

        {{-- Botón volver a proyectos (izquierda) --}}
        @if(Route::currentRouteName() !== 'projects')
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
                NOMBRE USUARIO
            </span>
        </div>

        {{-- Botón cerrar sesión (derecha) --}}
        <div style="width: 130px;"> {{-- mismo ancho que el botón de la izquierda para simetría --}}
            <button type="button" class="btn btn-outline-light btn-sm d-flex align-items-center w-100">
                <i class="fas fa-sign-out-alt me-1"></i>
                Cerrar Sesión
            </button>
        </div>

    </div>
</nav>

{{-- Espacio para que el contenido no quede tapado por el navbar fijo --}}
<div style="height: 56px;"></div>
