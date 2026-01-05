<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Proyectos</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

<div class="container">

    <div class="page-header">
        <h1>Listado de proyectos</h1>

        <a href="{{ route('projects.create') }}" class="btn btn-success">
            Crear proyecto
        </a>
    </div>

    <table class="table table-projects">
        <thead>
            <tr>
                <th class="col-actions-200">Acciones</th>
                <th class="col-id-80">ID</th>
                <th class="col-project-320">Proyecto</th>
                <th class="col-state-120">Estado</th>
                <th class="col-tasks-280">Tareas</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($projects as $project)
                <tr>
                    {{-- ACCIONES --}}
                    <td>
                        <div class="actions actions-row">
                            <a
                                href="{{ route('projects.show', $project) }}"
                                class="btn btn-primary btn-sm"
                            >
                                Ver
                            </a>

                            <a
                                href="{{ route('projects.edit', $project) }}"
                                class="btn btn-warning btn-sm"
                            >
                                Editar
                            </a>

                            <form
                                action="{{ route('projects.destroy', $project) }}"
                                method="POST"
                                class="inline-form"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    class="btn btn-danger btn-sm"
                                    type="submit"
                                    onclick="return confirm('¿Eliminar proyecto?')"
                                >
                                    Eliminar
                                </button>
                            </form>
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
                                {{ $project->name }}
                            </span>
                        </div>

                        @if($project->description)
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

                    {{-- TAREAS --}}
                    <td>
                        @if ($project->tasks->count())
                            <ul class="tasks">
                                @foreach ($project->tasks as $task)
                                    <li>
                                        <span class="task-title">
                                            Title: {{ $task->title }}
                                        </span>
                                        <span class="task-status {{ $task->status }}">
                                            {{ ucfirst($task->status) }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-muted">
                                Sin tareas
                            </span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

</body>
</html>
