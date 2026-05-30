<div class="min-vh-100 d-flex justify-content-center align-items-center bg-light p-4">
    <div class="card p-4 shadow-sm" style="width: 550px;">
        <h3 class="mb-4 text-center">Crear Usuario Personal</h3>

        <form wire:submit.prevent="addUser">
            {{-- Nombre --}}
            <div class="mb-3">
                <label for="name" class="form-label font-weight-bold">Nombre</label>
                <input type="text" id="name" wire:model.defer="name" class="form-control">
                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            {{-- Email --}}
            <div class="mb-3">
                <label for="email" class="form-label font-weight-bold">Correo electrónico</label>
                <input type="email" id="email" wire:model.defer="email" class="form-control">
                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            {{-- Contraseña --}}
            <div class="mb-3">
                <label for="password" class="form-label font-weight-bold">Contraseña</label>
                <input type="password" id="password" wire:model.defer="password" class="form-control">
                @error('password') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            {{-- SECCIÓN DE PERMISOS PERSONALIZADOS --}}
            <div class="mb-4 p-3 bg-white rounded border">
                <h5 class="text-secondary border-bottom pb-2 mb-3"><i class="fas fa-shield-alt me-1"></i> Asignar Permisos</h5>
                
                <h6>Proyectos</h6>
                <div class="form-check mb-3">
                    <input type="checkbox" id="can_create_projects" wire:model.defer="can_create_projects" class="form-check-input">
                    <label for="can_create_projects" class="form-check-label">Puede crear proyectos</label>
                </div>

                <h6>Tareas (Creación)</h6>
                <div class="form-check">
                    <input type="checkbox" id="can_create_project_tasks_by_others" wire:model.defer="can_create_project_tasks_by_others" class="form-check-input">
                    <label for="can_create_project_tasks_by_others" class="form-check-label">Puede crear tareas en su proyecto creadas por otros</label>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" id="can_create_any_task" wire:model.defer="can_create_any_task" class="form-check-input">
                    <label for="can_create_any_task" class="form-check-label">Puede crear cualquier tarea de la plataforma</label>
                </div>

                <h6>Tareas (Edición)</h6>
                <div class="form-check">
                    <input type="checkbox" id="can_edit_project_tasks_by_others" wire:model.defer="can_edit_project_tasks_by_others" class="form-check-input">
                    <label for="can_edit_project_tasks_by_others" class="form-check-label">Puede editar tareas en su proyecto creadas por otros</label>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" id="can_edit_any_task" wire:model.defer="can_edit_any_task" class="form-check-input">
                    <label for="can_edit_any_task" class="form-check-label">Puede editar cualquier tarea de la plataforma</label>
                </div>

                <h6>Tareas (Eliminación)</h6>
                <div class="form-check">
                    <input type="checkbox" id="can_delete_project_tasks_by_others" wire:model.defer="can_delete_project_tasks_by_others" class="form-check-input">
                    <label for="can_delete_project_tasks_by_others" class="form-check-label">Puede borrar tareas en su proyecto creadas por otros</label>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" id="can_delete_any_task" wire:model.defer="can_delete_any_task" class="form-check-input">
                    <label for="can_delete_any_task" class="form-check-label">Puede borrar cualquier tarea de la plataforma</label>
                </div>

                <h6>Personal</h6>
                <div class="form-check">
                    <input type="checkbox" id="can_reassign_users" wire:model.defer="can_reassign_users" class="form-check-input">
                    <label for="can_reassign_users" class="form-check-label">Puede reasignar usuarios en las tareas</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 shadow-sm">Crear Usuario y Guardar Permisos</button>
        </form>
    </div>
</div>