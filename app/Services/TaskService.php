<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Jobs\ProcessTaskJob;

class TaskService
{
    /**
     * Crea una tarea asociada a un proyecto concreto.
     *
     * - Usa la relación Eloquent para asegurar la asociación correcta.
     * - Desacopla al controlador de la lógica de creación.
     * - Lanza un Job en segundo plano para procesar la tarea.
     */
    public function createForProject(Project $project, array $data): Task
    {
        // Crea la tarea vinculándola automáticamente al proyecto
        $task = $project->tasks()->create($data);

        // Despacha el Job para procesar la tarea de forma asíncrona
        ProcessTaskJob::dispatch($task);

        return $task;
    }

    /**
     * Crea una tarea sin contexto de proyecto.
     *
     * Usado principalmente por la API REST, donde el project_id
     * viene directamente en los datos validados.
     */
    public function create(array $data): Task
    {
        return Task::create($data);
    }

    /**
     * Lista todas las tareas.
     *
     * @return \Illuminate\Database\Eloquent\Collection|array
     */
    public function listAll()
    {
        return Task::all();
    }

    /**
     * Lista tareas de un proyecto concreto.
     *
     * @param Project $project
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function listForProject(Project $project)
    {
        return $project->tasks()->get();
    }

        /**
     * Actualiza una tarea existente.
     *
     * La lógica de negocio asociada a cambios de estado
     * (eventos, observers, emails, etc.) se gestiona fuera
     * de este servicio para mantenerlo simple.
     */
    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        return $task;
    }
}
