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

        <div class="form-group">
            <select wire:model.defer="status" class="task-status-select">
                <option value="pending" class="status-pending">Pendiente</option>
                <option value="in_progress" class="status-progress">En progreso</option>
                <option value="done" class="status-done">Hecha</option>
            </select>
        </div>
        <div class="form-group">
            <select wire:model.defer="priority" class="task-priority-select">
                <option value="mid" class="priority-progress">Media</option>
                <option value="high" class="priority-pending">Alta</option>
                <option value="low" class="priority-low">Baja</option>
            </select>
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