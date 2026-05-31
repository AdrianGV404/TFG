<div class="un-root">

    <style>
        /* ── Variables (mismo sistema) ── */
        :root {
            --un-bg:        #f0f4f9;
            --un-card:      #ffffff;
            --un-border:    #e4e9f0;
            --un-text:      #0f172a;
            --un-muted:     #64748b;
            --un-accent:    #3b6ef6;
            --un-success:   #22c55e;
            --un-warn:      #f59e0b;
            --un-danger:    #ef4444;
            --un-purple:    #8b5cf6;
            --un-radius:    14px;
            --un-shadow:    0 2px 8px rgba(0,0,0,.06), 0 8px 24px rgba(0,0,0,.04);
            --un-shadow-h:  0 4px 16px rgba(59,110,246,.14), 0 12px 32px rgba(0,0,0,.08);
        }

        .dark-mode-active {
            --un-bg:     #0c1220;
            --un-card:   #151f31;
            --un-border: #1e2d47;
            --un-text:   #e8edf5;
            --un-muted:  #7a8fa6;
            --un-accent: #4f7eff;
        }

        /* ── Wrapper ── */
        .un-root {
            background: var(--un-bg);
            min-height: 100vh;
            padding: 24px 24px 60px;
            font-family: 'Segoe UI', system-ui, sans-serif;
            color: var(--un-text);
            transition: background .3s, color .3s;
        }

        /* ── Page header ── */
        .un-page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 22px;
        }
        .un-page-title {
            font-size: 1.45rem;
            font-weight: 800;
            letter-spacing: -.03em;
            color: var(--un-text);
            margin: 0;
        }
        .un-page-title span { color: var(--un-accent); }

        /* ── Botones ── */
        .un-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 9px;
            border: none;
            font-size: .83rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .18s ease;
            text-decoration: none;
            white-space: nowrap;
        }
        .un-btn-primary {
            background: var(--un-accent);
            color: #fff;
            box-shadow: 0 2px 8px rgba(59,110,246,.28);
        }
        .un-btn-primary:hover { background: #2952d9; transform: translateY(-1px); box-shadow: 0 4px 14px rgba(59,110,246,.38); }
        .un-btn-ghost {
            background: var(--un-card);
            color: var(--un-muted);
            border: 1.5px solid var(--un-border);
        }
        .un-btn-ghost:hover { border-color: var(--un-accent); color: var(--un-accent); transform: translateY(-1px); }

        .un-btn-submit {
            background: linear-gradient(135deg, var(--un-accent), var(--un-purple));
            color: #fff;
            padding: 11px 28px;
            font-size: .92rem;
            border-radius: 11px;
            box-shadow: 0 4px 16px rgba(59,110,246,.3);
        }
        .un-btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 22px rgba(59,110,246,.42);
        }

        /* ── Sección / tarjeta ── */
        .un-section {
            background: var(--un-card);
            border: 1.5px solid var(--un-border);
            border-radius: var(--un-radius);
            box-shadow: var(--un-shadow);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .un-section-header {
            padding: 14px 20px;
            border-bottom: 1.5px solid var(--un-border);
            display: flex;
            align-items: center;
            gap: 9px;
        }
        .un-section-icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: .85rem;
            flex-shrink: 0;
        }
        .un-section-icon-blue  { background: rgba(59,110,246,.12); color: var(--un-accent); }
        .un-section-icon-purple { background: rgba(139,92,246,.12); color: var(--un-purple); }
        .un-section-title {
            font-size: .95rem;
            font-weight: 700;
            color: var(--un-text);
            margin: 0;
        }

        .un-section-body { padding: 20px; }

        /* ── Campos ── */
        .un-fields-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px;
        }
        @media (max-width: 720px) {
            .un-fields-grid { grid-template-columns: 1fr; }
        }

        .un-field-label {
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: var(--un-muted);
            display: block;
            margin-bottom: 6px;
        }
        .un-input {
            width: 100%;
            padding: 9px 12px;
            border: 1.5px solid var(--un-border);
            border-radius: 9px;
            background: var(--un-bg);
            color: var(--un-text);
            font-size: .87rem;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            box-sizing: border-box;
        }
        .un-input:focus {
            border-color: var(--un-accent);
            box-shadow: 0 0 0 3px rgba(59,110,246,.1);
        }
        .un-field-error {
            font-size: .76rem;
            color: var(--un-danger);
            margin-top: 4px;
            display: block;
        }

        /* ── Permisos ── */
        .un-perms-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 0 24px;
        }
        @media (max-width: 900px) {
            .un-perms-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 600px) {
            .un-perms-grid { grid-template-columns: 1fr; }
        }

        .un-perm-group {
            border-right: 1.5px solid var(--un-border);
            padding-right: 24px;
        }
        .un-perm-group:last-child {
            border-right: none;
            padding-right: 0;
        }

        .un-perm-group-title {
            font-size: .7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: var(--un-muted);
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid var(--un-border);
        }

        .un-switch-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px solid var(--un-border);
            gap: 8px;
        }
        .un-switch-row:last-child { border-bottom: none; }
        .un-switch-label {
            font-size: .82rem;
            color: var(--un-text);
            line-height: 1.3;
        }
        .form-check-input {
            accent-color: var(--un-accent);
            flex-shrink: 0;
        }
        .form-check.form-switch { margin: 0; }

        /* Submit footer */
        .un-form-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 6px;
        }

        @media (max-width: 600px) {
            .un-root { padding: 14px 10px 40px; }
            .un-perm-group { border-right: none; padding-right: 0; padding-bottom: 16px; border-bottom: 1.5px solid var(--un-border); }
            .un-perm-group:last-child { border-bottom: none; padding-bottom: 0; }
        }
    </style>

    {{-- ── Cabecera ── --}}
    <div class="un-page-header">
        <h2 class="un-page-title">Nuevo <span>Usuario</span></h2>
        <a href="{{ route('users.index') }}" class="un-btn un-btn-ghost">
            <i class="fas fa-arrow-left"></i> Volver a la lista
        </a>
    </div>

    <form wire:submit.prevent="addUser">

        {{-- DATOS BÁSICOS --}}
        <div class="un-section">
            <div class="un-section-header">
                <div class="un-section-icon un-section-icon-blue">
                    <i class="fas fa-user"></i>
                </div>
                <h5 class="un-section-title">Datos del Usuario</h5>
            </div>
            <div class="un-section-body">
                <div class="un-fields-grid">
                    <div>
                        <label class="un-field-label">Nombre</label>
                        <input type="text" wire:model.defer="name" class="un-input" placeholder="Nombre completo">
                        @error('name')
                            <span class="un-field-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="un-field-label">Email</label>
                        <input type="email" wire:model.defer="email" class="un-input" placeholder="correo@ejemplo.com">
                        @error('email')
                            <span class="un-field-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="un-field-label">Contraseña</label>
                        <input type="password" wire:model.defer="password" class="un-input" placeholder="········">
                        @error('password')
                            <span class="un-field-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- PERMISOS --}}
        <div class="un-section">
            <div class="un-section-header">
                <div class="un-section-icon un-section-icon-purple">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h5 class="un-section-title">Permisos Personalizados</h5>
            </div>
            <div class="un-section-body">
                <div class="un-perms-grid">

                    {{-- Gestión general --}}
                    <div class="un-perm-group">
                        <div class="un-perm-group-title">Gestión general</div>
                        <div class="un-switch-row">
                            <span class="un-switch-label">Puede crear proyectos</span>
                            <div class="form-check form-switch">
                                <input type="checkbox" id="can_create_projects"
                                    wire:model.defer="can_create_projects" class="form-check-input">
                            </div>
                        </div>
                        <div class="un-switch-row">
                            <span class="un-switch-label">Puede reasignar usuarios</span>
                            <div class="form-check form-switch">
                                <input type="checkbox" id="can_reassign_users"
                                    wire:model.defer="can_reassign_users" class="form-check-input">
                            </div>
                        </div>
                    </div>

                    {{-- Tareas en su proyecto --}}
                    <div class="un-perm-group">
                        <div class="un-perm-group-title">Tareas (En su proyecto)</div>
                        <div class="un-switch-row">
                            <span class="un-switch-label">Crear tareas ajenas</span>
                            <div class="form-check form-switch">
                                <input type="checkbox" id="can_create_project_tasks_by_others"
                                    wire:model.defer="can_create_project_tasks_by_others" class="form-check-input">
                            </div>
                        </div>
                        <div class="un-switch-row">
                            <span class="un-switch-label">Editar tareas ajenas</span>
                            <div class="form-check form-switch">
                                <input type="checkbox" id="can_edit_project_tasks_by_others"
                                    wire:model.defer="can_edit_project_tasks_by_others" class="form-check-input">
                            </div>
                        </div>
                        <div class="un-switch-row">
                            <span class="un-switch-label">Borrar tareas ajenas</span>
                            <div class="form-check form-switch">
                                <input type="checkbox" id="can_delete_project_tasks_by_others"
                                    wire:model.defer="can_delete_project_tasks_by_others" class="form-check-input">
                            </div>
                        </div>
                    </div>

                    {{-- Tareas global --}}
                    <div class="un-perm-group">
                        <div class="un-perm-group-title">Tareas (Global)</div>
                        <div class="un-switch-row">
                            <span class="un-switch-label">Crear cualquier tarea</span>
                            <div class="form-check form-switch">
                                <input type="checkbox" id="can_create_any_task"
                                    wire:model.defer="can_create_any_task" class="form-check-input">
                            </div>
                        </div>
                        <div class="un-switch-row">
                            <span class="un-switch-label">Editar cualquier tarea</span>
                            <div class="form-check form-switch">
                                <input type="checkbox" id="can_edit_any_task"
                                    wire:model.defer="can_edit_any_task" class="form-check-input">
                            </div>
                        </div>
                        <div class="un-switch-row">
                            <span class="un-switch-label">Borrar cualquier tarea</span>
                            <div class="form-check form-switch">
                                <input type="checkbox" id="can_delete_any_task"
                                    wire:model.defer="can_delete_any_task" class="form-check-input">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="un-form-footer">
            <a href="{{ route('users.index') }}" class="un-btn un-btn-ghost">Cancelar</a>
            <button type="submit" class="un-btn un-btn-submit">
                <i class="fas fa-save"></i> Crear Usuario
            </button>
        </div>

    </form>

</div>