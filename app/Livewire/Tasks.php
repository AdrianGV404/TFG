<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use App\Models\Project;
use App\Livewire\Traits\WithSearchAndPagination;
use App\Livewire\Traits\Confirmable;
use App\Livewire\Traits\HasInlineEditing;
use App\Livewire\Traits\ScopedByProject;
use App\Livewire\Traits\Notifies;
use App\Livewire\Traits\FormValidationRules;

class Tasks extends Component
{
    use WithSearchAndPagination, Confirmable, HasInlineEditing, Notifies, ScopedByProject, FormValidationRules;

    public Project $project;

    /* =========================
       EDICIÓN INLINE
    ========================= */
    public ?int $editingTaskId = null;
    public string $editingTitle = '';
    public string $editingDescription = '';
    public array $editingStatus = [];
    public array $editingPriority = [];
    public array $originalStatus = [];
    public array $originalPriority = [];
    public int $taskFormKey = 0;

    protected $listeners = [
        'taskCreated' => 'onTaskCreated',
        'delete-task' => 'deleteFromModal'
    ];

    public function mount()
    {
        $this->orderBy = 'priority';

        // Inicializa arrays con los valores reales de cada tarea
        foreach ($this->project->tasks as $task) {
            $this->editingStatus[$task->id] = $task->status;
            $this->editingPriority[$task->id] = $task->priority;
            $this->originalStatus[$task->id] = $task->status;
            $this->originalPriority[$task->id] = $task->priority;
        }
    }

    public function onTaskCreated()
    {
        $this->taskFormKey++;
        $this->resetPage();

        // Inicializar los valores del nuevo task en los arrays
        foreach ($this->project->tasks as $task) {
            if (!isset($this->editingStatus[$task->id])) {
                $this->editingStatus[$task->id] = $task->status;
                $this->editingPriority[$task->id] = $task->priority;
                $this->originalStatus[$task->id] = $task->status;
                $this->originalPriority[$task->id] = $task->priority;
            }
        }
    }

    public function startEdit(int $taskId)
    {
        $task = $this->findScoped(Task::class, $taskId);

        $this->startEditingModel($task, 'editingTaskId', [
            'editingTitle' => 'title',
            'editingDescription' => 'description',
        ]);

        // Guardar los valores actuales como originales para restaurar si se cancela
        $this->originalStatus[$taskId] = $task->status;
        $this->originalPriority[$taskId] = $task->priority;
    }

    public function cancelEdit()
    {
        if ($this->editingTaskId) {
            // Restaurar valores originales solo al cancelar
            $this->editingStatus[$this->editingTaskId] = $this->originalStatus[$this->editingTaskId];
            $this->editingPriority[$this->editingTaskId] = $this->originalPriority[$this->editingTaskId];
        }

        // Limpiar campos de edición
        $this->editingTaskId = null;
        $this->editingTitle = '';
        $this->editingDescription = '';
    }

    public function saveEdit()
    {
        $this->validate($this->taskRulesForEditing());

        $this->updateScoped(Task::class, $this->editingTaskId, [
            'title' => $this->editingTitle,
            'description' => $this->editingDescription,
            'status' => $this->editingStatus[$this->editingTaskId],
            'priority' => $this->editingPriority[$this->editingTaskId],
        ]);

        // Actualizar los valores originales para futuras ediciones
        $this->originalStatus[$this->editingTaskId] = $this->editingStatus[$this->editingTaskId];
        $this->originalPriority[$this->editingTaskId] = $this->editingPriority[$this->editingTaskId];

        $this->notify("Tarea \"{$this->editingTitle}\" actualizada con éxito", 'success');

        // Limpiar campos de edición, sin restaurar selects
        $this->editingTaskId = null;
        $this->editingTitle = '';
        $this->editingDescription = '';
    }

    public function delete(int $taskId)
    {
        $this->deleteScoped(Task::class, $taskId);

        $this->notify('Tarea eliminada con éxito', 'danger');

        $this->resetPage();
    }

    public function render()
    {
        $query = $this->scopedQuery(Task::class);

        return view('livewire.tasks', [
            'tasks' => $this->applyFilters(
                $query,
                'title',
                "FIELD(status, 'pending', 'in_progress', 'done')",
                "FIELD(priority, 'very_high', 'high', 'mid', 'low', 'very_low')"
            ),
        ]);
    }

    public function confirmDelete(int $taskId)
    {
        $task = $this->findScoped(Task::class, $taskId);

        $this->dispatchConfirmDelete(
            'Eliminar tarea',
            "¿Seguro que quieres eliminar la tarea \"{$task->title}\"? Esta acción no se puede deshacer.",
            'delete-task',
            $taskId
        );
    }

    public function deleteFromModal(int $id)
    {
        $this->delete($id);
    }
}
