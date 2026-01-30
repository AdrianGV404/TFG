<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top py-1">
    <div class="container d-flex align-items-center justify-content-between">

        {{-- Botón volver a proyectos (solo si no estamos en la ruta de proyectos) --}}
        @if(Route::currentRouteName() !== 'livewire.projects')
            <a href="{{ route('livewire.projects') }}" class="btn btn-outline-light btn-sm d-flex align-items-center">
                <i class="fas fa-arrow-left me-1"></i>
                Volver a proyectos
            </a>
        @else
            <div style="width: 130px;"></div> {{-- Placeholder con el mismo ancho que el botón original --}}
        @endif

        {{-- Nombre de usuario --}}
        <span class="navbar-brand fw-bold fs-5 mb-0 text-center">NOMBRE USUARIO</span>

        {{-- Botón cerrar sesión --}}
        <button type="submit" class="btn btn-outline-light btn-sm d-flex align-items-center">
            <i class="fas fa-sign-out-alt me-1"></i>
            Cerrar Sesión
        </button>

    </div>
</nav>

{{-- Espacio para que el contenido no quede tapado por el navbar fijo --}}
<div style="height: 50px;"></div>
