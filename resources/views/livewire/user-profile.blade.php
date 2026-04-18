<form wire:submit.prevent="saveAll" enctype="multipart/form-data">

<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-dark">
            <i class="fas fa-user-gear me-2 text-primary"></i>Configuración de Usuario y Sistema
        </h2>
        <p class="text-muted">Personaliza tu experiencia y gestiona las reglas de negocio de la organización.</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom-0">
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
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($name) }}" 
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
                        <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small">Correo Electrónico</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" wire:model="email">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <hr class="my-4">

                    <div class="bg-light p-3 rounded-3">
                        <h6 class="fw-bold mb-3 small text-uppercase text-muted">
                            <i class="fas fa-lock me-2"></i>Seguridad
                        </h6>

                        <div class="mb-3">
                            <label class="small text-muted">Contraseña Actual</label>
                            <input type="password" class="form-control form-control-sm" wire:model="current_password">
                        </div>

                        <div class="row g-2 mb-3">
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
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-sliders me-2 text-secondary"></i>Preferencias y Sistema
                    </h5>
                </div>

                <div class="card-body">

                    <label class="form-label fw-bold small">Tema</label>

                    <input type="radio" value="light" wire:model="theme"> Claro
                    <input type="radio" value="dark" wire:model="theme"> Oscuro

                </div>

                <div class="card-footer bg-white border-0 p-4">
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        Guardar Cambios
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

</form>