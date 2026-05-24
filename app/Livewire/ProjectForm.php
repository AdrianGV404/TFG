<?php

namespace App\Livewire;

use App\Livewire\Traits\FormValidationRules;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

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
            'tenant_id' => Auth::user()->tenant_id,
            'created_by' => Auth::id(),
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
