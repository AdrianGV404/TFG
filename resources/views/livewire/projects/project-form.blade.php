<form wire:submit.prevent="save">

    {{-- LOADING --}}
    <div wire:loading.delay wire:target="save" class="text-muted loading-msg">
        Guardando proyecto…
    </div>

    <div class="form-group">
        <label>Nombre *</label>
        <input type="text" wire:model.defer="name" placeholder="Nombre del proyecto">
        @error('name')
            <small class="text-error">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group">
        <label>Descripción</label>
        <textarea wire:model.defer="description" placeholder="Descripción (opcional)"></textarea>
    </div>

    <div class="form-group">
        <label>Estado</label>
        <select wire:model.defer="status">
            <option value="active">Activo</option>
            <option value="archived">Archivado</option>
        </select>

        @error('status')
            <small class="text-error">{{ $message }}</small>
        @enderror
    </div>

    <div class="actions actions-row">
        <button type="submit" class="btn btn-primary btn-loading" wire:loading.attr="disabled" wire:target="save">
            <span class="btn-text">Crear proyecto</span>
            <span class="btn-spinner" wire:loading.delay wire:target="save">⏳</span>
        </button>

        <button type="button" class="btn btn-secondary" wire:click="$dispatch('closeForm')"
            wire:loading.attr="disabled">
            Cancelar
        </button>
    </div>

</form>
