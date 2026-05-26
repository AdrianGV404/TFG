<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;
use App\Models\Task;
use App\Models\Label; // Cambiado: Ahora importa el modelo Label
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
    public int $priority = 5;
    public ?string $due_date = null;
    
    // Cambiado: Array para almacenar los IDs de los labels seleccionados
    public array $selected_labels = [];

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'status' => 'required|in:pending,in_progress,done',
        'priority' => 'required|integer|min:0|max:10',
        'due_date' => 'nullable|date',
        // Cambiado: Validamos que existan en la tabla 'labels'
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
        $this->validate($this->rules);

        // 1. Crear la tarea
        $task = $this->project->tasks()->create([
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'due_date' => $this->due_date,
            'tenant_id' => auth()->user()->tenant_id,
        ]);

        // 2. Cambiado: Asociar los labels en la tabla intermedia de forma segura
        if (!empty($this->selected_labels)) {
            $task->labels()->sync($this->selected_labels);
        }

        $this->notify("Tarea \"{$this->title}\" creada con éxito", 'success');

        // 3. Cambiado: Reiniciar el formulario incluyendo el array de labels
        $this->reset(['title', 'description', 'status', 'priority', 'due_date', 'selected_labels']); 
        $this->status = 'pending';
        $this->priority = 5;

        $this->dispatch('taskCreated');
        $this->dispatch('closeForm');
    }

    public function render()
    {
        // Cambiado: Obtener solo los labels pertenecientes al Tenant actual
        $labels = Label::where('tenant_id', auth()->user()->tenant_id)->get();
        
        return view('livewire.tasks.task-form', compact('labels'));
    }
}