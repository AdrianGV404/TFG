<?php

namespace App\Events;

use App\Models\Task;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

//Evento que se dispara cuando una tarea es completada.

class TaskCompleted
{
    use Dispatchable, SerializesModels;

    public Task $task; //Publica oara los listeners

    public function __construct(Task $task)
    {
        $this->task = $task;
    }
}
