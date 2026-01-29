<div class="task-card">

    <div class="task-header">

        {{-- DESPLEGABLE DE ESTADO --}}
        <select
            class="task-status {{ $status }}"
            wire:model="status"
        >
            <option value="pending">Pendiente</option>
            <option value="in_progress">En progreso</option>
            <option value="done">Hecha</option>
        </select>

        {{-- DESPLEGABLE DE PRIORIDAD --}}
        <select
            class="task-priority {{ $priority }}"
            wire:model="priority"
        >
            <option value="very_high">Muy Alta</option>
            <option value="high">Alta</option>
            <option value="mid">Media</option>
            <option value="low">Baja</option>
            <option value="very_low">Muy Baja</option>
        </select>

        {{-- ELIMINAR --}}
        <button
            class="btn btn-danger btn-sm"
            wire:click="delete"
        >
            ✕
        </button>
    </div>

    {{-- TÍTULO --}}
    <div class="task-title">
        {{ $task->title }}
    </div>

    {{-- DESCRIPCIÓN --}}
    @if ($task->description)
        <div class="task-description">
            {{ $task->description }}
        </div>
    @endif

</div>
