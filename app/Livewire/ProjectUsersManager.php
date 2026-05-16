<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\Project;
use App\Models\User;

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
        return User::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->where('name', 'like', "%{$this->search}%")
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.project-users-manager');
    }
}