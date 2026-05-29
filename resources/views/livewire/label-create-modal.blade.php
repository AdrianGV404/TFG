<div>
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5); z-index: 1055;">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header bg-light">
                        <h6 class="modal-title fw-bold"><i class="fas fa-tag text-primary me-2"></i>Nueva Etiqueta</h6>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="mb-2">
                            <label for="labelName" class="form-label small text-muted text-uppercase fw-bold">Nombre</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="labelName" 
                                   wire:model="name" 
                                   wire:keydown.enter="save" 
                                   placeholder="Ej: Urgente, Frontend..."
                                   autofocus>
                            @error('name') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary btn-sm" wire:click="closeModal">Cancelar</button>
                        <button type="button" class="btn btn-primary btn-sm" wire:click="save" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="save">Crear Etiqueta</span>
                            <span wire:loading wire:target="save">Creando...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>