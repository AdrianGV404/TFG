<?php

namespace App\Mail;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Task $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function build()
    {
        return $this
            ->subject('Tarea completada')
            ->view('emails.task_completed')
            ->with([
                'task' => $this->task,
            ]);
    }
}
