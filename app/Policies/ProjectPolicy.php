<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Determina si el usuario puede crear proyectos.
     */
    public function create(User $user): bool
    {
        return $user->hasCustomPermission('create_project');
    }
}