<div class="container">

    {{-- HEADER --}}
    <div class="page-header">
        <h1>Proyectos</h1>

        <button
            type="button"
            class="btn btn-primary btn-loading"
            wire:click="openForm"
            wire:loading.attr="disabled"
            wire:target="openForm"
        >
            <span class="btn-text">+ Nuevo proyecto</span>
            <span class="btn-spinner" wire:loading.delay wire:target="openForm">⏳</span>
        </button>
    </div>

    {{-- FORMULARIO --}}
    @if ($showForm)
        <div class="task-form">
            <livewire:project-form />
        </div>
        <hr>
    @endif

    {{-- CONTROLES --}}
    @include('livewire.partials.search-controls', [
        'textPlaceholder' => 'Buscar por nombre...',
        'allowStatusOrder' => true
    ])

    {{-- TABLA --}}
    <table class="table">
        <thead>
            <tr>
                <th class="col-actions">Acciones</th>
                <th class="col-id">ID</th>
                <th>Proyecto</th>
                <th class="col-status-140">Estado</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($projects as $project)
                <tr wire:key="project-{{ $project->id }}-{{ $editingProjectId == $project->id ? 'editing' : 'view' }}">

                    {{-- ACCIONES --}}
                    <td>
                        @include('livewire.partials.action-buttons', ['editingId' => $editingProjectId, 'id' => $project->id])
                    </td>

                    {{-- ID --}}
                    <td class="text-muted">#{{ $project->id }}</td>

                    {{-- PROYECTO --}}
                    <td>
                        @if ($editingProjectId == $project->id)

                            <input
                                type="text"
                                class="form-control form-control-sm mb-1"
                                wire:model.defer="editingName"
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

                            <div class="project-name">
                                <a
                                    href="{{ route('livewire.projects.show', $project) }}"
                                    class="project-title"
                                >
                                    {{ $project->name }}
                                </a>
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

                        @endif
                    </td>

                    {{-- ESTADO --}}
                    <td>
                        @if ($editingProjectId == $project->id)

                            <select
                                class="form-select form-select-sm"
                                wire:model.defer="editingStatus"
                            >
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

