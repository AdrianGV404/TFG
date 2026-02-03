<div class="container">

    {{-- HEADER --}}
    <div class="page-header d-flex align-items-center justify-content-between">
        <h1>Proyectos</h1>

        @if (!$showForm)
            <button type="button" class="btn btn-primary btn-loading" wire:click="openForm" wire:loading.attr="disabled"
                wire:target="openForm">
                <span class="btn-text">+ Nuevo proyecto</span>
                <span class="btn-spinner" wire:loading.delay wire:target="openForm">⏳</span>
            </button>
        @endif
    </div>

    {{-- FORMULARIO --}}
    @if ($showForm)
        <div class="task-form">
            <livewire:project-form />
        </div>
        <hr>
    @endif

    {{-- CONTROLES --}}
    <div class="search-controls-wrapper d-flex align-items-center justify-content-between mb-3">
        @include('livewire.partials.search-controls', [
            'textPlaceholder' => 'Buscar por nombre...',
            'allowStatusOrder' => true,
            'isProjectList' => true,
        ])

        {{-- Checkbox para mostrar eliminados --}}
        <div class="form-check ms-3">
            <input class="form-check-input" type="checkbox" id="showDeleted" wire:model="showDeleted"
                wire:change="$refresh">
            <label class="form-check-label" for="showDeleted">
                Mostrar proyectos eliminados
            </label>
        </div>
    </div>

    {{-- TABLA --}}
    <table class="table">
        <thead>
            <tr>
                <th class="col-actions">Acciones</th>
                <th class="col-id">ID</th>
                <th class="col-project">Proyecto</th>
                <th class="col-status-140">Estado</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($projects as $project)
                @php
                    $isDeleted = $project->trashed();
                    $deletedAt = $isDeleted ? $project->deleted_at : null;
                    $daysToKeep = config('prune.days_to_keep_deleted.' . \App\Models\Project::class, 60);
                    $expiresAt = $isDeleted ? $deletedAt->copy()->addDays($daysToKeep) : null;
                    $daysLeft = $isDeleted ? now()->diffInDays($expiresAt, false) : null;
                @endphp

                <tr wire:key="project-{{ $project->id }}-{{ $editingProjectId == $project->id ? 'editing' : 'view' }}"
                    class="{{ $isDeleted ? 'bg-softdeleted' : '' }}">

                    {{-- ACCIONES --}}
                    <td>
                        @include('livewire.partials.action-buttons', [
                            'editingId' => $editingProjectId,
                            'id' => $project->id,
                        ])
                    </td>

                    {{-- ID --}}
                    <td class="text-muted">#{{ $project->id }}</td>

                    {{-- PROYECTO --}}
                    <td class="col-project">
                        @if ($editingProjectId == $project->id)
                            <input type="text" class="form-control form-control-sm mb-1"
                                wire:model.defer="editingName">
                            <textarea class="form-control form-control-sm auto-resize-textarea" rows="1" wire:model.defer="editingDescription"
                                placeholder="Descripción" x-data x-init="$el.style.height = $el.scrollHeight + 'px'"
                                x-on:input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"></textarea>
                        @else
                            <a href="{{ route('projects.show', $project) }}" class="project-card-link"
                                title="Abrir proyecto">
                                <div class="project-name">
                                    <div class="project-title">
                                        {{ $project->name }}
                                        <span class="project-open-icon">→</span>
                                    </div>

                                    @php
                                        $total = $project->total_tasks ?? 0;
                                    @endphp

                                    @if ($total > 0)
                                        @include('livewire.partials.project-progress', [
                                            'total' => $total,
                                            'done' => $project->done_tasks,
                                            'inProgress' => $project->in_progress_tasks,
                                            'pending' => $project->pending_tasks,
                                        ])
                                    @endif

                                    @if ($project->description)
                                        <div class="project-description">
                                            {!! nl2br(e($project->description)) !!}
                                        </div>
                                    @endif

                                    {{-- SOLO SOFTDELETED: Expira debajo del contenido, alineado con el proyecto --}}
                                    @if($isDeleted)
                                        <div class="expira-text text-danger mt-1" style="font-size:13px;">
                                            @if($daysLeft <= 5) ⚠️ @endif
                                            Expira en {{ $daysLeft }} días ({{ $expiresAt->format('d/m/Y') }})
                                        </div>
                                    @endif
                                </div>
                            </a>
                        @endif
                    </td>

                    {{-- ESTADO --}}
                    <td>
                        @if ($editingProjectId == $project->id)
                            <select class="form-select form-select-sm" wire:model.defer="editingStatus">
                                <option value="active">Activo</option>
                                <option value="archived">Archivado</option>
                            </select>
                        @else
                            <span class="badge {{ $project->status }}">
                                {{ ucfirst($project->status) }}
                            </span>
                        @endif
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-muted">
                        No hay proyectos aún.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $projects->links() }}
    </div>

</div>
