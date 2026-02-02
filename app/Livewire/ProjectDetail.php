<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;

class ProjectDetail extends Component
{
    public Project $project;

    public function mount(Project $project)
    {
        $this->project = $project;
    }

    public function render()
    {
        return view('livewire.projects.project-detail', [
            'project' => $this->project
        ]);    
    }
}
