<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver proyecto</title>

    {{-- CSS global --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

<div class="container">

    <h1>{{ $project->name }}</h1>

    <div class="project-meta">
        <p><strong>ID:</strong> {{ $project->id }}</p>

        <p>{{ $project->description }}</p>

        <p>
            Estado:
            <span class="badge {{ $project->status }}">
                {{ ucfirst($project->status) }}
            </span>
        </p>
    </div>

    <div class="actions">
        <a href="{{ route('projects.edit', $project) }}" class="btn btn-primary">
            Editar proyecto
        </a>

        <a href="{{ route('projects.index') }}" class="btn btn-secondary">
            Volver al listado
        </a>
    </div>

    <hr>

    <div class="tasks-section">
        <h2>Tareas del proyecto</h2>

        @include('projects._tasks', ['project' => $project])
    </div>

</div>

</body>
</html>
