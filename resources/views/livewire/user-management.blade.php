<div class="container py-4">
    <h3>Gestión de Usuarios y Permisos</h3>
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th style="width: 25%;">Nombre</th>
                <th style="width: 25%;">Email</th>
                <th style="width: 10%;">Rol</th>
                <th style="width: 40%;">Acciones / Panel de Configuración</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>
                        @if ($editingUserId === $user->id)
                            <input type="text" wire:model.defer="editingName" class="form-control form-control-sm mb-1">
                        @else
                            {{ $user->name }}
                        @endif
                    </td>

                    <td>
                        @if ($editingUserId === $user->id)
                            <input type="email" wire:model.defer="editingEmail"
                                class="form-control form-control-sm mb-1">
                        @else
                            {{ $user->email }}
                        @endif
                    </td>

                    <td><span class="badge bg-secondary">{{ $user->role }}</span></td>

                    <td>
                        @if ($editingUserId === $user->id)
                            {{-- Input de Contraseña Opcional --}}
                            <input type="password" wire:model.defer="editingPassword"
                                placeholder="Nueva contraseña (opcional)" class="form-control form-control-sm mb-3">

                            {{-- Desplegable Interno de Configuración de Permisos en Bloque --}}
                            <div class="p-3 bg-white rounded border mb-2 shadow-sm" style="font-size: 0.85rem;">
                                <strong class="text-primary d-block mb-2"><i class="fas fa-user-shield"></i> Customizar
                                    Permisos de este Usuario:</strong>

                                <div class="row text-muted">
                                    <div class="col-md-6 border-end">
                                        <div class="form-check form-switch mb-1">
                                            <input type="checkbox" id="edit_p_proj"
                                                wire:model.defer="editingPermissions.can_create_projects"
                                                class="form-check-input">
                                            <label for="edit_p_proj" class="form-check-label text-dark">Puede crear
                                                proyectos</label>
                                        </div>
                                        <div class="form-check form-switch mt-2">
                                            <input type="checkbox" id="edit_p_reas"
                                                wire:model.defer="editingPermissions.can_reassign_users"
                                                class="form-check-input">
                                            <label for="edit_p_reas" class="form-check-label text-dark">Puede reasignar
                                                usuarios</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-1">
                                            <input type="checkbox" id="edit_t_cr_p"
                                                wire:model.defer="editingPermissions.can_create_project_tasks_by_others"
                                                class="form-check-input">
                                            <label for="edit_t_cr_p" class="form-check-label text-dark">Crear tareas
                                                ajenas (Su Proy.)</label>
                                        </div>
                                        <div class="form-check form-switch mb-1">
                                            <input type="checkbox" id="edit_t_cr_a"
                                                wire:model.defer="editingPermissions.can_create_any_task"
                                                class="form-check-input">
                                            <label for="edit_t_cr_a" class="form-check-label text-dark">Crear cualquier
                                                tarea (Global)</label>
                                        </div>
                                        <hr class="my-1">
                                        <div class="form-check form-switch mb-1">
                                            <input type="checkbox" id="edit_t_ed_p"
                                                wire:model.defer="editingPermissions.can_edit_project_tasks_by_others"
                                                class="form-check-input">
                                            <label for="edit_t_ed_p" class="form-check-label text-dark">Editar tareas
                                                ajenas (Su Proy.)</label>
                                        </div>
                                        <div class="form-check form-switch mb-1">
                                            <input type="checkbox" id="edit_t_ed_a"
                                                wire:model.defer="editingPermissions.can_edit_any_task"
                                                class="form-check-input">
                                            <label for="edit_t_ed_a" class="form-check-label text-dark">Editar cualquier
                                                tarea (Global)</label>
                                        </div>
                                        <hr class="my-1">
                                        <div class="form-check form-switch mb-1">
                                            <input type="checkbox" id="edit_t_dl_p"
                                                wire:model.defer="editingPermissions.can_delete_project_tasks_by_others"
                                                class="form-check-input">
                                            <label for="edit_t_dl_p" class="form-check-label text-dark">Borrar tareas
                                                ajenas (Su Proy.)</label>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" id="edit_t_dl_a"
                                                wire:model.defer="editingPermissions.can_delete_any_task"
                                                class="form-check-input">
                                            <label for="edit_t_dl_a" class="form-check-label text-dark">Borrar cualquier
                                                tarea (Global)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button wire:click="saveEdit" class="btn btn-sm btn-success me-1 shadow-sm">Guardar
                                Cambios</button>
                            <button wire:click="cancelEdit" class="btn btn-sm btn-secondary shadow-sm">Cancelar</button>
                        @else
                            <button wire:click="startEdit({{ $user->id }})"
                                class="btn btn-sm btn-primary me-1">Editar Usuario y Permisos</button>
                            <button wire:click="deleteUser({{ $user->id }})"
                                class="btn btn-sm btn-danger">Eliminar</button>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
