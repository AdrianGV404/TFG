<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;

class Projects extends Component
{
    public bool $showForm = false;

    protected $listeners = [
        'projectCreated' => 'refreshProjects',
        'projectDeleted' => 'refreshProjects',
        'closeForm' => 'closeForm',
    ];

    public function openForm()
    {
        $this->showForm = true;
    }

    public function closeForm()
    {
        $this->showForm = false;
    }

    public function delete(int $projectId)
    {
        Project::findOrFail($projectId)->delete();
        $this->dispatch('projectDeleted');
    }

    public function refreshProjects()
    {
        // solo fuerza el re-render (livewire re-renderiza cuando se ejecuta un metodo del componente)
    }

    public function render()
    {
        return view('livewire.projects', [
            'projects' => Project::latest()->get(),
        ]);
    }
}
