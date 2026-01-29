<div class="new-task">

    <h3>Nueva tarea</h3>

    <form wire:submit.prevent="save">
        <div class="form-group">
            <input
                type="text"
                wire:model.defer="title"
                placeholder="Título"
            >
            @error('title')
                <small class="text-error">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <textarea
                wire:model.defer="description"
                placeholder="Descripción"
                x-data
                x-ref="textarea"
                x-on:input="$refs.textarea.style.height = 'auto'; $refs.textarea.style.height = $refs.textarea.scrollHeight + 'px';"
                class="auto-resize-textarea"
            ></textarea>
        </div>
        <div class="form-group-row">
            <!-- ESTADO -->
            <div class="form-group">
                <label class="form-label">Estado</label>
                <select wire:model.defer="status" class="task-status-select">
                    <option value="pending" class="status-pending">Pendiente</option>
                    <option value="in_progress" class="status-progress">En progreso</option>
                    <option value="done" class="status-done">Hecha</option>
                </select>
            </div>

            <!-- PRIORIDAD -->
            <div class="form-group">
                <label class="form-label">Prioridad</label>
                <select wire:model.defer="priority" class="task-priority-select">
                    <option value="very_high" class="priority-very_high" @selected($priority === 'very_high')>Muy Alta</option>
                    <option value="high" class="priority-high" @selected($priority === 'high')>Alta</option>
                    <option value="mid" class="priority-mid" @selected($priority === 'mid')>Media</option>
                    <option value="low" class="priority-low" @selected($priority === 'low')>Baja</option>
                    <option value="very_low" class="priority-very_low" @selected($priority === 'very_low')>Muy Baja</option>
                </select>
            </div>
        </div>
        <br>
        <button
            class="btn btn-primary btn-loading"
            type="submit"
            wire:loading.attr="disabled"
            wire:target="save"
        >
            <span class="btn-text">Crear tarea</span>
            <span class="btn-spinner" wire:loading.delay wire:target="save">
                Guardando…
            </span>
        </button>
    </form>
</div>