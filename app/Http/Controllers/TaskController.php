<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Services\TaskService;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;

class TaskController extends Controller
{
    public function __construct(
        private TaskService $taskService
    ) {}
   /**
     * Crea una nueva tarea asociada a un proyecto concreto.
     *
     * - La validación se realiza mediante StoreTaskRequest.
     * - La creación real de la tarea se delega al TaskService.
     */
    public function store(StoreTaskRequest $request, Project $project)
    {
        $this->authorize('create', [Task::class, $project]);

        $this->taskService->createForProject($project, $request->validated());

        return redirect()->back();
    }
    /**
     * Actualiza una tarea existente dentro de un proyecto.
     *
     * - Usa UpdateTaskRequest para validar los datos.
     * - La actualización y posibles efectos secundarios
     *   (eventos, observers, etc.) se gestionan en el servicio.
     */
    public function update(UpdateTaskRequest $request, Project $project, Task $task)
    {
        $this->authorize('update', $task);

        $this->taskService->update($task, $request->validated());

        return redirect()->route('projects.show', $project);
    }

    public function destroy(Project $project, Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return back();
    }
}
