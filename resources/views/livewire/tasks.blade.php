<div class="container tasks-wrapper">

    {{-- HEADER --}}
    <div class="page-header" style="display:flex; flex-direction:row; gap:12px; align-items:center; justify-content:flex-start;">
        <h2 class="mb-0" style="margin:0;">Tareas del proyecto</h2>
    </div>

    {{-- FORMULARIO + PIECHART --}}
    <div style="display:flex; gap:24px; align-items:flex-start; margin-bottom:12px; flex-wrap:wrap; justify-content:space-between;">

        {{-- FORMULARIO (columna izquierda, más estrecha) --}}
        <div style="flex:1 1 auto; min-width:280px;">
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
            @php
                $donePct = ($done / $total) * 100;
                $inProgressPct = ($inProgress / $total) * 100;
                $pendingPct = 100 - $donePct - $inProgressPct;

                $radius = 42;
                $circumference = 2 * pi() * $radius;

                $doneStroke = ($donePct / 100) * $circumference;
                $inProgressStroke = ($inProgressPct / 100) * $circumference;
                $pendingStroke = ($pendingPct / 100) * $circumference;
            @endphp

            <div style="flex:0 0 160px; display:flex; flex-direction:column; align-items:center;">

                {{-- PIECHART SVG --}}
                <svg width="100" height="100" viewBox="0 0 100 100">
                    {{-- BACKGROUND --}}
                    <circle cx="50" cy="50" r="{{ $radius }}" fill="none" stroke="#e9ecef" stroke-width="10" />
                    {{-- DONE --}}
                    <circle cx="50" cy="50" r="{{ $radius }}" fill="none" stroke="#4caf7a" stroke-width="10"
                        stroke-dasharray="{{ $doneStroke }} {{ $circumference }}" stroke-dashoffset="0"
                        transform="rotate(-90 50 50)" />
                    {{-- IN PROGRESS --}}
                    <circle cx="50" cy="50" r="{{ $radius }}" fill="none" stroke="#3399ff" stroke-width="10"
                        stroke-dasharray="{{ $inProgressStroke }} {{ $circumference }}"
                        stroke-dashoffset="-{{ $doneStroke }}" transform="rotate(-90 50 50)" />
                    {{-- PENDING --}}
                    <circle cx="50" cy="50" r="{{ $radius }}" fill="none" stroke="#ffc107" stroke-width="10"
                        stroke-dasharray="{{ $pendingStroke }} {{ $circumference }}"
                        stroke-dashoffset="-{{ $doneStroke + $inProgressStroke }}" transform="rotate(-90 50 50)" />
                    {{-- PORCENTAJE CENTRO --}}
                    <text x="50" y="55" text-anchor="middle" font-size="14" font-weight="600" fill="#212529">
                        {{ round($donePct) }}%
                    </text>
                </svg>

                {{-- LEYENDA --}}
                <div style="font-size:13px; display:flex; flex-direction:column; gap:4px; margin-top:8px; text-align:center;">
                    <div>✔ <strong>{{ $done }}</strong> ({{ round($donePct) }}%)</div>
                    <div>⏳ <strong>{{ $inProgress }}</strong> ({{ round($inProgressPct) }}%)</div>
                    <div>⏺ <strong>{{ $pending }}</strong> ({{ round($pendingPct) }}%)</div>
                </div>

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
                <th style="width:180px;">Acciones</th>
                <th style="width:80px;">ID</th>
                <th>Tarea</th>
                <th style="width:220px;">Estado</th>
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
                        <div style="display:flex; gap:6px;">
                            @if ($editingTaskId === $task->id)
                                <button class="btn btn-success btn-sm" wire:click="saveEdit">Guardar</button>
                                <button class="btn btn-secondary btn-sm" wire:click="cancelEdit">Cancelar</button>
                            @else
                                <button class="btn btn-secondary btn-sm" wire:click="startEdit({{ $task->id }})">Editar</button>
                                <button class="btn btn-danger btn-sm" wire:click="confirmDelete({{ $task->id }})">Eliminar</button>
                            @endif
                        </div>
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
                            <select class="task-status-select" style="background: {{ $bg }}; border-color: {{ $color }};"
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
