<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;
use App\Models\User;
use App\Services\NotificationService;

class ProjectUsersManager extends Component
{
    public Project $project;
    public string $search = '';

    public function addUser(int $userId)
    {
        if (!auth()->user()->isAdmin() && $this->project->created_by !== auth()->id()) {
            abort(403);
        }

        $this->project->users()->syncWithoutDetaching([$userId]);

        // Send notification to the added user
        $user = User::find($userId);
        if ($user) {
            NotificationService::notifyProjectAssignment($user, $this->project->name);
        }
    }

    public function removeUser(int $userId)
    {
        if (!auth()->user()->isAdmin() && $this->project->created_by !== auth()->id()) {
            abort(403);
        }

        $this->project->users()->detach($userId);
    }

    public function getUsersProperty()
    {
        return $this->project->users;
    }

    public function getAvailableUsersProperty()
    {
        // IDs de usuarios ya asignados a este proyecto
        $assignedIds = $this->project->users()->pluck('users.id')->toArray();

        $query = User::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->whereNotIn('id', $assignedIds); // excluir los ya asignados

        // Si hay texto de búsqueda, filtrar por nombre
        if (trim($this->search) !== '') {
            $query->where('name', 'like', '%' . trim($this->search) . '%');
        }

        return $query->orderBy('name')->limit(20)->get();
    }

    public function render()
    {
        return view('livewire.project-users-manager');
    }
}