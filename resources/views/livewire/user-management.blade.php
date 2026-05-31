<div class="um-root">

    <style>
        /* ── Variables (mismo sistema que tasks/task-detail) ── */
        :root {
            --um-bg:        #f0f4f9;
            --um-card:      #ffffff;
            --um-border:    #e4e9f0;
            --um-text:      #0f172a;
            --um-muted:     #64748b;
            --um-accent:    #3b6ef6;
            --um-success:   #22c55e;
            --um-warn:      #f59e0b;
            --um-danger:    #ef4444;
            --um-purple:    #8b5cf6;
            --um-radius:    14px;
            --um-shadow:    0 2px 8px rgba(0,0,0,.06), 0 8px 24px rgba(0,0,0,.04);
            --um-shadow-h:  0 4px 16px rgba(59,110,246,.14), 0 12px 32px rgba(0,0,0,.08);
        }

        .dark-mode-active {
            --um-bg:     #0c1220;
            --um-card:   #151f31;
            --um-border: #1e2d47;
            --um-text:   #e8edf5;
            --um-muted:  #7a8fa6;
            --um-accent: #4f7eff;
        }

        /* ── Wrapper ── */
        .um-root {
            background: var(--um-bg);
            min-height: 100vh;
            padding: 24px 24px 60px;
            font-family: 'Segoe UI', system-ui, sans-serif;
            color: var(--um-text);
            transition: background .3s, color .3s;
        }

        /* ── Page header ── */
        .um-page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 22px;
        }
        .um-page-title {
            font-size: 1.45rem;
            font-weight: 800;
            letter-spacing: -.03em;
            color: var(--um-text);
            margin: 0;
        }
        .um-page-title span { color: var(--um-accent); }

        /* ── Botones ── */
        .um-btn {
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
        .um-btn-primary {
            background: var(--um-accent);
            color: #fff;
            box-shadow: 0 2px 8px rgba(59,110,246,.28);
        }
        .um-btn-primary:hover { background: #2952d9; transform: translateY(-1px); box-shadow: 0 4px 14px rgba(59,110,246,.38); }
        .um-btn-ghost {
            background: var(--um-card);
            color: var(--um-muted);
            border: 1.5px solid var(--um-border);
        }
        .um-btn-ghost:hover { border-color: var(--um-accent); color: var(--um-accent); transform: translateY(-1px); }
        .um-btn-danger {
            background: rgba(239,68,68,.1);
            color: var(--um-danger);
            border: 1.5px solid rgba(239,68,68,.2);
        }
        .um-btn-danger:hover { background: rgba(239,68,68,.18); transform: translateY(-1px); }
        .um-btn-success {
            background: rgba(34,197,94,.1);
            color: #16a34a;
            border: 1.5px solid rgba(34,197,94,.2);
        }
        .um-btn-success:hover { background: rgba(34,197,94,.18); transform: translateY(-1px); }

        /* ── Lista de usuarios ── */
        .um-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* ── Fila de usuario ── */
        .um-row {
            background: var(--um-card);
            border: 1.5px solid var(--um-border);
            border-radius: var(--um-radius);
            box-shadow: var(--um-shadow);
            overflow: hidden;
            transition: box-shadow .2s, border-color .2s;
        }
        .um-row:hover {
            box-shadow: var(--um-shadow-h);
            border-color: rgba(59,110,246,.18);
        }

        /* Franja de color lateral */
        .um-row-stripe {
            width: 4px;
            flex-shrink: 0;
            align-self: stretch;
            background: linear-gradient(180deg, var(--um-accent), var(--um-purple));
        }

        .um-row-main {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 18px;
            flex-wrap: wrap;
        }

        /* Avatar */
        .um-avatar {
            width: 42px; height: 42px;
            border-radius: 50%;
            border: 2px solid var(--um-border);
            object-fit: cover;
            flex-shrink: 0;
            font-size: .85rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            text-transform: uppercase;
            overflow: hidden;
            transition: border-color .15s;
        }
        .um-row:hover .um-avatar { border-color: var(--um-accent); }
        .um-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }

        /* Info del usuario */
        .um-user-info {
            flex: 1;
            min-width: 160px;
        }
        .um-user-name {
            font-size: .95rem;
            font-weight: 700;
            color: var(--um-text);
            margin: 0 0 2px;
            line-height: 1.3;
        }
        .um-user-email {
            font-size: .78rem;
            color: var(--um-muted);
        }

        /* Badge de rol */
        .um-role-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .03em;
            background: rgba(59,110,246,.1);
            color: var(--um-accent);
            border: 1px solid rgba(59,110,246,.2);
            flex-shrink: 0;
        }
        .dark-mode-active .um-role-badge {
            background: rgba(79,126,255,.15);
            border-color: rgba(79,126,255,.3);
        }

        /* Acciones */
        .um-actions {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-shrink: 0;
            flex-wrap: wrap;
        }

        /* ── Panel de edición (inline) ── */
        .um-edit-panel {
            border-top: 1.5px solid var(--um-border);
            padding: 20px 22px;
            background: var(--um-bg);
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .um-edit-fields {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 14px;
        }
        @media (max-width: 720px) {
            .um-edit-fields { grid-template-columns: 1fr; }
        }

        .um-field-label {
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: var(--um-muted);
            display: block;
            margin-bottom: 5px;
        }
        .um-input {
            width: 100%;
            padding: 8px 12px;
            border: 1.5px solid var(--um-border);
            border-radius: 9px;
            background: var(--um-card);
            color: var(--um-text);
            font-size: .85rem;
            outline: none;
            transition: border-color .15s;
            box-sizing: border-box;
        }
        .um-input:focus { border-color: var(--um-accent); }

        /* Panel de permisos */
        .um-perms-panel {
            background: var(--um-card);
            border: 1.5px solid var(--um-border);
            border-radius: 11px;
            padding: 16px 18px;
        }
        .um-perms-title {
            font-size: .78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: var(--um-accent);
            margin: 0 0 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .um-perms-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 24px;
        }
        @media (max-width: 720px) {
            .um-perms-grid { grid-template-columns: 1fr; }
        }

        .um-perm-group-title {
            font-size: .68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--um-muted);
            padding-bottom: 6px;
            border-bottom: 1px solid var(--um-border);
            margin-bottom: 6px;
            grid-column: 1 / -1;
        }

        /* Toggle switches */
        .um-switch-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid var(--um-border);
        }
        .um-switch-row:last-child { border-bottom: none; }
        .um-switch-label {
            font-size: .8rem;
            color: var(--um-text);
        }
        .form-check-input {
            accent-color: var(--um-accent);
        }

        /* Edit actions footer */
        .um-edit-footer {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }

        @media (max-width: 600px) {
            .um-root { padding: 14px 10px 40px; }
            .um-row-main { flex-wrap: wrap; }
        }
    </style>

    {{-- ── Cabecera ── --}}
    <div class="um-page-header">
        <h2 class="um-page-title">Gestión de <span>Usuarios</span> y Permisos</h2>
    </div>

    {{-- ── Lista ── --}}
    <div class="um-list">
        @foreach ($users as $user)
            @php
                $avatarColors = ['#3b6ef6','#6366f1','#22c55e','#f59e0b','#ef4444','#06b6d4','#8b5cf6','#ec4899'];
                $bg = $avatarColors[$user->id % count($avatarColors)];
                $initials = collect(explode(' ', $user->name))
                    ->map(fn($p) => strtoupper(substr($p, 0, 1)))
                    ->take(2)->implode('');
            @endphp

            <div class="um-row" wire:key="user-{{ $user->id }}">

                <div class="um-row-main">

                    {{-- Avatar --}}
                    <div class="um-avatar" style="background: {{ $bg }};">
                        @if ($user->profile_photo_path)
                            <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}">
                        @elseif (method_exists($user, 'profile_photo_url'))
                            <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}"
                                onerror="this.style.display='none'">
                        @else
                            {{ $initials }}
                        @endif
                    </div>

                    {{-- Nombre + email --}}
                    <div class="um-user-info">
                        @if ($editingUserId === $user->id)
                            <input type="text" wire:model.defer="editingName"
                                class="um-input" style="margin-bottom:5px;" placeholder="Nombre">
                            <input type="email" wire:model.defer="editingEmail"
                                class="um-input" placeholder="Email">
                        @else
                            <div class="um-user-name">{{ $user->name }}</div>
                            <div class="um-user-email">{{ $user->email }}</div>
                        @endif
                    </div>

                    {{-- Rol --}}
                    <span class="um-role-badge">
                        <i class="fas fa-shield-alt" style="font-size:.6rem;"></i>
                        {{ $user->role }}
                    </span>

                    {{-- Acciones --}}
                    <div class="um-actions">
                        @if ($editingUserId === $user->id)
                            <button wire:click="saveEdit" class="um-btn um-btn-success">
                                <i class="fas fa-check"></i> Guardar
                            </button>
                            <button wire:click="cancelEdit" class="um-btn um-btn-ghost">
                                Cancelar
                            </button>
                        @else
                            <button wire:click="startEdit({{ $user->id }})" class="um-btn um-btn-primary">
                                <i class="fas fa-pen"></i> Editar
                            </button>
                            <button wire:click="deleteUser({{ $user->id }})" class="um-btn um-btn-danger">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        @endif
                    </div>

                </div>{{-- /um-row-main --}}

                {{-- Panel de edición expandido --}}
                @if ($editingUserId === $user->id)
                    <div class="um-edit-panel">

                        {{-- Contraseña --}}
                        <div>
                            <label class="um-field-label">
                                <i class="fas fa-lock" style="margin-right:4px;"></i>Nueva contraseña (opcional)
                            </label>
                            <input type="password" wire:model.defer="editingPassword"
                                placeholder="Dejar en blanco para no cambiar"
                                class="um-input" style="max-width:360px;">
                        </div>

                        {{-- Permisos --}}
                        <div class="um-perms-panel">
                            <div class="um-perms-title">
                                <i class="fas fa-user-shield"></i> Permisos personalizados
                            </div>

                            <div class="um-perms-grid">

                                {{-- General --}}
                                <div>
                                    <div class="um-perm-group-title" style="grid-column:auto;">Gestión general</div>
                                    <div class="um-switch-row">
                                        <span class="um-switch-label">Puede crear proyectos</span>
                                        <div class="form-check form-switch mb-0">
                                            <input type="checkbox" id="edit_p_proj"
                                                wire:model.defer="editingPermissions.can_create_projects"
                                                class="form-check-input">
                                        </div>
                                    </div>
                                    <div class="um-switch-row">
                                        <span class="um-switch-label">Puede reasignar usuarios</span>
                                        <div class="form-check form-switch mb-0">
                                            <input type="checkbox" id="edit_p_reas"
                                                wire:model.defer="editingPermissions.can_reassign_users"
                                                class="form-check-input">
                                        </div>
                                    </div>
                                </div>

                                {{-- Tareas --}}
                                <div>
                                    <div class="um-perm-group-title" style="grid-column:auto;">Tareas en su proyecto</div>
                                    <div class="um-switch-row">
                                        <span class="um-switch-label">Crear tareas ajenas</span>
                                        <div class="form-check form-switch mb-0">
                                            <input type="checkbox" id="edit_t_cr_p"
                                                wire:model.defer="editingPermissions.can_create_project_tasks_by_others"
                                                class="form-check-input">
                                        </div>
                                    </div>
                                    <div class="um-switch-row">
                                        <span class="um-switch-label">Editar tareas ajenas</span>
                                        <div class="form-check form-switch mb-0">
                                            <input type="checkbox" id="edit_t_ed_p"
                                                wire:model.defer="editingPermissions.can_edit_project_tasks_by_others"
                                                class="form-check-input">
                                        </div>
                                    </div>
                                    <div class="um-switch-row">
                                        <span class="um-switch-label">Borrar tareas ajenas</span>
                                        <div class="form-check form-switch mb-0">
                                            <input type="checkbox" id="edit_t_dl_p"
                                                wire:model.defer="editingPermissions.can_delete_project_tasks_by_others"
                                                class="form-check-input">
                                        </div>
                                    </div>

                                    <div class="um-perm-group-title" style="grid-column:auto; margin-top:12px;">Tareas global</div>
                                    <div class="um-switch-row">
                                        <span class="um-switch-label">Crear cualquier tarea</span>
                                        <div class="form-check form-switch mb-0">
                                            <input type="checkbox" id="edit_t_cr_a"
                                                wire:model.defer="editingPermissions.can_create_any_task"
                                                class="form-check-input">
                                        </div>
                                    </div>
                                    <div class="um-switch-row">
                                        <span class="um-switch-label">Editar cualquier tarea</span>
                                        <div class="form-check form-switch mb-0">
                                            <input type="checkbox" id="edit_t_ed_a"
                                                wire:model.defer="editingPermissions.can_edit_any_task"
                                                class="form-check-input">
                                        </div>
                                    </div>
                                    <div class="um-switch-row">
                                        <span class="um-switch-label">Borrar cualquier tarea</span>
                                        <div class="form-check form-switch mb-0">
                                            <input type="checkbox" id="edit_t_dl_a"
                                                wire:model.defer="editingPermissions.can_delete_any_task"
                                                class="form-check-input">
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>{{-- /um-edit-panel --}}
                @endif

            </div>{{-- /um-row --}}

        @endforeach
    </div>

</div>