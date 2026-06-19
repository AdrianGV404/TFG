<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Ejecutado antes que cualquier otro método.
     * Si el proyecto pertenece a otro tenant, se deniega SIEMPRE,
     * incluso para administradores.
     */
    public function before(User $user, string $ability, ...$arguments): ?bool
    {
        $project = $arguments[0] ?? null;

        if ($project instanceof Project && $project->tenant_id !== $user->tenant_id) {
            return false; // Deniega de forma absoluta, ni siquiera un admin puede pasar
        }

        if ($user->isAdmin()) {
            return true;
        }

        return null; // continuar con el método correspondiente
    }

    public function view(User $user, Project $project): bool
    {
        // Si llegamos aquí, before() ya garantizó que es del mismo tenant
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasCustomPermission('create_project');
    }

    public function update(User $user, Project $project): bool
    {
        return $project->created_by === $user->id
            || $user->hasCustomPermission('edit_task', $project);
    }

    public function delete(User $user, Project $project): bool
    {
        return $project->created_by === $user->id
            || $user->hasCustomPermission('delete_task', $project);
    }
}