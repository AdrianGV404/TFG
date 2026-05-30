<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Nuevo Usuario</h2>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Volver a la lista
        </a>
    </div>

    <form wire:submit.prevent="addUser" class="mt-3">

        {{-- DATOS BÁSICOS --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-primary"><i class="fas fa-user me-2"></i>Datos del Usuario</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Nombre</label>
                        <input type="text" wire:model.defer="name" class="form-control">
                        @error('name')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" wire:model.defer="email" class="form-control">
                        @error('email')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Contraseña</label>
                        <input type="password" wire:model.defer="password" class="form-control">
                        @error('password')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- PERMISOS PERSONALIZADOS --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-primary"><i class="fas fa-shield-alt me-2"></i>Asignar Permisos Personalizados</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 border-end">
                        <h6 class="text-secondary fw-bold mb-3">Gestión General</h6>

                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" id="can_create_projects" wire:model.defer="can_create_projects"
                                class="form-check-input">
                            <label for="can_create_projects" class="form-check-label">Puede crear proyectos</label>
                        </div>

                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" id="can_reassign_users" wire:model.defer="can_reassign_users"
                                class="form-check-input">
                            <label for="can_reassign_users" class="form-check-label">Puede reasignar usuarios</label>
                        </div>
                    </div>

                    <div class="col-md-4 border-end">
                        <h6 class="text-secondary fw-bold mb-3">Tareas (En su proyecto)</h6>

                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" id="can_create_project_tasks_by_others"
                                wire:model.defer="can_create_project_tasks_by_others" class="form-check-input">
                            <label for="can_create_project_tasks_by_others" class="form-check-label">Crear tareas
                                ajenas</label>
                        </div>

                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" id="can_edit_project_tasks_by_others"
                                wire:model.defer="can_edit_project_tasks_by_others" class="form-check-input">
                            <label for="can_edit_project_tasks_by_others" class="form-check-label">Editar tareas
                                ajenas</label>
                        </div>

                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" id="can_delete_project_tasks_by_others"
                                wire:model.defer="can_delete_project_tasks_by_others" class="form-check-input">
                            <label for="can_delete_project_tasks_by_others" class="form-check-label">Borrar tareas
                                ajenas</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <h6 class="text-secondary fw-bold mb-3">Tareas (Global)</h6>

                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" id="can_create_any_task" wire:model.defer="can_create_any_task"
                                class="form-check-input">
                            <label for="can_create_any_task" class="form-check-label">Crear cualquier tarea</label>
                        </div>

                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" id="can_edit_any_task" wire:model.defer="can_edit_any_task"
                                class="form-check-input">
                            <label for="can_edit_any_task" class="form-check-label">Editar cualquier tarea</label>
                        </div>

                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" id="can_delete_any_task" wire:model.defer="can_delete_any_task"
                                class="form-check-input">
                            <label for="can_delete_any_task" class="form-check-label">Borrar cualquier tarea</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                <i class="fas fa-save me-1"></i> Crear Usuario
            </button>
        </div>
    </form>
</div>
