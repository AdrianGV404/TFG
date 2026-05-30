<div>
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5); z-index: 1055;">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header bg-light">
                        <h6 class="modal-title fw-bold">
                            <i class="fas fa-tags text-primary me-2"></i>Gestión de Etiquetas
                        </h6>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-4">
                            <label for="labelName" class="form-label small text-muted text-uppercase fw-bold">
                                {{ $editId ? 'Editar Etiqueta' : 'Nueva Etiqueta' }}
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="labelName" wire:model="name"
                                    wire:keydown.enter="save" placeholder="Ej: Urgente, Frontend..." autofocus>
                                @if ($editId)
                                    <button class="btn btn-outline-secondary" type="button" wire:click="resetForm"
                                        title="Cancelar edición">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif
                            </div>
                            @error('name')
                                <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                            @enderror

                            <button type="button" class="btn btn-primary btn-sm w-100 mt-2" wire:click="save"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">
                                    {{ $editId ? 'Guardar Cambios' : 'Crear Etiqueta' }}
                                </span>
                                <span wire:loading wire:target="save">Procesando...</span>
                            </button>
                        </div>

                        <hr>

                        <div>
                            <label class="form-label small text-muted text-uppercase fw-bold mb-2">Etiquetas
                                Existentes</label>
                            <div class="list-group list-group-flush border rounded"
                                style="max-height: 220px; overflow-y: auto;">
                                @forelse($labels as $label)
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-2"
                                        wire:key="label-{{ $label->id }}">
                                        <span class="text-truncate small fw-medium"
                                            style="max-width: 65%;">{{ $label->name }}</span>
                                        <div class="btn-group flex-shrink-0">
                                            <button type="button" class="btn btn-sm btn-link text-secondary p-1"
                                                wire:click="editLabel({{ $label->id }})" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-link text-danger p-1"
                                                wire:click="deleteLabel({{ $label->id }})"
                                                wire:confirm="¿Seguro que deseas eliminar esta etiqueta? Se quitará automáticamente de las tareas asociadas."
                                                title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-3 text-center text-muted small">
                                        No hay etiquetas en tu espacio.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary btn-sm w-100"
                            wire:click="closeModal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
