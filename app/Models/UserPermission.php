<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPermission extends Model
{
    protected $fillable = [
        'user_id',
        'can_create_projects',
        'can_create_project_tasks_by_others',
        'can_create_any_task',
        'can_edit_project_tasks_by_others',
        'can_edit_any_task',
        'can_delete_project_tasks_by_others',
        'can_delete_any_task',
        'can_reassign_users',
    ];

    protected $casts = [
        'can_create_projects' => 'boolean',
        'can_create_project_tasks_by_others' => 'boolean',
        'can_create_any_task' => 'boolean',
        'can_edit_project_tasks_by_others' => 'boolean',
        'can_edit_any_task' => 'boolean',
        'can_delete_project_tasks_by_others' => 'boolean',
        'can_delete_any_task' => 'boolean',
        'can_reassign_users' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}