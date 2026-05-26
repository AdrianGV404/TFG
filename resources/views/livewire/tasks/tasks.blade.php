<div class="container tasks-wrapper" x-data="{ openModal: false }">
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

        @if ($showForm)
            <div class="form-col">
                <livewire:task-form :project="$project" wire:key="task-form-{{ $taskFormKey }}" />
            </div>
        @endif
    </div>

    <hr>

    {{-- CONTROLES --}}
    <div class="search-controls-wrapper mb-2">
        <div class="d-flex justify-content-end mb-1">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="showDeleted" wire:model="showDeleted"
                    wire:change="$refresh">
                <label class="form-check-label ms-1" for="showDeleted">
                    Mostrar tareas eliminadas
                </label>
            </div>
        </div>

        @include('livewire.partials.search-controls', [
            'textPlaceholder' => 'Buscar por título...',
            'allowStatusOrder' => true,
            'labels'           => $labels
        ])
    </div>

    {{-- TABLA --}}
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
                            'isTask' => true,
                        ])
                    </td>

                    {{-- ID --}}
                    <td class="text-muted">#{{ $task->id }}</td>

                    {{-- TAREA --}}
                    <td class="col-task">
                        @if ($isEditing)
                            <input type="text" class="form-control form-control-sm mb-1"
                                wire:model.defer="editingTitle">

                            <textarea class="form-control form-control-sm auto-resize-textarea mb-2" rows="1"
                                wire:model.defer="editingDescription" x-data x-init="$el.style.height = $el.scrollHeight + 'px'"
                                x-on:input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"></textarea>

                            <select multiple class="form-select form-select-sm mb-1"
                                wire:model.defer="editingLabelIds.{{ $task->id }}" style="max-height: 100px;">
                                @foreach ($labels as $label)
                                    <option value="{{ $label->id }}">{{ $label->name }}</option>
                                @endforeach
                            </select>
                            <div class="text-muted mb-2" style="font-size: 0.7rem;">
                                * Mantén presionado Ctrl (o Cmd en Mac) para seleccionar varias.
                            </div>
                            @error('editingLabelIds.' . $task->id . '.*')
                                <small class="text-error d-block" style="color: red;">Error: No puedes duplicar
                                    labels.</small>
                            @enderror
                        @else
                            @if ($task->labels && $task->labels->isNotEmpty())
                                <div class="mb-1 d-flex flex-wrap gap-1">
                                    @foreach ($task->labels as $taskLabel)
                                        <span class="badge"
                                            style="background-color: #6c757d; color: #fff; font-size: 0.72rem; padding: 2px 6px; border-radius: 4px; margin-right: 2px;">
                                            {{ $taskLabel->text }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            <div class="task-title">{{ $task->title }}</div>

                            @if ($task->description)
                                <div class="task-description">{!! nl2br(e($task->description)) !!}</div>
                            @endif
                        @endif
                    </td>

                    {{-- TIMESTAMPS Y CADUCIDAD --}}
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

                        <div class="mt-1" style="border-top:1px dashed #ccc; padding-top:4px;">
                            <span style="font-weight:500;">Vence:</span>
                            @if ($isEditing)
                                <input type="date" class="form-control form-control-sm"
                                    wire:model.defer="editingDueDate.{{ $task->id }}"
                                    style="display:inline-block; width:auto;">
                            @else
                                @if ($task->due_date)
                                    @php $isOverdue = $task->due_date->isPast() && !$task->isDone(); @endphp
                                    <span class="{{ $isOverdue ? 'text-danger fw-bold' : '' }}">
                                        {{ $task->due_date->format('d/m/Y') }}
                                        @if ($isOverdue)
                                            ⚠️
                                        @endif
                                    </span>
                                @else
                                    <span style="font-style:italic;">Sin fecha</span>
                                @endif
                            @endif
                        </div>
                    </td>

                    {{-- ESTADO + PRIORIDAD --}}
                    <td>
                        <div class="task-status-wrapper" style="display:flex; flex-direction:column; gap:4px;">
                            <select
                                class="task-status-select {{ $status }} {{ $isEditing ? 'editable' : 'readonly' }}"
                                wire:model.defer="editingStatus.{{ $task->id }}"
                                @if (!$isEditing || $isDeleted) disabled @endif>
                                <option value="pending">⏳ Pendiente</option>
                                <option value="in_progress">▶ En progreso</option>
                                <option value="on_hold">⏸ En pausa</option>
                                <option value="testing">🧪 En pruebas</option>
                                <option value="done">✅ Hecha</option>
                            </select>

                            <select wire:model.defer="editingPriority.{{ $task->id }}"
                                class="task-priority-select {{ $isEditing ? 'editable' : 'readonly' }}"
                                @if (!$isEditing || $isDeleted) disabled @endif>
                                @for ($i = 0; $i <= 10; $i++)
                                    @php $class = \App\Models\Task::PRIORITY_CLASSES[$i] ?? 'unknown'; @endphp
                                    <option value="{{ $i }}" class="priority-{{ $class }}">
                                        {{ \App\Models\Task::PRIORITY_LABELS[$i] ?? 'Desconocida' }}
                                        ({{ $i }})
                                    </option>
                                @endfor
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

    {{-- MODAL TIEMPO MANUAL --}}
    <div class="modal fade {{ $showManualTimeModal ? 'show d-block' : '' }}"
        style="background:rgba(0,0,0,.5); {{ $showManualTimeModal ? 'display:block;' : 'display:none;' }}"
        tabindex="-1" wire:key="manual-time-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Añadir tiempo manual</h5>
                    <button type="button" class="btn-close" wire:click="closeManualTimeModal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <label>Horas</label>
                            <input type="number" min="0" class="form-control" wire:model="manualHours">
                            @error('manualHours')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col">
                            <label>Minutos</label>
                            <input type="number" min="0" max="59" class="form-control"
                                wire:model="manualMinutes">
                            @error('manualMinutes')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        wire:click="closeManualTimeModal">Cancelar</button>
                    <button type="button" class="btn btn-primary" wire:click="saveManualTime">Guardar
                        tiempo</button>
                </div>
            </div>
        </div>
    </div>


    <style>
        .task-status-select.on_hold {
            background-color: #f59e0b !important;
            color: #fff !important;
        }

        .task-status-select.testing {
            background-color: #8b5cf6 !important;
            color: #fff !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchWrapper = document.querySelector('.search-controls-wrapper');
            const table = document.querySelector('table');
            if (!searchWrapper || !table) return;

            const tableHead = table.querySelector('thead');
            const navbarHeight = 64;

            function adjustSticky() {
                const searchHeight = searchWrapper.offsetHeight;
                document.documentElement.style.setProperty('--search-height', searchHeight + 'px');

                tableHead.querySelectorAll('th').forEach(th => {
                    th.style.top = (navbarHeight + searchHeight + 15) + 'px';
                });

                const firstRow = table.querySelector('tbody tr');
                if (!firstRow) return;

                const tds = firstRow.querySelectorAll('td');
                const ths = tableHead.querySelectorAll('th');
                ths.forEach((th, i) => {
                    if (tds[i]) th.style.width = tds[i].offsetWidth + 'px';
                });
            }

            adjustSticky();
            window.addEventListener('resize', adjustSticky);
            window.addEventListener('scroll', adjustSticky);
        });
    </script>
</div>
