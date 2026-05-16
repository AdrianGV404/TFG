@php
    $editingId = $editingId ?? null;
    $isTask = $isTask ?? false;

    $model = $isTask ? $task : $project;
    $id = $id ?? ($model->id ?? null);
    $title = $isTask ? $task->title : $project->name;
    $trashed = $model->trashed();
    $deleteEvent = $isTask ? 'delete-task' : 'delete-project';
    $restoreEvent = $isTask ? 'restore-task' : 'restore-project';
@endphp

<div class="actions">

    @if ($editingId === $id)
        <button type="button" class="btn btn-success btn-sm btn-loading" wire:click="saveEdit" wire:loading.attr="disabled"
            wire:target="saveEdit">
            <span class="btn-text">Guardar</span>
            <span class="btn-spinner" wire:loading.delay wire:target="saveEdit">⏳</span>
        </button>

        <button type="button" class="btn btn-secondary btn-sm" wire:click="cancelEdit">Cancelar</button>
    @else
        @if ($trashed)
            {{-- Botón Restaurar --}}
            <button type="button" class="btn btn-success btn-sm btn-loading" x-data
                data-id="{{ $id }}"
                data-title="Restaurar {{ $title }}"
                data-message="Vas a restaurar {{ $isTask ? 'la tarea' : 'el proyecto' }} <i>{{ $title }}</i>."
                data-action="{{ $restoreEvent }}"
                x-on:click="$dispatch('confirm-restore', {
                    id: $el.dataset.id,
                    title: $el.dataset.title,
                    message: $el.dataset.message,
                    action: $el.dataset.action
                })">
                Restaurar
            </button>

            {{-- Botón Eliminar Permanente --}}
            <button type="button" class="btn btn-danger btn-sm btn-loading" x-data
                data-id="{{ $id }}"
                data-title="Eliminar {{ $title }}"
                data-message="Ya está eliminado y se borrará permanentemente. Esta acción <b>no se puede deshacer</b>."
                data-action="{{ $deleteEvent }}"
                x-on:click="$dispatch('confirm-delete', {
                    id: $el.dataset.id,
                    title: $el.dataset.title,
                    message: $el.dataset.message,
                    action: $el.dataset.action
                })">
                Eliminar
            </button>
        @else
            {{-- Botón Editar --}}
            <button type="button" class="btn btn-secondary btn-sm" wire:click="startEdit({{ $id }})">
                Editar
            </button>

            {{-- Botón Eliminar Soft --}}
            <button type="button" class="btn btn-danger btn-sm btn-loading" x-data
                data-id="{{ $id }}"
                data-title="Eliminar {{ $title }}"
                data-message="¿Seguro que quieres eliminar {{ $isTask ? 'la tarea' : 'el proyecto' }} <i>{{ $title }}</i>? Esta acción solo la podrá deshacer el administrador."
                data-action="{{ $deleteEvent }}"
                x-on:click="$dispatch('confirm-delete', {
                    id: $el.dataset.id,
                    title: $el.dataset.title,
                    message: $el.dataset.message,
                    action: $el.dataset.action
                })">
                Eliminar
            </button>
        @endif
    @endif
</div>