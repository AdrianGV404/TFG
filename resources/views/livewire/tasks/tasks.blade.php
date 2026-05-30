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
            'labels' => $labels,
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
                    $status = $task->status;
                    $priority = $task->priority;
                    $isDeleted = $task->trashed();
                    $deletedAt = $isDeleted ? $task->deleted_at : null;
                    $daysToKeep = config('prune.days_to_keep_deleted.' . \App\Models\Task::class, 60);
                    $expiresAt = $isDeleted ? $deletedAt->copy()->addDays($daysToKeep) : null;
                    $daysLeft = $isDeleted ? now()->diffInDays($expiresAt, false) : null;
                @endphp

                <tr wire:key="task-{{ $task->id }}" class="{{ $isDeleted ? 'bg-softdeleted' : '' }}">

                    {{-- ACCIONES --}}
                    <td>
                        <div class="d-flex gap-1 align-items-center flex-wrap">
                            {{-- Enlace a detalle --}}
                            @if (!$isDeleted)
                                <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-secondary"
                                    title="Ver detalle">
                                    🔍
                                </a>
                            @endif

                            {{-- Eliminar / Restaurar --}}
                            @if ($isDeleted)
                                <button class="btn btn-sm btn-outline-success"
                                    wire:click="$dispatch('restore-task', { id: {{ $task->id }} })"
                                    title="Restaurar">
                                    ♻️
                                </button>
                                <button class="btn btn-sm btn-outline-danger" wire:click="delete({{ $task->id }})"
                                    wire:confirm="¿Eliminar definitivamente esta tarea?"
                                    title="Eliminar definitivamente">
                                    🗑
                                </button>
                                @if ($daysLeft !== null)
                                    <span class="text-muted" style="font-size:.75rem; white-space:nowrap;">
                                        Expira en {{ max(0, (int) $daysLeft) }}d
                                    </span>
                                @endif
                            @else
                                <button class="btn btn-sm btn-outline-danger" wire:click="delete({{ $task->id }})"
                                    wire:confirm="¿Eliminar esta tarea?" title="Eliminar">
                                    🗑
                                </button>
                            @endif
                        </div>
                    </td>

                    {{-- ID --}}
                    <td class="text-muted">#{{ $task->id }}</td>

                    {{-- TAREA --}}
                    <td class="col-task">
                        {{-- Etiquetas --}}
                        @if ($task->labels && $task->labels->isNotEmpty())
                            <div class="mb-1 d-flex flex-wrap gap-1">
                                @foreach ($task->labels as $taskLabel)
                                    <span class="badge"
                                        style="background-color:#6c757d;color:#fff;font-size:.72rem;padding:2px 6px;border-radius:4px;">
                                        {{ $taskLabel->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Título como enlace --}}
                        <a href="{{ route('tasks.show', $task) }}" class="task-title"
                            style="font-weight:600; text-decoration:none; color:inherit;">
                            {{ $task->title }}
                            @if ($isDeleted)
                                <span class="badge bg-danger ms-1" style="font-size:.7rem;">Eliminada</span>
                            @endif
                        </a>

                        {{-- Descripción (preview) --}}
                        @if ($task->description)
                            <div class="task-description text-muted"
                                style="font-size:.82rem; margin-top:2px; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">
                                {{ $task->description }}
                            </div>
                        @endif

                        {{-- Creado por --}}
                        @if ($task->creator)
                            <div class="d-flex align-items-center gap-1 mt-1">
                                <img src="{{ $task->creator->profile_photo_url }}" alt="{{ $task->creator->name }}"
                                    style="width:18px;height:18px;border-radius:50%;object-fit:cover;">
                                <span style="font-size:.75rem; color:#9ca3af;">{{ $task->creator->name }}</span>
                            </div>
                        @endif
                    </td>

                    {{-- TIMESTAMPS --}}
                    <td class="text-muted col-timestamps">
                        <div>
                            <span style="font-weight:500;">C:</span>
                            {{ $task->created_at->format('d/m/Y') }} | {{ $task->created_at->format('H:i') }}
                        </div>
                        <div>
                            <span style="font-weight:500;">A:</span>
                            {{ $task->updated_at->format('d/m/Y') }} | {{ $task->updated_at->format('H:i') }}
                        </div>

                        <div class="mt-1" style="border-top:1px dashed #ccc; padding-top:4px;">
                            <span style="font-weight:500;">Vence:</span>
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
                        </div>

                        @if ($isDeleted && $daysLeft !== null)
                            <div class="mt-1 text-danger" style="font-size:.78rem;">
                                ⏳ Expira en {{ max(0, (int) $daysLeft) }} días
                            </div>
                        @endif
                    </td>

                    {{-- ESTADO + PRIORIDAD (solo lectura) --}}
                    <td>
                        <div class="task-status-wrapper" style="display:flex; flex-direction:column; gap:4px;">
                            <select class="task-status-select {{ $status }} readonly" disabled>
                                <option value="pending" @selected($status === 'pending')>⏳ Pendiente</option>
                                <option value="in_progress" @selected($status === 'in_progress')>▶ En progreso</option>
                                <option value="on_hold" @selected($status === 'on_hold')>⏸ En pausa</option>
                                <option value="testing" @selected($status === 'testing')>🧪 En pruebas</option>
                                <option value="done" @selected($status === 'done')>✅ Hecha</option>
                            </select>

                            <select class="task-priority-select readonly" disabled>
                                @for ($i = 0; $i <= 10; $i++)
                                    @php $cls = \App\Models\Task::PRIORITY_CLASSES[$i] ?? 'unknown'; @endphp
                                    <option value="{{ $i }}" class="priority-{{ $cls }}"
                                        @selected($priority == $i)>
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

    <style>
        .task-status-select.on_hold {
            background-color: #f59e0b !important;
            color: #fff !important;
        }

        .task-status-select.testing {
            background-color: #8b5cf6 !important;
            color: #fff !important;
        }

        .task-status-select.readonly,
        .task-priority-select.readonly {
            opacity: .85;
            cursor: default;
        }

        .task-title:hover {
            text-decoration: underline !important;
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
