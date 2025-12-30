<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;

class ProjectForm extends Component
{
    public string $name = '';
    public ?string $description = null;
    public string $status = 'active';

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'status' => 'required|in:active,archived',
    ];

    public function save()
    {
        $this->validate();

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
        return view('livewire.project-form');
    }
}
