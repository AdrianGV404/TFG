<div class="container py-4">
    <h3>Gestión de Usuarios</h3>
    <a href="{{ route('users.create') }}" class="btn btn-sm btn-primary mb-3">
        <i class="fas fa-user-plus me-1"></i> Nuevo Usuario
    </a>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>
                        @if ($editingUserId === $user->id)
                            <input type="text" wire:model.defer="editingName" class="form-control">
                        @else
                            {{ $user->name }}
                        @endif
                    </td>
                    <td>
                        @if ($editingUserId === $user->id)
                            <input type="email" wire:model.defer="editingEmail" class="form-control">
                        @else
                            {{ $user->email }}
                        @endif
                    </td>
                    <td>{{ $user->role }}</td>
                    <td>
                        @if ($editingUserId === $user->id)
                            <input type="password" wire:model.defer="editingPassword"
                                placeholder="Nueva contraseña (opcional)" class="form-control mb-1">
                            <button wire:click="saveEdit" class="btn btn-sm btn-success">Guardar</button>
                            <button wire:click="cancelEdit" class="btn btn-sm btn-secondary">Cancelar</button>
                        @else
                            <button wire:click="startEdit({{ $user->id }})"
                                class="btn btn-sm btn-primary">Editar</button>
                            <button wire:click="deleteUser({{ $user->id }})"
                                class="btn btn-sm btn-danger">Eliminar</button>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
