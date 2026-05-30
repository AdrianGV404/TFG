<?php

namespace App\Listeners;

use App\Events\TaskCompleted;
use App\Mail\TaskCompletedMail;
use Illuminate\Support\Facades\Mail;

//Se ejecuta automáticamente cuando una tarea cambia a estado "done"
class SendTaskCompletedEmail
{
    public function handle(TaskCompleted $event): void
    {
        // Queue the mailable for asynchronous delivery (improves throughput under load)
        Mail::to('test@laravel.com')
            ->queue(new TaskCompletedMail($event->task));
    }
}
