<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;
use App\Models\Task;
use App\Models\Label;
use App\Livewire\Traits\Notifies;
use App\Livewire\Traits\ScopedByProject;
use App\Livewire\Traits\FormValidationRules;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskForm extends Component
{
    use Notifies, ScopedByProject, FormValidationRules, AuthorizesRequests;
    
    public Project $project;
    public string $title = '';
    public ?string $description = null;
    public string $status = 'pending';
    public int $priority = 5;
    public ?string $due_date = null;
    public array $selected_labels = [];

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'status' => 'required|in:pending,in_progress,done',
        'priority' => 'required|integer|min:0|max:10',
        'due_date' => 'nullable|date',
        'selected_labels' => 'nullable|array',
        'selected_labels.*' => 'distinct|exists:labels,id',
    ];

    public function mount()
    {
        $this->priority = $this->priority ?? 5;
        $this->status = $this->status ?? 'pending';
    }

    public function save()
    {
        try {
            $this->authorize('create', [Task::class, $this->project]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notify('No tienes permisos para crear tareas.', 'danger');
            return;
        }

        $this->validate($this->rules);

        $task = $this->project->tasks()->create([
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'due_date' => $this->due_date,
            'tenant_id' => auth()->user()->tenant_id,
            'created_by'  => auth()->id(), 
        ]);

        if (!empty($this->selected_labels)) {
            $task->labels()->sync($this->selected_labels);
        }

        $this->notify("Tarea \"{$this->title}\" creada con éxito", 'success');

        $this->reset(['title', 'description', 'status', 'priority', 'due_date', 'selected_labels']); 
        $this->status = 'pending';
        $this->priority = 5;

        $this->dispatch('taskCreated');
        $this->dispatch('closeForm');
    }

    public function render()
    {
        $labels = Label::where('tenant_id', auth()->user()->tenant_id)->get();
        return view('livewire.tasks.task-form', compact('labels'));
    }
}