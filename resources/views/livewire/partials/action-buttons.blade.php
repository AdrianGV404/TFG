@php
    // Expected variables: $editingId (current editing id value), $id (row id)
    // If not provided, try common names
    $editingId = $editingId ?? null;
    $id = $id ?? null;
@endphp

<div class="actions">
    @if ($editingId === $id)
        <button
            type="button"
            class="btn btn-success btn-sm btn-loading"
            wire:click="saveEdit"
            wire:loading.attr="disabled"
            wire:target="saveEdit"
        >
            <span class="btn-text">Guardar</span>
            <span class="btn-spinner" wire:loading.delay wire:target="saveEdit">⏳</span>
        </button>

        <button type="button" class="btn btn-secondary btn-sm" wire:click="cancelEdit">Cancelar</button>

    @else

        <button type="button" class="btn btn-secondary btn-sm" wire:click="startEdit({{ $id }})">Editar</button>

        <button
            type="button"
            class="btn btn-danger btn-sm btn-loading"
            wire:click="confirmDelete({{ $id }})"
            wire:loading.attr="disabled"
            wire:target="delete({{ $id }})"
        >
            <span class="btn-text">Eliminar</span>
            <span class="btn-spinner" wire:loading.delay wire:target="delete({{ $id }})">⏳</span>
        </button>

    @endif
</div>
