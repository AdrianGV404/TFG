@php
    $editingId = $editingId ?? null;
    $project = $project ?? null;
    $id = $id ?? ($project->id ?? null);
@endphp

<div class="actions">

    {{-- Modo edición inline --}}
    @if ($editingId === $id)
        <button type="button" class="btn btn-success btn-sm btn-loading" wire:click="saveEdit" wire:loading.attr="disabled"
            wire:target="saveEdit">
            <span class="btn-text">Guardar</span>
            <span class="btn-spinner" wire:loading.delay wire:target="saveEdit">⏳</span>
        </button>

        <button type="button" class="btn btn-secondary btn-sm" wire:click="cancelEdit">Cancelar</button>
    @else
        @if ($project && $project->trashed())
            {{-- Proyecto softdeleted: Restaurar + Eliminar --}}
            <button type="button" class="btn btn-success btn-sm btn-loading" x-data
                x-on:click="$dispatch('confirm-restore', {
                    id: {{ $id }},
                    title: 'Restaurar proyecto',
                    message: 'Vas a restarurar el proyecto <i>{{ $project->name }}</i>.',
                    action: 'restore-project'
                })">
                Restaurar
            </button>


            <button type="button" class="btn btn-danger btn-sm btn-loading" x-data
                x-on:click="$dispatch('confirm-delete', {
                    id: {{ $id }},
                    title: 'Eliminar proyecto',
                    message: 'Este proyecto ya está eliminado y se borrará permanentemente.<br>Esta acción <b>no se puede deshacer</b>.',
                    action: 'delete-project',
                    isPermanent: true
                })">
                Eliminar
            </button>
        @else
            {{-- Proyecto normal: Editar + Eliminar --}}
            <button type="button" class="btn btn-secondary btn-sm" wire:click="startEdit({{ $id }})">
                Editar
            </button>

            <button type="button" class="btn btn-danger btn-sm btn-loading" x-data
                x-on:click="$dispatch('confirm-delete', {
                    id: {{ $id }},
                    title: 'Eliminar proyecto',
                    message: '¿Seguro que quieres eliminar el proyecto <br> <i>{{ $project->name }}</i>?<br>Esta acción solo la podrá deshacer el administrador.',
                    action: 'delete-project',
                    isPermanent: false
                })">
                Eliminar
            </button>
        @endif

    @endif
</div>
