<div class="container tasks-wrapper">

    {{-- HEADER --}}
    <div class="page-header" style="display:flex; align-items:center; gap:14px;">
        <h2 class="mb-0">Tareas del proyecto</h2>

        @php
            $total = $project->tasks()->count();
            $pending = $project->tasks()->where('status', 'pending')->count();
            $inProgress = $project->tasks()->where('status', 'in_progress')->count();
            $done = $project->tasks()->where('status', 'done')->count();
        @endphp

        @if ($total > 0)
            <div style="margin-top:6px;">
                <div
                    style="
                        display:flex;
                        height:14px;
                        width:260px;
                        border-radius:8px;
                        overflow:hidden;
                        box-shadow: inset 0 0 0 1px rgba(0,0,0,.05);
                        margin: 0 auto;
                    "
                >
                    <div style="width: {{ $done * 100 / $total }}%; background:#4caf7a;"></div>
                    <div style="width: {{ $inProgress * 100 / $total }}%; background:#3399ff;"></div>
                    <div style="width: {{ $pending * 100 / $total }}%; background:#ffc107;"></div>
                </div>

                <small
                    class="text-muted"
                    style="
                        display:block;
                        width:260px;
                        margin:4px auto 0;
                        text-align:center;
                    "
                >
                    ✔ {{ $done }} ({{ round($done * 100 / $total) }}%)
                    &nbsp;&nbsp;
                    ⏳ {{ $inProgress }} ({{ round($inProgress * 100 / $total) }}%)
                    &nbsp;&nbsp;
                    ⏺ {{ $pending }} ({{ round($pending * 100 / $total) }}%)
                </small>

            </div>
        @endif

    </div>

    {{-- FORMULARIO --}}
    <div class="task-form">
        <livewire:task-form
            :project="$project"
            wire:key="task-form-{{ $taskFormKey }}"
        />
    </div>

    <hr>

    {{-- CONTROLES --}}
    @include('livewire.partials.search-controls', [
        'textPlaceholder' => 'Buscar por título...',
        'allowStatusOrder' => true
    ])

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
                    // Determinar estado actual
                    $status = $taskStatuses[$task->id] ?? $task->status;

                    // Color de la bola y fondo del select según el estado
                    [$color, $bg, $optionClass] = match ($status) {
                        'pending' => ['#ffc107', '#fff8e1', 'status-pending'],
                        'in_progress' => ['#0d6efd', '#e7f1ff', 'status-progress'],
                        'done' => ['#198754', '#eaf6ef', 'status-done'],
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
                                    wire:click="confirmDelete({{ $task->id }})"
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
                            class="form-control form-control-sm auto-resize-textarea"
                            rows="1"
                            wire:model.defer="editingDescription"
                            placeholder="Descripción"
                            x-data
                            x-init="$el.style.height = $el.scrollHeight + 'px'"
                            x-on:input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"
                        ></textarea>

                        @else
                            <div class="task-title">
                                {{ $task->title }}
                            </div>

                            @if ($task->description)
                                <div class="task-description">
                                    {!! nl2br(e($task->description)) !!}
                                </div>
                            @endif
                        @endif
                    </td>

                    {{-- ESTADO --}}
                    <td>
                        <div class="task-status-wrapper">
                            @php
                                $status = $task->status;

                                [$color, $bg] = match ($status) {
                                    'pending' => ['#ffc107', '#fff8e1'],
                                    'in_progress' => ['#0d6efd', '#e7f1ff'],
                                    'done' => ['#198754', '#eaf6ef'],
                                };
                            @endphp

                            <span class="task-status-dot {{ $status }}"></span>

                            <select
                                class="task-status-select"
                                style="background: {{ $bg }}; border-color: {{ $color }};"
                                wire:change="updateStatus({{ $task->id }}, $event.target.value)"
                            >
                                <option value="pending" class="status-pending" @selected($status === 'pending')>Pendiente</option>
                                <option value="in_progress" class="status-progress" @selected($status === 'in_progress')>En progreso</option>
                                <option value="done" class="status-done" @selected($status === 'done')>Hecha</option>
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
