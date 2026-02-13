<div class="min-vh-100 d-flex justify-content-center align-items-center bg-light p-4">

    <div class="card p-4 shadow-sm" style="width: 450px;">

        <h3 class="mb-4 text-center">Registrar Empresa</h3>

        <form wire:submit.prevent="register">

            {{-- Nombre de la empresa --}}
            <div class="mb-3">
                <label for="companyName" class="form-label">Nombre de la empresa</label>
                <input type="text" id="companyName" wire:model.defer="companyName" class="form-control">
                @error('companyName')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            {{-- Nombre admin --}}
            <div class="mb-3">
                <label for="name" class="form-label">Nombre del administrador</label>
                <input type="text" id="name" wire:model.defer="name" class="form-control">
                @error('name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            {{-- Email --}}
            <div class="mb-3">
                <label for="email" class="form-label">Correo electrónico</label>
                <input type="email" id="email" wire:model.defer="email" class="form-control">
                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            {{-- Contraseña --}}
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" id="password" wire:model.defer="password" class="form-control">
                @error('password')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-success w-100 mt-2">
                Registrar Empresa
            </button>
        </form>

    </div>

</div>
