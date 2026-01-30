<div class="container tasks-wrapper">

    {{-- HEADER --}}
    <div class="page-header">
        <h2 class="mb-0">Tareas del proyecto</h2>
    </div>

    {{-- FORMULARIO + PIECHART --}}
    <div class="d-flex" style="gap:24px; align-items:flex-start; margin-bottom:12px; flex-wrap:wrap; justify-content:space-between;">

        {{-- FORMULARIO --}}
        <div class="form-col">
            <livewire:task-form
                :project="$project"
                wire:key="task-form-{{ $taskFormKey }}"
            />
        </div>

        {{-- PIECHART POR ESTADO --}}
        @php
            $done = $project->tasks()->where('status', 'done')->count();
            $inProgress = $project->tasks()->where('status', 'in_progress')->count();
            $pending = $project->tasks()->where('status', 'pending')->count();
            $total = $done + $inProgress + $pending;
        @endphp

        @if($total > 0)
            <div class="piecol">
                <h5 style="margin-bottom:8px; font-size:14px; text-align:center;">Estados</h5>
                @include('livewire.partials.pie-chart', [
                    'values' => [$done, $inProgress, $pending],
                    'labels' => ['Hecha','En progreso','Pendiente'],
                    'colors' => ['#4caf7a','#3399ff','#ffc107']
                ])
            </div>
        @endif

        {{-- PIECHART POR PRIORIDAD --}}
        @php
            $priorities = ['very_high','high','mid','low','very_low'];
            $priorityCounts = [];
            foreach ($priorities as $p) {
                $priorityCounts[$p] = $project->tasks()
                    ->whereIn('status',['pending','in_progress'])
                    ->where('priority',$p)->count();
            }
            $totalPriority = array_sum($priorityCounts);
        @endphp

        @if($totalPriority > 0)
            <div class="piecol">
                <h5 style="margin-bottom:8px; font-size:14px; text-align:center;">Prioridades</h5>
                @include('livewire.partials.pie-chart', [
                    'values' => array_values($priorityCounts),
                    'labels' => ['Muy Alta','Alta','Media','Baja','Muy Baja'],
                    'colors' => ['#dc3545','#fd7e14','#ffc107','#0dcaf0','#6c757d']
                ])
            </div>
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
                <th class="col-priority">Prioridad</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($tasks as $task)
                @php
                    $isEditing = $editingTaskId === $task->id;
                    $status = $editingStatus[$task->id] ?? $task->status;
                    $priority = $editingPriority[$task->id] ?? $task->priority;
                @endphp

                <tr wire:key="task-{{ $task->id }}--{{ $isEditing ? 'editing' : 'view' }}">
                    {{-- ACCIONES --}}
                    <td>
                        @include('livewire.partials.action-buttons', ['editingId' => $editingTaskId, 'id' => $task->id])
                    </td>

                    {{-- ID --}}
                    <td class="text-muted">#{{ $task->id }}</td>

                    {{-- TAREA --}}
                    <td>
                        @if ($isEditing)
                            <input type="text" class="form-control form-control-sm mb-1" wire:model.defer="editingTitle">
                            <textarea class="form-control form-control-sm auto-resize-textarea" rows="1"
                                wire:model.defer="editingDescription"
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
                            <select class="task-status-select {{ $status }} {{ $isEditing ? 'editable' : 'readonly' }}"
                                    wire:model.defer="editingStatus.{{ $task->id }}"
                                    @if(!$isEditing) disabled @endif>
                                <option value="pending" @selected($status === 'pending')>Pendiente</option>
                                <option value="in_progress" @selected($status === 'in_progress')>En progreso</option>
                                <option value="done" @selected($status === 'done')>Hecha</option>
                            </select>
                        </div>
                    </td>

                    {{-- PRIORIDAD --}}
                    <td>
                        <div class="task-status-wrapper">
                            <select class="task-priority-select {{ $priority }} {{ $isEditing ? 'editable' : 'readonly' }}"
                                    wire:model.defer="editingPriority.{{ $task->id }}"
                                    @if(!$isEditing) disabled @endif>
                                <option value="very_high" @selected($priority === 'very_high')>Muy Alta</option>
                                <option value="high" @selected($priority === 'high')>Alta</option>
                                <option value="mid" @selected($priority === 'mid')>Media</option>
                                <option value="low" @selected($priority === 'low')>Baja</option>
                                <option value="very_low" @selected($priority === 'very_low')>Muy Baja</option>
                            </select>
                        </div>
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
