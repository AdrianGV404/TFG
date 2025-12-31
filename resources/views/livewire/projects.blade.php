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

    {{-- TABLA --}}
    <table class="table">
        <thead>
            <tr>
                <th style="width:180px;">Acciones</th>
                <th style="width:80px;">ID</th>
                <th>Proyecto</th>
                <th style="width:140px;">Estado</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($projects as $project)
                <tr wire:key="project-{{ $project->id }}-{{ $editingProjectId == $project->id ? 'editing' : 'view' }}">

                    {{-- ACCIONES --}}
                    <td>
                        <div style="display:flex; gap:6px;">
                            @if ($editingProjectId == $project->id)

                                <button
                                    type="button"
                                    class="btn btn-success btn-sm btn-loading"
                                    wire:click="saveEdit"
                                    wire:loading.attr="disabled"
                                    wire:target="saveEdit"
                                >
                                    <span class="btn-text">Guardar</span>
                                    <span class="btn-spinner" wire:loading.delay wire:target="saveEdit">⏳</span>
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-secondary btn-sm"
                                    wire:click="cancelEdit"
                                >
                                    Cancelar
                                </button>

                            @else

                                <button
                                    type="button"
                                    class="btn btn-secondary btn-sm"
                                    wire:click="startEdit({{ $project->id }})"
                                >
                                    Editar
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-danger btn-sm btn-loading"
                                    wire:click="delete({{ $project->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="delete({{ $project->id }})"
                                >
                                    <span class="btn-text">Eliminar</span>
                                    <span class="btn-spinner" wire:loading.delay wire:target="delete({{ $project->id }})">⏳</span>
                                </button>

                            @endif
                        </div>
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
                                class="form-control form-control-sm"
                                rows="2"
                                wire:model.defer="editingDescription"
                                placeholder="Descripción"
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

                            @if ($project->description)
                                <div class="project-description">
                                    {{ $project->description }}
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

</div>
