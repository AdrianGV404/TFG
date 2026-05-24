<form wire:submit.prevent="saveAll" enctype="multipart/form-data">

    <style>
        :root {
            --bg-body: #f8f9fa;
            --bg-card: #ffffff;
            --text-main: #212529;
            --text-muted: #6c757d;
            --border-color: #dee2e6;
            --input-bg: #ffffff;
        }

        .dark-mode-active {
            --bg-body: #0f172a;
            --bg-card: #1e293b;
            --text-main: #f1f5f9;
            --text-muted: #94a3b8;
            --border-color: #334155;
            --input-bg: #0f172a;
        }

        .theme-wrapper {
            background-color: var(--bg-body);
            color: var(--text-main);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh;
        }

        .dark-mode-active .card {
            background-color: var(--bg-card) !important;
            border-color: var(--border-color) !important;
            color: var(--text-main) !important;
        }

        .dark-mode-active .card-header,
        .dark-mode-active .card-footer {
            background-color: rgba(255,255,255,0.03) !important;
            border-color: var(--border-color) !important;
        }

        .dark-mode-active input:not([type="radio"]):not([type="checkbox"]),
        .dark-mode-active select {
            background-color: var(--input-bg) !important;
            border-color: var(--border-color) !important;
            color: var(--text-main) !important;
        }

        .dark-mode-active .text-muted,
        .dark-mode-active .form-label.text-muted {
            color: var(--text-muted) !important;
        }

        .dark-mode-active .bg-light {
            background-color: rgba(0,0,0,0.2) !important;
        }

        /* ── Selector de tema ── */
        .theme-selector-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .theme-card {
            position: relative;
            cursor: pointer;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 12px;
            transition: all 0.25s ease;
            background: var(--bg-card);
        }

        .theme-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        input[type="radio"]:checked + .theme-card {
            border-color: #0d6efd;
            box-shadow: 0 0 0 1px #0d6efd;
        }

        .theme-preview {
            height: 60px;
            border-radius: 8px;
            margin-bottom: 10px;
            display: flex;
            flex-direction: column;
            gap: 5px;
            padding: 8px;
        }

        .preview-light { background: #ffffff; border: 1px solid #eee; }
        .preview-dark  { background: #1e293b; border: 1px solid #334155; }

        .p-line { height: 6px; border-radius: 3px; background: #e2e8f0; }
        .preview-dark .p-line { background: #475569; }
        .p-line-primary { background: #0d6efd !important; opacity: 0.6; }

        .check-icon {
            position: absolute; top: 8px; right: 8px;
            color: #0d6efd; display: none;
        }
        input[type="radio"]:checked + .theme-card .check-icon { display: block; }

        /* ── Toggles ── */
        .toggle-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .toggle-desc .title { font-weight: 600; font-size: 0.85rem; }
        .toggle-desc .sub   { font-size: 0.75rem; color: var(--text-muted); }

        .toggle-switch { position: relative; width: 52px; height: 28px; }
        .toggle-switch input { display: none; }

        .toggle-slider {
            position: absolute; inset: 0;
            cursor: pointer;
            background-color: #cbd5e1;
            border-radius: 999px;
            transition: 0.3s;
        }

        .toggle-slider:before {
            content: "";
            position: absolute;
            height: 22px; width: 22px;
            left: 3px; top: 3px;
            background: #fff;
            border-radius: 50%;
            transition: 0.3s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .toggle-switch input:checked + .toggle-slider { background-color: #0d6efd; }
        .toggle-switch input:checked + .toggle-slider:before { transform: translateX(24px); }

        /* ── Canal de notificación ── */
        .channel-option input[type="radio"] { display: none; }

        .channel-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            padding: 10px 8px;
            border-radius: 10px;
            border: 2px solid var(--border-color);
            background: var(--bg-card);
            cursor: pointer;
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--text-muted);
            transition: all 0.2s;
            width: 100%;
        }

        .channel-btn i { font-size: 1.1rem; }
        .channel-btn:hover { border-color: #0d6efd; color: #0d6efd; }

        .channel-option input[type="radio"]:checked + .channel-btn {
            border-color: #0d6efd;
            color: #0d6efd;
            box-shadow: 0 0 0 1px #0d6efd;
            background: rgba(13,110,253,0.05);
        }
    </style>

    <div class="theme-wrapper {{ $theme === 'dark' ? 'dark-mode-active' : '' }} py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">
                    <i class="fas fa-user-gear me-2 text-primary"></i>Configuración de Usuario y Sistema
                </h2>
                <p class="text-muted">Personaliza tu experiencia y gestiona las reglas de negocio de la organización.</p>
            </div>

            <div class="row g-4">

                {{-- ══ COLUMNA IZQUIERDA: Perfil ══ --}}
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header py-3 border-bottom-0">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-id-card me-2 text-secondary"></i>Perfil de Usuario
                            </h5>
                        </div>

                        <div class="card-body">
                            {{-- Avatar --}}
                            <div class="text-center mb-4">
                                <div class="position-relative d-inline-block">
                                    @if ($photo)
                                        <img src="{{ $photo->temporaryUrl() }}"
                                             class="rounded-circle img-thumbnail shadow-sm"
                                             style="width:130px;height:130px;object-fit:cover;">
                                    @elseif ($profile_photo_path)
                                        <img src="{{ asset('storage/' . $profile_photo_path) }}?v={{ time() }}"
                                             class="rounded-circle img-thumbnail shadow-sm"
                                             style="width:130px;height:130px;object-fit:cover;">
                                    @else
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($name) }}&background=0D6EFD&color=fff"
                                             class="rounded-circle img-thumbnail shadow-sm"
                                             style="width:130px;height:130px;object-fit:cover;">
                                    @endif

                                    <div wire:loading wire:target="photo"
                                         class="position-absolute top-50 start-50 translate-middle">
                                        <div class="spinner-border text-primary"></div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <label class="btn btn-sm btn-outline-primary shadow-sm">
                                        <i class="fas fa-camera me-1"></i> Subir Nueva Foto
                                        <input type="file" wire:model="photo" class="d-none">
                                    </label>
                                    @error('photo')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Nombre Completo</label>
                                <input type="text" class="form-control" wire:model="name">
                                @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small">Correo Electrónico</label>
                                <input type="email" class="form-control" wire:model="email">
                                @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            <hr class="my-4">

                            {{-- Seguridad --}}
                            <div class="bg-light p-3 rounded-3">
                                <h6 class="fw-bold mb-3 small text-uppercase text-muted">
                                    <i class="fas fa-lock me-2"></i>Seguridad
                                </h6>
                                <div class="row g-2 mb-3">
                                    <div class="col-md-12 mb-2">
                                        <label class="small text-muted">Contraseña Actual</label>
                                        <input type="password" class="form-control form-control-sm"
                                               wire:model="current_password">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-muted">Nueva Contraseña</label>
                                        <input type="password" class="form-control form-control-sm"
                                               wire:model="new_password">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-muted">Confirmar Nueva</label>
                                        <input type="password" class="form-control form-control-sm"
                                               wire:model="new_password_confirmation">
                                    </div>
                                </div>
                                <button type="button" wire:click="updatePassword"
                                        class="btn btn-dark btn-sm w-100">
                                    Actualizar Contraseña
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══ COLUMNA DERECHA: Preferencias ══ --}}
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header py-3 border-bottom-0">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-sliders me-2 text-secondary"></i>Preferencias y Sistema
                            </h5>
                        </div>

                        <div class="card-body">

                            {{-- ── Tema ── --}}
                            <label class="form-label fw-bold small mb-3 text-uppercase text-muted">
                                Apariencia del Tema
                            </label>

                            <div class="theme-selector-container mb-4">
                                <label class="m-0">
                                    <input type="radio" value="light" wire:model="theme" class="d-none">
                                    <div class="theme-card">
                                        <i class="fas fa-circle-check check-icon"></i>
                                        <div class="theme-preview preview-light">
                                            <div class="p-line" style="width:80%"></div>
                                            <div class="p-line p-line-primary" style="width:50%"></div>
                                            <div class="p-line" style="width:90%"></div>
                                        </div>
                                        <div class="text-center">
                                            <span class="fw-bold d-block small">Modo Claro</span>
                                        </div>
                                    </div>
                                </label>

                                <label class="m-0">
                                    <input type="radio" value="dark" wire:model="theme" class="d-none">
                                    <div class="theme-card">
                                        <i class="fas fa-circle-check check-icon"></i>
                                        <div class="theme-preview preview-dark">
                                            <div class="p-line" style="width:80%"></div>
                                            <div class="p-line p-line-primary" style="width:50%"></div>
                                            <div class="p-line" style="width:90%"></div>
                                        </div>
                                        <div class="text-center">
                                            <span class="fw-bold d-block small">Modo Oscuro</span>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <hr class="my-3">

                            {{-- ── Notificaciones ── --}}
                            <label class="form-label fw-bold small mb-3 text-uppercase text-muted">
                                <i class="fas fa-bell me-1"></i> Notificaciones
                            </label>

                            {{-- Canal de entrega --}}
                            <div class="mb-3">
                                <label class="small text-muted mb-2 d-block">Canal de entrega</label>
                                <div class="d-flex gap-2">
                                    <label class="channel-option" style="flex:1;">
                                        <input type="radio" value="app" wire:model.live="notif_channel">
                                        <div class="channel-btn">
                                            <i class="fas fa-mobile-alt"></i>
                                            App
                                        </div>
                                    </label>
                                    <label class="channel-option" style="flex:1;">
                                        <input type="radio" value="email" wire:model.live="notif_channel">
                                        <div class="channel-btn">
                                            <i class="fas fa-envelope"></i>
                                            Email
                                        </div>
                                    </label>
                                    <label class="channel-option" style="flex:1;">
                                        <input type="radio" value="both" wire:model.live="notif_channel">
                                        <div class="channel-btn">
                                            <i class="fas fa-layer-group"></i>
                                            Ambos
                                        </div>
                                    </label>
                                </div>
                                @error('notif_channel')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Toggles de eventos --}}
                            <div class="toggle-row">
                                <div class="toggle-desc">
                                    <div class="title">
                                        <i class="fas fa-user-plus me-1" style="color:#3b82f6; font-size:.8rem;"></i>
                                        Nueva asignación
                                    </div>
                                    <div class="sub">Cuando se te asigne una tarea o proyecto</div>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" wire:model.live="notif_tasks">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div class="toggle-row">
                                <div class="toggle-desc">
                                    <div class="title">
                                        <i class="fas fa-clock me-1" style="color:#f59e0b; font-size:.8rem;"></i>
                                        Fecha de caducidad
                                    </div>
                                    <div class="sub">Aviso cuando una tarea vence pronto</div>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" wire:model.live="notif_alerts">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div class="toggle-row">
                                <div class="toggle-desc">
                                    <div class="title">
                                        <i class="fas fa-arrows-rotate me-1" style="color:#8b5cf6; font-size:.8rem;"></i>
                                        Cambio de estado
                                    </div>
                                    <div class="sub">Cuando otro usuario cambia el estado de una tarea</div>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" wire:model.live="notif_status_change">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <hr class="my-3">

                            {{-- ── Sistema ── --}}
                            <label class="form-label fw-bold small mb-3 text-uppercase text-muted">
                                <i class="fas fa-gear me-1"></i> Configuración del Sistema
                            </label>

                            <div class="toggle-row">
                                <div class="toggle-desc">
                                    <div class="title">Etiquetas de empleados</div>
                                    <div class="sub">Permitir mencionar empleados en comentarios</div>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" wire:model.live="allow_employee_tags">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div class="mb-3 mt-3">
                                <label class="small text-muted">Retención de datos (días)</label>
                                <input type="number" class="form-control" wire:model.live="retention_days">
                                @error('retention_days')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mt-4 p-3 rounded-3 bg-light border-start border-primary border-4">
                                <div class="d-flex">
                                    <i class="fas fa-circle-info text-primary me-3 mt-1"></i>
                                    <p class="mb-0 small text-muted">
                                        Recuerda guardar para mantener la configuración.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-transparent border-0 p-4">
                            <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</form>