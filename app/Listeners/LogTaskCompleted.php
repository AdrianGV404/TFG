<?php

namespace App\Listeners;

use App\Events\TaskCompleted;
use Illuminate\Support\Facades\Log;

// Listener encargado de registrar en el log cuando una tarea ha sido marcada como completada.
class LogTaskCompleted
{
    public function handle(TaskCompleted $event): void
    {
        Log::info('Task completed', [
            'task_id' => $event->task->id,
            'title' => $event->task->title,
            'status' => $event->task->status,
        ]);
    }
}
