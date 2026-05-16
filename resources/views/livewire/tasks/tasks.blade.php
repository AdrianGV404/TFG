<div class="container tasks-wrapper">

    {{-- HEADER --}}
    <div class="page-header">
        <h2 class="mb-0">Tareas del proyecto</h2>
    </div>

    {{-- FORMULARIO --}}
    <div class="d-flex"
        style="gap:24px; align-items:flex-start; margin-bottom:12px; flex-wrap:wrap; justify-content:space-between;">

        @if (!$showForm)
            <button type="button"
                    class="btn btn-primary btn-loading"
                    wire:click="openForm"
                    wire:loading.attr="disabled"
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

    <hr>

    {{-- CONTROLES --}}
    <div class="search-controls-wrapper mb-2">

        {{-- Checkbox encima, alineado a la derecha --}}
        <div class="d-flex justify-content-end mb-1">
            <div class="form-check">
                <input class="form-check-input"
                       type="checkbox"
                       id="showDeleted"
                       wire:model="showDeleted"
                       wire:change="$refresh">

                <label class="form-check-label ms-1" for="showDeleted">
                    Mostrar tareas eliminadas
                </label>
            </div>
        </div>

        {{-- Search controls --}}
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
                            <input type="text"
                                   class="form-control form-control-sm mb-1"
                                   wire:model.defer="editingTitle">

                            <textarea class="form-control form-control-sm auto-resize-textarea"
                                      rows="1"
                                      wire:model.defer="editingDescription"
                                      x-data
                                      x-init="$el.style.height = $el.scrollHeight + 'px'"
                                      x-on:input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"></textarea>

                        @else
                            <div class="task-title">{{ $task->title }}</div>

                            @if ($task->description)
                                <div class="task-description">{!! nl2br(e($task->description)) !!}</div>
                            @endif

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
                            <span>{{ $task->created_at->format('d/m/Y') }} | {{ $task->created_at->format('H:i') }}</span>
                        </div>
                        <div>
                            <span style="font-weight:500;">A:</span>
                            <span>{{ $task->updated_at->format('d/m/Y') }} | {{ $task->updated_at->format('H:i') }}</span>
                        </div>
                    </td>

                    {{-- ESTADO + PRIORIDAD --}}
                    <td>
                        <div class="task-status-wrapper"
                             style="display:flex; flex-direction:column; gap:4px;">

                            <select class="task-status-select {{ $status }} {{ $isEditing ? 'editable' : 'readonly' }}"
                                    wire:model.defer="editingStatus.{{ $task->id }}"
                                    @if (!$isEditing || $isDeleted) disabled @endif>

                                <option value="pending">Pendiente</option>
                                <option value="in_progress">En progreso</option>
                                <option value="done">Hecha</option>
                            </select>

                            <select wire:model.defer="editingPriority.{{ $task->id }}"
                                    class="task-priority-select {{ $isEditing ? 'editable' : 'readonly' }}"
                                    @if (!$isEditing || $isDeleted) disabled @endif>

                                @for ($i = 0; $i <= 10; $i++)
                                    @php
                                        $class = \App\Models\Task::PRIORITY_CLASSES[$i] ?? 'unknown';
                                    @endphp

                                    <option value="{{ $i }}"
                                            class="priority-{{ $class }}">
                                        {{ \App\Models\Task::PRIORITY_LABELS[$i] ?? 'Desconocida' }} ({{ $i }})
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
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    const searchWrapper = document.querySelector('.search-controls-wrapper');
    const table = document.querySelector('table');
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
            if (tds[i]) {
                th.style.width = tds[i].offsetWidth + 'px';
            }
        });
    }

    adjustSticky();
    window.addEventListener('resize', adjustSticky);
    window.addEventListener('scroll', adjustSticky);
});
</script>