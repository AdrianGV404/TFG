<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Ejecutado antes que cualquier otro método.
     * Los admins tienen acceso total sin pasar por ninguna comprobación.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null; // null = continuar con el método correspondiente
    }

    public function create(User $user): bool
    {
        return $user->hasCustomPermission('create_project');
    }

    public function update(User $user, Project $project): bool
    {
        // Puede editar si es el creador del proyecto o tiene permiso global
        return $project->created_by === $user->id
            || $user->hasCustomPermission('edit_task', $project);
    }

    public function delete(User $user, Project $project): bool
    {
        // Puede borrar si es el creador del proyecto o tiene permiso global
        return $project->created_by === $user->id
            || $user->hasCustomPermission('delete_task', $project);
    }
}