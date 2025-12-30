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
        Mail::to('test@laravel.com')
            ->send(new TaskCompletedMail($event->task));
    }
}
