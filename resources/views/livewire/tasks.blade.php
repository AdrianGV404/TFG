<div class="container tasks-wrapper">

    {{-- HEADER --}}
    <div class="page-header">
        <h2>Tareas del proyecto</h2>
    </div>

    {{-- FORMULARIO --}}
    <div class="task-form">
        <livewire:task-form :project="$project" />
    </div>

    <hr>

    {{-- CONTROLES --}}
    <div style="display:flex; justify-content:space-between; gap:12px; margin-bottom:12px; flex-wrap:wrap;">

        {{-- BUSCADORES --}}
        <div style="display:flex; gap:8px;">
            <input
                type="text"
                class="form-control form-control-sm"
                style="max-width:260px"
                placeholder="Buscar por título..."
                wire:model.live.debounce.400ms="searchTitle"
            >

            <input
                type="text"
                class="form-control form-control-sm"
                style="width:100px"
                placeholder="ID"
                wire:model.live="searchId"
            >
        </div>

        {{-- ORDEN Y PAGINACIÓN --}}
        <div style="display:flex; gap:10px;">
            <select
                class="form-select"
                style="min-width:190px;"
                wire:model.live="orderBy"
            >
                <option value="id_desc">ID ↓ (más recientes)</option>
                <option value="id_asc">ID ↑ (más antiguos)</option>
                <option value="status">Estado</option>
            </select>

            <select
                class="form-select form-select-sm"
                wire:model.live="perPage"
            >
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>

    {{-- TABLA --}}
    <table class="table">
        <thead>
            <tr>
                <th style="width:180px;">Acciones</th>
                <th style="width:80px;">ID</th>
                <th>Tarea</th>
                <th style="width:220px;">Estado</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($tasks as $task)

                @php
                    $color = match ($task->status) {
                        'pending' => '#ffc107',
                        'in_progress' => '#0d6efd',
                        'done' => '#198754',
                    };

                    $bg = match ($task->status) {
                        'pending' => '#fff8e1',
                        'in_progress' => '#e7f1ff',
                        'done' => '#eaf6ef',
                    };
                @endphp

                <tr wire:key="task-{{ $task->id }}--{{ $editingTaskId === $task->id ? 'editing' : 'view' }}">

                    {{-- ACCIONES --}}
                    <td>
                        <div style="display:flex; gap:6px;">
                            @if ($editingTaskId === $task->id)
                                <button
                                    class="btn btn-success btn-sm"
                                    wire:click="saveEdit"
                                >
                                    Guardar
                                </button>

                                <button
                                    class="btn btn-secondary btn-sm"
                                    wire:click="cancelEdit"
                                >
                                    Cancelar
                                </button>
                            @else
                                <button
                                    class="btn btn-secondary btn-sm"
                                    wire:click="startEdit({{ $task->id }})"
                                >
                                    Editar
                                </button>

                                <button
                                    class="btn btn-danger btn-sm"
                                    wire:click="delete({{ $task->id }})"
                                >
                                    Eliminar
                                </button>
                            @endif
                        </div>
                    </td>

                    {{-- ID --}}
                    <td class="text-muted">
                        #{{ $task->id }}
                    </td>

                    {{-- TAREA --}}
                    <td>
                        @if ($editingTaskId === $task->id)
                            <input
                                type="text"
                                class="form-control form-control-sm mb-1"
                                wire:model.defer="editingTitle"
                            >

                            <textarea
                                class="form-control form-control-sm"
                                rows="2"
                                wire:model.defer="editingDescription"
                                placeholder="Descripción"
                            ></textarea>
                        @else
                            <div class="task-title">
                                {{ $task->title }}
                            </div>

                            @if ($task->description)
                                <div class="task-description">
                                    {{ $task->description }}
                                </div>
                            @endif
                        @endif
                    </td>

                    {{-- ESTADO --}}
                    <td>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <span
                                style="
                                    width:14px;
                                    height:14px;
                                    border-radius:50%;
                                    background:{{ $color }};
                                    border:1px solid rgba(0,0,0,.25);
                                    display:inline-block;
                                "
                            ></span>

                            <select
                                class="form-select task-status-select"
                                style="background:{{ $bg }}; border-color:{{ $color }};"
                                wire:change="updateStatus({{ $task->id }}, $event.target.value)"
                            >
                                <option value="pending" @selected($task->status === 'pending')>Pendiente</option>
                                <option value="in_progress" @selected($task->status === 'in_progress')>En progreso</option>
                                <option value="done" @selected($task->status === 'done')>Hecha</option>
                            </select>
                        </div>
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="4" class="text-muted">
                        No hay tareas que coincidan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- PAGINADOR --}}
    <div class="mt-3">
        {{ $tasks->links() }}
    </div>
</div>
