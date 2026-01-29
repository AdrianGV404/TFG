<div class="new-task">
    <h3>Nueva tarea</h3>

    <form action="{{ route('projects.tasks.store', $project) }}" method="POST">
        @csrf

        <div>
            <input
                type="text"
                name="title"
                placeholder="Título"
                required
            >
        </div>

        <div>
            <textarea
                name="description"
                placeholder="Descripción"
            ></textarea>
        </div>

        <div>
            <select name="status">
                <option value="pending">Pendiente</option>
                <option value="in_progress">En progreso</option>
                <option value="done">Hecha</option>
            </select>
        </div>

        <div>
            <select
                name="priority"
                class="task-priority-select {{ old('priority', 'mid') }}"
            >
                <option value="very_high" @selected(old('priority')==='very_high')>Muy Alta</option>
                <option value="high"      @selected(old('priority')==='high')>Alta</option>
                <option value="mid"       @selected(old('priority')==='mid')>Media</option>
                <option value="low"       @selected(old('priority')==='low')>Baja</option>
                <option value="very_low"  @selected(old('priority')==='very_low')>Muy Baja</option>
            </select>
        </div>

        <button class="btn btn-primary" type="submit">
            Crear tarea
        </button>
    </form>
</div>

<div class="tasks-section">
    <h2>Tareas</h2>

    @if ($project->tasks->count())
        @foreach ($project->tasks as $task)
            <div class="task-card">
                <div class="task-header">
                    <div>
                        <br>
                        <div class="task-title">
                            Title: {{ $task->title }}
                        </div>
                        <div class="task-status">
                            Estado: {{ ucfirst($task->status) }}
                        </div>
                        <div class="task-priority">
                            Prioridad: {{ ucfirst($task->priority) }}
                        </div>
                    </div>

                    <div class="task-actions">
                        <button
                            class="btn btn-secondary"
                            type="button"
                            onclick="document.getElementById('edit-task-{{ $task->id }}').style.display='block'"
                        >
                            Editar
                        </button>

                        <form
                            action="{{ route('projects.tasks.destroy', [$project, $task]) }}"
                            method="POST"
                            class="inline-form"
                        >
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" type="submit">
                                Eliminar
                            </button>
                        </form>
                    </div>
                </div>

                {{-- FORMULARIO EDITAR --}}
                <form
                    id="edit-task-{{ $task->id }}"
                    action="{{ route('projects.tasks.update', [$project, $task]) }}"
                    method="POST"
                    class="task-form hidden"
                >
                    @csrf
                    @method('PATCH')

                    <div>
                        <input
                            type="text"
                            name="title"
                            value="{{ old('title', $task->title) }}"
                            required
                        >
                    </div>

                    <div>
                        <textarea
                            name="description"
                            placeholder="Descripción"
                        >{{ old('description', $task->description) }}</textarea>
                    </div>

                    <div>
                        <select name="status">
                            <option value="pending" @selected($task->status === 'pending')>
                                Pendiente
                            </option>
                            <option value="in_progress" @selected($task->status === 'in_progress')>
                                En progreso
                            </option>
                            <option value="done" @selected($task->status === 'done')>
                                Hecha
                            </option>
                        </select>
                    </div>

                    <div>
                        <select name="priority">
                            <option value="very_high" @selected($task->priority === 'very_high')>
                                Muy Alta
                            <option value="high" @selected($task->priority === 'high')>
                                Alta
                            </option>
                            <option value="mid" @selected($task->priority === 'mid')>
                                Media
                            </option>
                            <option value="low" @selected($task->priority === 'low')>
                                Baja
                            </option>
                            <option value="very_low" @selected($task->priority === 'very_low')>
                                Muy Baja
                            </option>
                        </select>
                    </div>

                    <button class="btn btn-primary" type="submit">
                        Guardar cambios
                    </button>
                </form>
            </div>
        @endforeach
    @else
        <p>No hay tareas para este proyecto.</p>
    @endif
</div>
