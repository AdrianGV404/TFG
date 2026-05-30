<div class="container task-detail-wrapper" style="max-width: 860px; padding: 2rem 1rem;">

    {{-- BREADCRUMB --}}
    <nav class="mb-3" style="font-size:.875rem; color:#6b7280;">
        <a href="{{ route('projects') }}" style="color:inherit;">Proyectos</a>
        <span class="mx-1">/</span>
        <a href="{{ route('projects.show', $task->project) }}" style="color:inherit;">{{ $task->project->name }}</a>
        <span class="mx-1">/</span>
        <span style="color:#111;">{{ $task->title }}</span>
    </nav>

    {{-- CABECERA --}}
    <div class="d-flex align-items-start justify-content-between gap-3 mb-4 flex-wrap">
        <div style="flex:1; min-width:0;">
            @if ($isEditing)
                <input type="text" wire:model.defer="title" class="form-control form-control-lg fw-semibold"
                    style="font-size:1.4rem;">
                @error('title')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            @else
                <h1 style="font-size:1.6rem; font-weight:700; margin:0; line-height:1.3;">
                    {{ $task->title }}
                </h1>
            @endif
        </div>

        <div class="d-flex gap-2 flex-shrink-0 flex-wrap">
            @if (!$task->trashed())
                @if ($isEditing)
                    <button wire:click="save" class="btn btn-primary btn-sm">💾 Guardar</button>
                    <button wire:click="cancelEdit" class="btn btn-secondary btn-sm">Cancelar</button>
                @else
                    <button wire:click="startEdit" class="btn btn-outline-primary btn-sm">✏️ Editar</button>
                @endif
                <button wire:click="delete" wire:confirm="¿Eliminar esta tarea? Podrá recuperarse más tarde."
                    class="btn btn-outline-danger btn-sm">🗑 Eliminar</button>
            @else
                <span class="badge bg-danger" style="font-size:.85rem; padding:.4rem .8rem;">Eliminada</span>
            @endif
        </div>
    </div>

    <div class="row g-4">

        {{-- COLUMNA IZQUIERDA: contenido principal --}}
        <div class="col-12 col-md-8">

            {{-- DESCRIPCIÓN --}}
            <div class="card mb-3" style="border-radius:10px;">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Descripción</h6>
                    @if ($isEditing)
                        <textarea wire:model.defer="description" class="form-control auto-resize-textarea" rows="5" x-data
                            x-init="$el.style.height = $el.scrollHeight + 'px'" x-on:input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"
                            placeholder="Describe la tarea..."></textarea>
                    @else
                        @if ($task->description)
                            <p style="margin:0; white-space:pre-line;">{!! nl2br(e($task->description)) !!}</p>
                        @else
                            <p class="text-muted fst-italic" style="margin:0;">Sin descripción.</p>
                        @endif
                    @endif
                </div>
            </div>

            {{-- ETIQUETAS --}}
            <div class="card mb-3" style="border-radius:10px;">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Etiquetas</h6>
                    @if ($isEditing)
                        <select multiple class="form-control" wire:model.defer="selected_labels"
                            style="min-height:110px;">
                            @forelse ($labels as $label)
                                <option value="{{ $label->id }}">{{ $label->name }}</option>
                            @empty
                                <option disabled>No hay etiquetas en este tenant.</option>
                            @endforelse
                        </select>
                        <div class="text-muted mt-1" style="font-size:.8rem;">
                            * Ctrl/Cmd para seleccionar varias.
                        </div>
                    @else
                        @if ($task->labels && $task->labels->isNotEmpty())
                            <div class="d-flex flex-wrap gap-1">
                                @foreach ($task->labels as $lbl)
                                    <span class="badge"
                                        style="background:#6c757d;color:#fff;font-size:.8rem;padding:3px 8px;border-radius:5px;">
                                        {{ $lbl->name }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted fst-italic" style="margin:0;">Sin etiquetas.</p>
                        @endif
                    @endif
                </div>
            </div>

            {{-- TIEMPO --}}
            <div class="card" style="border-radius:10px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-subtitle text-muted mb-0">Tiempo registrado</h6>
                        <div class="d-flex gap-2">
                            <button wire:click="toggleTimeTracking"
                                class="btn btn-sm {{ $runningEntry ? 'btn-danger' : 'btn-success' }}">
                                {{ $runningEntry ? '⏹ Detener' : '▶ Iniciar' }}
                            </button>
                            <button wire:click="openManualTimeModal" class="btn btn-sm btn-outline-secondary">
                                ➕ Manual
                            </button>
                        </div>
                    </div>

                    @php
                        $totalH = floor($totalSeconds / 3600);
                        $totalM = floor(($totalSeconds % 3600) / 60);
                        $totalS = $totalSeconds % 60;
                    @endphp
                    <div class="mb-3 p-2 rounded" style="background:#f8f9fa;">
                        <strong>Total:</strong>
                        <span style="font-size:1.1rem; font-family:monospace;">
                            {{ str_pad($totalH, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($totalM, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($totalS, 2, '0', STR_PAD_LEFT) }}
                        </span>
                        @if ($runningEntry)
                            <span class="badge bg-danger ms-2" style="animation:pulse 1.5s infinite;">
                                ● En curso
                            </span>
                        @endif
                    </div>

                    @if ($task->timeEntries->isNotEmpty())
                        <table class="table table-sm table-hover" style="font-size:.85rem;">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Inicio</th>
                                    <th>Duración</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($task->timeEntries->sortByDesc('started_at') as $entry)
                                    @php
                                        $dur = $entry->duration_seconds ?? 0;
                                        $dH = floor($dur / 3600);
                                        $dM = floor(($dur % 3600) / 60);
                                        $dS = $dur % 60;
                                    @endphp
                                    <tr>
                                        <td>
                                            <img src="{{ $entry->user?->profile_photo_url }}"
                                                alt="{{ $entry->user?->name }}"
                                                style="width:22px;height:22px;border-radius:50%;object-fit:cover;margin-right:5px;">
                                            {{ $entry->user?->name ?? 'Desconocido' }}
                                        </td>
                                        <td>{{ $entry->started_at->format('d/m/Y H:i') }}</td>
                                        <td style="font-family:monospace;">
                                            @if ($entry->is_running)
                                                <span class="text-danger">En curso...</span>
                                            @else
                                                {{ str_pad($dH, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($dM, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($dS, 2, '0', STR_PAD_LEFT) }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted fst-italic" style="margin:0;">Aún no hay entradas de tiempo.</p>
                    @endif
                </div>
            </div>

        </div>

        {{-- COLUMNA DERECHA: metadatos --}}
        <div class="col-12 col-md-4">

            {{-- ESTADO --}}
            <div class="card mb-3" style="border-radius:10px;">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Estado</h6>
                    <select wire:model{{ $isEditing ? '.defer' : '' }}="status"
                        class="task-status-select {{ $status }} form-select"
                        @if (!$isEditing) disabled @endif>
                        <option value="pending">⏳ Pendiente</option>
                        <option value="in_progress">▶ En progreso</option>
                        <option value="on_hold">⏸ En pausa</option>
                        <option value="testing">🧪 En pruebas</option>
                        <option value="done">✅ Hecha</option>
                    </select>
                </div>
            </div>

            {{-- PRIORIDAD --}}
            <div class="card mb-3" style="border-radius:10px;">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Prioridad</h6>
                    <select wire:model{{ $isEditing ? '.defer' : '' }}="priority"
                        class="task-priority-select form-select" @if (!$isEditing) disabled @endif>
                        @for ($i = 0; $i <= 10; $i++)
                            @php $cls = \App\Models\Task::PRIORITY_CLASSES[$i] ?? 'unknown'; @endphp
                            <option value="{{ $i }}" class="priority-{{ $cls }}">
                                {{ \App\Models\Task::PRIORITY_LABELS[$i] ?? 'Desconocida' }} ({{ $i }})
                            </option>
                        @endfor
                    </select>
                </div>
            </div>

            {{-- FECHA LÍMITE --}}
            <div class="card mb-3" style="border-radius:10px;">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Fecha límite</h6>
                    @if ($isEditing)
                        <input type="date" wire:model.defer="due_date" class="form-control">
                        @error('due_date')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
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
                            <span class="text-muted fst-italic">Sin fecha</span>
                        @endif
                    @endif
                </div>
            </div>

            {{-- CREADO POR --}}
            <div class="card mb-3" style="border-radius:10px;">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Creado por</h6>
                    @if ($task->creator)
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $task->creator->profile_photo_url }}" alt="{{ $task->creator->name }}"
                                style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:2px solid #e5e7eb;">
                            <div>
                                <div style="font-weight:600;font-size:.9rem;">{{ $task->creator->name }}</div>
                                <div style="font-size:.78rem;color:#9ca3af;">{{ $task->creator->email }}</div>
                            </div>
                        </div>
                    @else
                        <span class="text-muted fst-italic">Desconocido</span>
                    @endif
                </div>
            </div>

            {{-- FECHAS --}}
            <div class="card" style="border-radius:10px;">
                <div class="card-body" style="font-size:.85rem; color:#6b7280;">
                    <div class="mb-1">
                        <strong>Creada:</strong>
                        {{ $task->created_at->format('d/m/Y H:i') }}
                    </div>
                    <div>
                        <strong>Actualizada:</strong>
                        {{ $task->updated_at->format('d/m/Y H:i') }}
                    </div>
                    @if ($task->trashed())
                        <div class="mt-1 text-danger">
                            <strong>Eliminada:</strong>
                            {{ $task->deleted_at->format('d/m/Y H:i') }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    {{-- MODAL TIEMPO MANUAL --}}
    @if ($showManualTimeModal)
        <div class="modal fade show d-block" style="background:rgba(0,0,0,.5);" tabindex="-1">
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
                        <button class="btn btn-secondary" wire:click="closeManualTimeModal">Cancelar</button>
                        <button class="btn btn-primary" wire:click="saveManualTime">Guardar tiempo</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .4;
            }
        }

        .task-status-select.on_hold {
            background-color: #f59e0b !important;
            color: #fff !important;
        }

        .task-status-select.testing {
            background-color: #8b5cf6 !important;
            color: #fff !important;
        }

        .task-status-select:disabled {
            opacity: .85;
            cursor: default;
        }

        .task-priority-select:disabled {
            opacity: .85;
            cursor: default;
        }
    </style>
</div>
