<div class="container py-4">
    <h2>Nuevo Usuario</h2>

    <form wire:submit.prevent="addUser" class="mt-3">
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" wire:model.defer="name" class="form-control">
            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" wire:model.defer="email" class="form-control">
            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <input type="password" wire:model.defer="password" class="form-control">
            @error('password') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="btn btn-primary">Crear Usuario</button>
    </form>
</div>
