<div class="container">

    {{-- HEADER --}}
    <div class="page-header">
        <h1>Proyectos</h1>

        <button
            class="btn btn-primary btn-loading"
            wire:click="openForm"
            wire:loading.attr="disabled"
            wire:target="openForm"
        >
            <span class="btn-text">
                + Nuevo proyecto
            </span>

            <span
                class="btn-spinner"
                wire:loading.delay
                wire:target="openForm"
            >
                ⏳
            </span>
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
    <table>
        <thead>
            <tr>
                <th style="width: 160px;">Acciones</th>
                <th style="width: 80px;">ID</th>
                <th>Proyecto</th>
                <th style="width: 140px;">Estado</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($projects as $project)
                <tr wire:key="project-{{ $project->id }}">

                    {{-- ACCIONES --}}
                    <td>
                        <div class="actions actions-row">
                            <button
                                class="btn btn-danger btn-sm btn-loading"
                                wire:click="delete({{ $project->id }})"
                                wire:loading.attr="disabled"
                                wire:target="delete({{ $project->id }})"
                            >
                                <span class="btn-text">
                                    Eliminar
                                </span>

                                <span
                                    class="btn-spinner"
                                    wire:loading.delay
                                    wire:target="delete({{ $project->id }})"
                                >
                                    ⏳
                                </span>
                            </button>
                        </div>
                    </td>

                    {{-- ID --}}
                    <td class="text-muted">
                        #{{ $project->id }}
                    </td>

                    {{-- PROYECTO --}}
                    <td>
                        <div class="project-name">
                            <span class="project-title">
                                <a
                                    href="{{ route('livewire.projects.show', $project) }}"
                                    class="project-title"
                                >
                                    {{ $project->name }}
                                </a>
                            </span>
                        </div>

                        @if ($project->description)
                            <div class="project-description">
                                {{ $project->description }}
                            </div>
                        @endif
                    </td>

                    {{-- ESTADO --}}
                    <td>
                        <span class="badge {{ $project->status }}">
                            {{ ucfirst($project->status) }}
                        </span>
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
