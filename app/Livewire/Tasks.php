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

    public bool $showForm = false;
    public Project $project;
    public bool $showDeleted = false;

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
        'delete-task' => 'deleteFromModal',
        'closeForm' => 'closeForm',
        'restore-task' => 'restoreTask',
    ];

    public function mount()
    {
        $this->orderBy = 'priority';

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

        $this->originalStatus[$taskId] = $task->status;
        $this->originalPriority[$taskId] = $task->priority;
    }

    public function cancelEdit()
    {
        if ($this->editingTaskId) {
            $this->editingStatus[$this->editingTaskId] = $this->originalStatus[$this->editingTaskId];
            $this->editingPriority[$this->editingTaskId] = $this->originalPriority[$this->editingTaskId];
        }

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

        $this->originalStatus[$this->editingTaskId] = $this->editingStatus[$this->editingTaskId];
        $this->originalPriority[$this->editingTaskId] = $this->editingPriority[$this->editingTaskId];

        $this->notify("Tarea \"{$this->editingTitle}\" actualizada con éxito", 'success');

        $this->editingTaskId = null;
        $this->editingTitle = '';
        $this->editingDescription = '';
    }

    public function delete(int $taskId)
    {
        $task = Task::withTrashed()->findOrFail($taskId);
        $name = $task->title;

        if ($task->trashed()) {
            $task->forceDelete();
            $this->notify("Tarea \"$name\" eliminada definitivamente", 'danger');
        } else {
            $task->delete();
            $this->notify("Tarea \"$name\" eliminada con éxito", 'danger');
        }

        $this->resetPage();
    }

    public function restoreTask(int $taskId)
    {
        $task = Task::withTrashed()->findOrFail($taskId);
        $task->restore();
        $this->notify("Tarea \"{$task->title}\" restaurada con éxito", 'success');
    }

    public function confirmDelete(int $taskId)
    {
        $task = Task::withTrashed()->findOrFail($taskId);

        $this->dispatchConfirmDelete(
            'Eliminar tarea',
            $task->trashed()
                ? "Esta tarea \"{$task->title}\" ya está eliminada. Se borrará permanentemente. Esta acción <b>no se puede deshacer</b>."
                : "¿Seguro que quieres eliminar la tarea \"{$task->title}\"? Esta acción solo la podrá deshacer el administrador.",
            'delete-task',
            $taskId,
            $task->trashed()
        );
    }

    public function deleteFromModal(int $id)
    {
        $this->delete($id);
    }

    public function openForm()
    {
        $this->showForm = true;
    }

    public function closeForm()
    {
        $this->showForm = false;
    }

    public function render()
    {
        $query = $this->scopedQuery(Task::class);

        if ($this->showDeleted) {
            $query = $query->withTrashed();
        }

        return view('livewire.tasks.tasks', [
            'tasks' => $this->applyFilters(
                $query,
                'title',
                "FIELD(status, 'pending', 'in_progress', 'done')",
                'priority'
            ),
        ]);
    }
}
