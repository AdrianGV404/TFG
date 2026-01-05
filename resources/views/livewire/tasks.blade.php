<div class="container tasks-wrapper">

    {{-- HEADER --}}
    <div class="page-header">
        <h2 class="mb-0">Tareas del proyecto</h2>
    </div>

    {{-- FORMULARIO + PIECHART --}}
    <div class="d-flex" style="gap:24px; align-items:flex-start; margin-bottom:12px; flex-wrap:wrap; justify-content:space-between;">

        {{-- FORMULARIO (columna izquierda, más estrecha) --}}
        <div class="form-col">
            <livewire:task-form
                :project="$project"
                wire:key="task-form-{{ $taskFormKey }}"
            />
        </div>

        {{-- PIECHART (columna derecha) --}}
        @php
            $total = $project->tasks()->count();
            $pending = $project->tasks()->where('status', 'pending')->count();
            $inProgress = $project->tasks()->where('status', 'in_progress')->count();
            $done = $project->tasks()->where('status', 'done')->count();
        @endphp

        @if ($total > 0)
            @include('livewire.partials.pie-chart', ['total' => $total, 'done' => $done, 'inProgress' => $inProgress, 'pending' => $pending])
        @endif

    </div>

    <hr>

    {{-- CONTROLES --}}
    @include('livewire.partials.search-controls', [
        'textPlaceholder' => 'Buscar por título...',
        'allowStatusOrder' => true
    ])

    {{-- TABLA DE TAREAS --}}
    <table class="table">
        <thead>
            <tr>
                <th class="col-actions">Acciones</th>
                <th class="col-id">ID</th>
                <th>Tarea</th>
                <th class="col-status">Estado</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($tasks as $task)
                @php
                    $status = $taskStatuses[$task->id] ?? $task->status;
                    [$color, $bg, $optionClass] = match ($status) {
                        'pending' => ['#ffc107', '#fff8e1', 'status-pending'],
                        'in_progress' => ['#0d6efd', '#e7f1ff', 'status-progress'],
                        'done' => ['#198754', '#eaf6ef', 'status-done'],
                    };
                @endphp

                <tr wire:key="task-{{ $task->id }}--{{ $editingTaskId === $task->id ? 'editing' : 'view' }}">
                    {{-- ACCIONES --}}
                    <td>
                        @include('livewire.partials.action-buttons', ['editingId' => $editingTaskId, 'id' => $task->id])
                    </td>

                    {{-- ID --}}
                    <td class="text-muted">#{{ $task->id }}</td>

                    {{-- TAREA --}}
                    <td>
                        @if ($editingTaskId === $task->id)
                            <input type="text" class="form-control form-control-sm mb-1" wire:model.defer="editingTitle">
                            <textarea class="form-control form-control-sm auto-resize-textarea" rows="1"
                                wire:model.defer="editingDescription" placeholder="Descripción"
                                x-data x-init="$el.style.height = $el.scrollHeight + 'px'"
                                x-on:input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"></textarea>
                        @else
                            <div class="task-title">{{ $task->title }}</div>
                            @if ($task->description)
                                <div class="task-description">{!! nl2br(e($task->description)) !!}</div>
                            @endif
                        @endif
                    </td>

                    {{-- ESTADO --}}
                    <td>
                        <div class="task-status-wrapper">
                            @php
                                [$color, $bg] = match ($status) {
                                    'pending' => ['#ffc107', '#fff8e1'],
                                    'in_progress' => ['#0d6efd', '#e7f1ff'],
                                    'done' => ['#198754', '#eaf6ef'],
                                };
                            @endphp
                            <span class="task-status-dot {{ $status }}"></span>
                            <select class="task-status-select {{ $status }}"
                                wire:change="updateStatus({{ $task->id }}, $event.target.value)">
                                <option value="pending" class="status-pending" @selected($status === 'pending')>Pendiente</option>
                                <option value="in_progress" class="status-progress" @selected($status === 'in_progress')>En progreso</option>
                                <option value="done" class="status-done" @selected($status === 'done')>Hecha</option>
                            </select>
                        </div>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-muted">No hay tareas que coincidan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- PAGINADOR --}}
    <div class="mt-3">
        {{ $tasks->links() }}
    </div>

</div>
