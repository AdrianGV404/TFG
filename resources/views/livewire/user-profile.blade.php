<form wire:submit.prevent="saveAll" enctype="multipart/form-data">

<style>
    /* 1. DEFINICIÓN DE COLORES (VARIABLES) */
    :root {
        --bg-body: #f8f9fa;
        --bg-card: #ffffff;
        --text-main: #212529;
        --text-muted: #6c757d;
        --border-color: #dee2e6;
        --input-bg: #ffffff;
    }

    /* 2. COLORES MODO OSCURO (AGRADABLES A LA VISTA) */
    .dark-mode-active {
        --bg-body: #0f172a;    /* Azul noche profundo */
        --bg-card: #1e293b;    /* Gris azulado para las tarjetas */
        --text-main: #f1f5f9;  /* Blanco suave */
        --text-muted: #94a3b8; /* Gris azulado claro */
        --border-color: #334155;
        --input-bg: #0f172a;
    }

    /* 3. APLICACIÓN AUTOMÁTICA A TODO EL CONTENEDOR */
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

    .dark-mode-active input:not([type="radio"]), 
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

    /* ESTILOS DE LAS TARJETAS SELECTORAS */
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
    .preview-dark { background: #1e293b; border: 1px solid #334155; }

    .p-line { height: 6px; border-radius: 3px; background: #e2e8f0; }
    .preview-dark .p-line { background: #475569; }
    .p-line-primary { background: #0d6efd !important; opacity: 0.6; }

    .check-icon {
        position: absolute;
        top: 8px;
        right: 8px;
        color: #0d6efd;
        display: none;
    }

    input[type="radio"]:checked + .theme-card .check-icon {
        display: block;
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
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header py-3 border-bottom-0">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-id-card me-2 text-secondary"></i>Perfil de Usuario
                        </h5>
                    </div>

                    <div class="card-body">
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
                                @error('photo') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Nombre Completo</label>
                            <input type="text" class="form-control" wire:model="name">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small">Correo Electrónico</label>
                            <input type="email" class="form-control" wire:model="email">
                        </div>

                        <hr class="my-4">

                        <div class="bg-light p-3 rounded-3">
                            <h6 class="fw-bold mb-3 small text-uppercase text-muted">
                                <i class="fas fa-lock me-2"></i>Seguridad
                            </h6>
                            <div class="row g-2 mb-3">
                                <div class="col-md-12 mb-2">
                                    <label class="small text-muted">Contraseña Actual</label>
                                    <input type="password" class="form-control form-control-sm" wire:model="current_password">
                                </div>
                                <div class="col-md-6">
                                    <label class="small text-muted">Nueva Contraseña</label>
                                    <input type="password" class="form-control form-control-sm" wire:model="new_password">
                                </div>
                                <div class="col-md-6">
                                    <label class="small text-muted">Confirmar Nueva</label>
                                    <input type="password" class="form-control form-control-sm" wire:model="new_password_confirmation">
                                </div>
                            </div>
                            <button type="button" wire:click="updatePassword" class="btn btn-dark btn-sm w-100">
                                Actualizar Contraseña
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header py-3 border-bottom-0">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-sliders me-2 text-secondary"></i>Preferencias y Sistema
                        </h5>
                    </div>

                    <div class="card-body">
                        <label class="form-label fw-bold small mb-3 text-uppercase text-muted">Apariencia del Tema</label>

                        <div class="theme-selector-container">
                            <label class="m-0">
                                <input type="radio" value="light" wire:model="theme" class="d-none">
                                <div class="theme-card">
                                    <i class="fas fa-circle-check check-icon"></i>
                                    <div class="theme-preview preview-light">
                                        <div class="p-line" style="width: 80%"></div>
                                        <div class="p-line p-line-primary" style="width: 50%"></div>
                                        <div class="p-line" style="width: 90%"></div>
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
                                        <div class="p-line" style="width: 80%"></div>
                                        <div class="p-line p-line-primary" style="width: 50%"></div>
                                        <div class="p-line" style="width: 90%"></div>
                                    </div>
                                    <div class="text-center">
                                        <span class="fw-bold d-block small">Modo Oscuro</span>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="mt-4 p-3 rounded-3 bg-light border-start border-primary border-4">
                            <div class="d-flex">
                                <i class="fas fa-circle-info text-primary me-3 mt-1"></i>
                                <p class="mb-0 small text-muted">
                                    El cambio se aplica instantáneamente. Recuerda guardar para mantener la preferencia.
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