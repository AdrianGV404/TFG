<div class="min-vh-100 d-flex flex-column justify-content-center align-items-center bg-light p-4">

    {{-- Título --}}
    <h1 class="mb-4 text-center">Bienvenido a ProMaTi</h1>

    {{-- Botones principales --}}
    <div class="d-flex gap-3 mb-3 flex-wrap justify-content-center">
        <button wire:click="toggleOptions" class="btn btn-primary">
            Crear nuevo entorno
        </button>

        <button onclick="window.location='{{ route('login') }}'" class="btn btn-outline-secondary">
            Login
        </button>
    </div>

    {{-- Opciones del entorno --}}
    @if ($showOptions)
        <div class="d-flex gap-3 mt-3 flex-wrap justify-content-center">
            <button wire:click="createPersonalTenant" class="btn btn-info">
                Modo Personal (1 admin, 0 usuarios)
            </button>

            <button wire:click="createEmpresaTenant" class="btn btn-success">
                Modo Empresa (1 admin, N usuarios)
            </button>
        </div>
    @endif

</div>
