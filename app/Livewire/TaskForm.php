<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;
use App\Models\Task;
use App\Livewire\Traits\Notifies;
use App\Livewire\Traits\ScopedByProject;
use App\Livewire\Traits\FormValidationRules;

class TaskForm extends Component
{
    use Notifies, ScopedByProject, FormValidationRules;
    public Project $project;

    public string $title = '';
    public ?string $description = null;
    public string $status = 'pending';
    public string $priority = 'mid';

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'status' => 'required|in:pending,in_progress,done',
        'priority' => 'required|in:high,mid,low',
    ];

    public function save()
    {
        $this->validate($this->taskRules());

        $this->createScoped(Task::class, [
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
        ]);

        $this->notify("Tarea \"{$this->title}\" creada con éxito", 'success');

        $this->reset(['title', 'description', 'status', 'priority']);
        $this->status = 'pending';
        $this->priority = 'mid';

        $this->dispatch('taskCreated');
    }

    public function render()
    {
        return view('livewire.task-form');
    }
}
