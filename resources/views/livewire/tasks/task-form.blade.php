<div class="new-task">
    <h3>Nueva tarea</h3>

    <form wire:submit.prevent="save">
        <div class="form-group">
            <input type="text" wire:model.defer="title" placeholder="Título">
            @error('title')
                <small class="text-error">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <textarea wire:model.defer="description" placeholder="Descripción" x-data x-ref="textarea"
                x-on:input="$refs.textarea.style.height = 'auto'; $refs.textarea.style.height = $refs.textarea.scrollHeight + 'px';"
                class="auto-resize-textarea"></textarea>
        </div>

        <div class="form-group mb-3">
            <label class="form-label">Labels</label>
            <select multiple class="form-control" wire:model.defer="selected_labels" style="min-height: 120px;">
                @forelse($labels as $label)
                    <option value="{{ $label->id }}">{{ $label->name }}</option>
                @empty
                    <option disabled>No hay labels creados en este tenant.</option>
                @endforelse
            </select>
            <div class="text-muted mt-1" style="font-size: 0.8rem;">
                * Mantén presionado Ctrl (o Cmd en Mac) para seleccionar varias.
            </div>
            @error('selected_labels')
                <small class="text-error d-block" style="color: red;">{{ $message }}</small>
            @enderror
            @error('selected_labels.*')
                <small class="text-error d-block" style="color: red;">Error: Se ha seleccionado un label duplicado o
                    inválido.</small>
            @enderror
        </div>

        <div class="form-group-row">
            <div class="form-group">
                <label class="form-label">Estado</label>
                <select wire:model.defer="status" class="task-status-select">
                    <option value="pending" class="status-pending">Pendiente</option>
                    <option value="in_progress" class="status-progress">En progreso</option>
                    <option value="done" class="status-done">Hecha</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Prioridad</label>
                <select wire:model.defer="priority"
                    class="task-priority-select priority-{{ \App\Models\Task::PRIORITY_CLASSES[$priority] ?? 'mid' }}">
                    @for ($i = 0; $i <= 10; $i++)
                        @php
                            $class = \App\Models\Task::PRIORITY_CLASSES[$i] ?? 'unknown';
                        @endphp
                        <option value="{{ $i }}" class="priority-{{ $class }}"
                            @selected($priority == $i)>
                            {{ \App\Models\Task::PRIORITY_LABELS[$i] ?? 'Desconocida' }} ({{ $i }})
                        </option>
                    @endfor
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Fecha Límite</label>
                <input type="date" wire:model.defer="due_date" class="form-control">
                @error('due_date')
                    <small class="text-error">{{ $message }}</small>
                @enderror
            </div>
        </div>
        <br>
        <button class="btn btn-primary btn-loading" type="submit" wire:loading.attr="disabled" wire:target="save">
            <span class="btn-text">Crear tarea</span>
            <span class="btn-spinner" wire:loading.delay wire:target="save">
                Guardando…
            </span>
        </button>
        <button type="button" class="btn btn-secondary" wire:click="$dispatch('closeForm')"
            wire:loading.attr="disabled">
            Cancel
        </button>
    </form>
</div>
