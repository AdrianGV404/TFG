<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Services\TaskService;
use App\Http\Resources\TaskResource;
use App\Http\Requests\StoreApiTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Illuminate\Http\Response;

/**
 * Controlador REST para gestionar tareas a través de la API.
 * Recibe peticiones HTTP y utiliza el servicio TaskService para la lógica de negocio.
*/
class TaskController extends Controller
{
    public function __construct(
        private TaskService $taskService
    ) {}

    public function index()
    {
        return TaskResource::collection(Task::all());
    }

    /**
     * Crea una nueva tarea a partir de los datos validados.
     * La creación se delega al servicio para mantener el controlador limpio.
     */
    public function store(StoreApiTaskRequest $request)
    {
        $task = $this->taskService->create($request->validated());

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Task $task)
    {
        return new TaskResource($task);
    }
    
    //Actualiza una tarea existente con los datos validados.
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task = $this->taskService->update($task, $request->validated());

        return new TaskResource($task);
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
