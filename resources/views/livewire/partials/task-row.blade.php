<div class="task-row">

    @if ($editingTaskId !== $task->id)

        <div class="task-title">
            {{ $task->title }}
        </div>

        @if ($task->description)
            <div class="task-description">
                {{ $task->description }}
            </div>
        @endif

        <div class="actions actions-row">
            <button
                class="btn btn-secondary btn-sm"
                wire:click="edit({{ $task->id }})"
            >
                Editar
            </button>

            <button
                class="btn btn-danger btn-sm"
                wire:click="delete({{ $task->id }})"
            >
                Eliminar
            </button>
        </div>

    @else
        {{-- EDIT --}}
        <input type="text" wire:model.defer="editTitle">
        <textarea wire:model.defer="editDescription"></textarea>

        <select wire:model.defer="editStatus">
            <option value="pending">Pendiente</option>
            <option value="in_progress">En progreso</option>
            <option value="done">Hecha</option>
        </select>

        <select wire:model.defer="editPriority">
            <option value="very_high">Muy Alta</option>
            <option value="high">Alta</option>
            <option value="mid">Media</option>
            <option value="low">Baja</option>
            <option value="very_low">Muy Baja</option>
        </select>

        <div class="actions actions-row">
            <button class="btn btn-primary btn-sm" wire:click="update({{ $task->id }})">
                Guardar
            </button>
            <button class="btn btn-secondary btn-sm" wire:click="cancelEdit">
                Cancelar
            </button>
        </div>
    @endif

</div>
