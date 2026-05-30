<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Tarea completada</title>
</head>

<body>
    <h1>Tarea completada</h1>

    <p>
        La tarea <strong>{{ $task->title }}</strong> ha sido marcada como completada.
    </p>

    @if ($task->description)
        <p>{{ $task->description }}</p>
    @endif

    <p>
        Estado: {{ $task->status }}
    </p>
</body>

</html>
