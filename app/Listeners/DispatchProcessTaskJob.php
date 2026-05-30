<?php

namespace App\Listeners;

use App\Events\TaskCompleted;
use App\Jobs\ProcessTaskJob;

class DispatchProcessTaskJob
{
    public function handle(TaskCompleted $event): void
    {
        ProcessTaskJob::dispatch($event->task);
    }
}
