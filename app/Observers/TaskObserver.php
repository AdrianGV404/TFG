<?php

namespace App\Observers;

use App\Models\Task;
use App\Events\TaskCompleted;

class TaskObserver
{   
    /**
     * Se ejecuta automáticamente después de que una tarea
     * haya sido actualizada en la base de datos.
     */
    public function updated(Task $task): void
    {
        // Comprueba si el campo "status" ha cambiado en esta actualización
        // y si el nuevo valor del estado es "done"
        if (
            $task->wasChanged('status') &&
            $task->status === 'done'
        ) {
            // Dispara el evento TaskCompleted para desacoplar
            // las acciones que deben ocurrir cuando una tarea se completa
            event(new TaskCompleted($task));
        }
    }
}
