<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;
use App\Livewire\Traits\FormValidationRules;

class ProjectForm extends Component
{
    use FormValidationRules;
    public string $name = '';
    public ?string $description = null;
    public string $status = 'active';


    public function save()
    {
        $this->validate($this->projectRules());

        Project::create([
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
        ]);

        $this->reset();

        $this->dispatch('projectCreated');
        $this->dispatch('closeForm');
    }

    public function render()
    {
        return view('livewire.projects.project-form');
    }
}
