<div class="min-vh-100 d-flex justify-content-center align-items-center bg-light p-4">
    <div class="card p-4 shadow-sm" style="width: 400px;">
        <h3 class="mb-4 text-center">Login</h3>

        <form wire:submit.prevent="login">
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

            <button type="submit" class="btn btn-primary w-100">Ingresar</button>
        </form>
    </div>
</div>
