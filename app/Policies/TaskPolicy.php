<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    public function create(User $user, Project $project): bool
    {
        return $user->hasCustomPermission('create_task', $project);
    }

    public function update(User $user, Task $task): bool
    {
        return $user->hasCustomPermission('edit_task', $task);
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->hasCustomPermission('delete_task', $task);
    }
    
    public function reassign(User $user): bool
    {
        return $user->hasCustomPermission('reassign_users');
    }
}