<div class="container tasks-wrapper">

    {{-- HEADER --}}
    <div class="page-header">
        <h2 class="mb-0">Tareas del proyecto</h2>
    </div>

    {{-- FORMULARIO --}}
    <div class="d-flex"
        style="gap:24px; align-items:flex-start; margin-bottom:12px; flex-wrap:wrap; justify-content:space-between;">
        @if (!$showForm)
            <button type="button" class="btn btn-primary btn-loading" wire:click="openForm" wire:loading.attr="disabled"
                wire:target="openForm">
                <span class="btn-text">+ Nueva Tarea</span>
                <span class="btn-spinner" wire:loading.delay wire:target="openForm">⏳</span>
            </button>
        @endif

        {{-- FORMULARIO --}}
        @if ($showForm)
            <div class="form-col">
                <livewire:task-form :project="$project" wire:key="task-form-{{ $taskFormKey }}" />
            </div>
        @endif
    </div>

    {{-- PIECHARTS --}}
    @php
        $done = $project->tasks()->where('status', 'done')->count();
        $inProgress = $project->tasks()->where('status', 'in_progress')->count();
        $pending = $project->tasks()->where('status', 'pending')->count();
        $total = $done + $inProgress + $pending;

        $priorityCounts = [
            'very_high' => $project
                ->tasks()
                ->whereIn('priority', [0])
                ->count(),
            'high' => $project
                ->tasks()
                ->whereIn('priority', [1, 2, 3])
                ->count(),
            'mid' => $project
                ->tasks()
                ->whereIn('priority', [4, 5, 6])
                ->count(),
            'low' => $project
                ->tasks()
                ->whereIn('priority', [7, 8])
                ->count(),
            'very_low' => $project
                ->tasks()
                ->whereIn('priority', [9, 10])
                ->count(),
        ];
        $totalPriority = array_sum($priorityCounts);
    @endphp
    <div class="piecharts-fixed">
        @if ($total > 0 || $totalPriority > 0)
            <div class="piecharts-wrapper" style="display:flex; gap:24px; flex-wrap:wrap; margin-bottom:16px;">
                @if ($total > 0)
                    <div class="piecol" style="flex:1; min-width:200px;">
                        <h5 style="margin-bottom:8px; font-size:14px; text-align:center;">Estados</h5>
                        @include('livewire.partials.pie-chart', [
                            'values' => [$done, $inProgress, $pending],
                            'labels' => ['Hecha', 'En progreso', 'Pendiente'],
                            'colors' => ['#4caf7a', '#3399ff', '#ffc107'],
                        ])
                    </div>
                @endif

                @if ($totalPriority > 0)
                    <div class="piecol" style="flex:1; min-width:200px;">
                        <h5 style="margin-bottom:8px; font-size:14px; text-align:center;">Prioridades</h5>
                        @include('livewire.partials.pie-chart', [
                            'values' => array_values($priorityCounts),
                            'labels' => [
                                'Muy Alta (0)',
                                'Alta (1, 2, 3)',
                                'Media (4, 5, 6)',
                                'Baja (7, 8)',
                                'Muy Baja (9, 10)',
                            ],
                            'colors' => ['#dc3545', '#fd7e14', '#ffc107', '#0dcaf0', '#6c757d'],
                        ])
                    </div>
                @endif
            </div>
        @endif
    </div>
    <hr>

    {{-- CONTROLES --}}
    <div class="search-controls-wrapper">
        @include('livewire.partials.search-controls', [
            'textPlaceholder' => 'Buscar por título...',
            'allowStatusOrder' => true,
        ])
    </div>
    {{-- TABLA DE TAREAS --}}
    <table class="table">
        <thead>
            <tr>
                <th class="col-actions">Acciones</th>
                <th class="col-id">ID</th>
                <th class="col-task">Tarea</th>
                <th class="col-timestamps">Creada / Actualizada</th>
                <th class="col-status">Estado / Prioridad</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($tasks as $task)
                @php
                    $isEditing = $editingTaskId === $task->id;
                    $status = $editingStatus[$task->id] ?? $task->status;
                    $priority = $editingPriority[$task->id] ?? $task->priority;
                    $isDeleted = $task->trashed();
                    $deletedAt = $isDeleted ? $task->deleted_at : null;
                    $daysToKeep = config('prune.days_to_keep_deleted.' . \App\Models\Task::class, 60);
                    $expiresAt = $isDeleted ? $deletedAt->copy()->addDays($daysToKeep) : null;
                    $daysLeft = $isDeleted ? now()->diffInDays($expiresAt, false) : null;
                @endphp

                <tr wire:key="task-{{ $task->id }}--{{ $isEditing ? 'editing' : 'view' }}"
                    class="{{ $isDeleted ? 'bg-softdeleted' : '' }}">

                    {{-- ACCIONES --}}
                    <td>
                        @include('livewire.partials.action-buttons', [
                            'editingId' => $editingTaskId,
                            'task' => $task,
                            'isTask' => true
                        ])
                    </td>

                    {{-- ID --}}
                    <td class="text-muted">#{{ $task->id }}</td>

                    {{-- TAREA --}}
                    <td class="col-task">
                        @if ($isEditing)
                            <input type="text" class="form-control form-control-sm mb-1"
                                wire:model.defer="editingTitle">
                            <textarea class="form-control form-control-sm auto-resize-textarea" rows="1" wire:model.defer="editingDescription"
                                x-data x-init="$el.style.height = $el.scrollHeight + 'px'" x-on:input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"></textarea>
                        @else
                            <div class="task-title">{{ $task->title }}</div>
                            @if ($task->description)
                                <div class="task-description">{!! nl2br(e($task->description)) !!}</div>
                            @endif

                            {{-- SOLO SOFTDELETED: Expira debajo del contenido --}}
                            @if ($isDeleted)
                                <div class="expira-text text-danger mt-1" style="font-size:13px;">
                                    @if ($daysLeft <= 5)
                                        ⚠️
                                    @endif
                                    Expira en {{ $daysLeft }} días ({{ $expiresAt->format('d/m/Y') }})
                                </div>
                            @endif
                        @endif
                    </td>

                    {{-- TIMESTAMPS --}}
                    <td class="text-muted col-timestamps">
                        <div>
                            <span style="font-weight:500;">C:</span>
                            <span>{{ $task->created_at->format('d/m/Y') }} |
                                {{ $task->created_at->format('H:i') }}</span>
                        </div>
                        <div>
                            <span style="font-weight:500;">A:</span>
                            <span>{{ $task->updated_at->format('d/m/Y') }} |
                                {{ $task->updated_at->format('H:i') }}</span>
                        </div>
                    </td>

                    {{-- ESTADO + PRIORIDAD --}}
                    <td>
                        <div class="task-status-wrapper" style="display:flex; flex-direction:column; gap:4px;">
                            {{-- ESTADO --}}
                            <select
                                class="task-status-select {{ $status }} {{ $isEditing ? 'editable' : 'readonly' }}"
                                wire:model.defer="editingStatus.{{ $task->id }}"
                                @if (!$isEditing || $isDeleted) disabled @endif>
                                <option value="pending" @selected($status === 'pending')>Pendiente</option>
                                <option value="in_progress" @selected($status === 'in_progress')>En progreso</option>
                                <option value="done" @selected($status === 'done')>Hecha</option>
                            </select>

                            {{-- PRIORIDAD --}}
                            <select wire:model.defer="editingPriority.{{ $task->id }}"
                                class="task-priority-select {{ $isEditing ? 'editable' : 'readonly' }}"
                                @if (!$isEditing || $isDeleted) disabled @endif>
                                @for ($i = 0; $i <= 10; $i++)
                                    @php
                                        $class = \App\Models\Task::PRIORITY_CLASSES[$i] ?? 'unknown';
                                    @endphp
                                    <option value="{{ $i }}" class="priority-{{ $class }}"
                                        @selected($priority == $i)>
                                        {{ \App\Models\Task::PRIORITY_LABELS[$i] ?? 'Desconocida' }}
                                        ({{ $i }})
                                    </option>
                                @endfor
                            </select>
                        </div>

                        {{-- BOTÓN RESTAURAR --}}
                        @if ($isDeleted)
                            <button class="btn btn-sm btn-success mt-1" wire:click="restoreTask({{ $task->id }})">
                                Restaurar
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-muted">No hay tareas que coincidan.</td>
                </tr>
            @endforelse
        </tbody>

    </table>

    {{-- PAGINADOR --}}
    <div class="mt-3">
        {{ $tasks->links() }}
    </div>
</div>
