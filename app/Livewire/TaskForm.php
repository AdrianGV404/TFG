<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;
use App\Models\Task;

class TaskForm extends Component
{
    public Project $project;

    public string $title = '';
    public ?string $description = null;
    public string $status = 'pending';

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'status' => 'required|in:pending,in_progress,done',
    ];

    public function save()
    {
        $this->validate();

        Task::create([
            'project_id' => $this->project->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
        ]);

        $this->dispatch(
            'notify',
            message: "Tarea \"{$this->title}\" creada con éxito",
            type: 'success'
        );

        $this->reset(['title', 'description', 'status']);
        $this->status = 'pending';

        $this->dispatch('taskCreated');
    }

    public function render()
    {
        return view('livewire.task-form');
    }
}
