<?php

namespace App\Observers;

use App\Models\Task;
use App\Events\TaskCompleted;
use App\Services\NotificationService;

class TaskObserver
{
    public function updated(Task $task): void
    {
        if ($task->wasChanged('status')) {
            $oldStatus = $task->getOriginal('status');
            $newStatus = $task->status;

            // Fire the TaskCompleted event when done
            if ($newStatus === 'done') {
                event(new TaskCompleted($task));
            }

            // Notify other project members of the status change
            NotificationService::notifyStatusChange(
                $task,
                $oldStatus,
                $newStatus,
                auth()->id() ?? 0
            );
        }
    }
}