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
            <button type="button" class="btn btn-success btn-sm btn-loading" x-data
                x-on:click="$dispatch('confirm-restore', {
                id: {{ $id }},
                title: 'Restaurar {{ $title }}',
                message: 'Vas a restaurar {{ $isTask ? 'la tarea' : 'el proyecto' }} <i>{{ $title }}</i>.',
                action: '{{ $restoreEvent }}'})">
                Restaurar
            </button>

            <button type="button" class="btn btn-danger btn-sm btn-loading" x-data
                x-on:click="$dispatch('confirm-delete', {
                    id: {{ $id }},
                    title: 'Eliminar {{ $title }}',
                    message: '{{ $trashed ? 'Ya está eliminado y se borrará permanentemente.' : '' }}. Esta acción <b>no se puede deshacer</b>.',
                    action: '{{ $deleteEvent }}',
                    isPermanent: true
                })">
                Eliminar
            </button>
        @else
            <button type="button" class="btn btn-secondary btn-sm" wire:click="startEdit({{ $id }})">
                Editar
            </button>

            <button type="button" class="btn btn-danger btn-sm btn-loading" x-data
                x-on:click="$dispatch('confirm-delete', {
                    id: {{ $id }},
                    title: 'Eliminar {{ $title }}',
                    message: '¿Seguro que quieres eliminar {{ $isTask ? 'la tarea' : 'el proyecto' }} <i>{{ $title }}</i>? Esta acción solo la podrá deshacer el administrador.',
                    action: '{{ $deleteEvent }}',
                    isPermanent: false
                })">
                Eliminar
            </button>
        @endif
    @endif
</div>