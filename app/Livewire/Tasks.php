<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use App\Models\Project;
use App\Livewire\Traits\WithSearchAndPagination;
use App\Livewire\Traits\HasInlineEditing;
use App\Livewire\Traits\ScopedByProject;
use App\Livewire\Traits\Notifies;
use App\Livewire\Traits\FormValidationRules;
use App\Models\TaskTimeEntry;

class Tasks extends Component
{
    use WithSearchAndPagination, HasInlineEditing, Notifies, ScopedByProject, FormValidationRules;

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
    public array $editingDueDate = [];
    public array $originalDueDate = [];
    /* =========================
       MODAL TIEMPO MANUAL
    ========================= */
    public bool $showManualTimeModal = false;
    public ?int $manualTimeTaskId = null;
    public int $manualHours = 0;
    public int $manualMinutes = 0;

    protected $listeners = [
        'delete-task' => 'deleteFromModal',
        'restore-task' => 'restoreTask',
        'taskCreated' => 'onTaskCreated',
        'closeForm' => 'closeForm',
    ];

public function mount()
    {
        $this->orderBy = 'priority';

        foreach ($this->project->tasks as $task) {
            $this->editingStatus[$task->id] = $task->status;
            $this->editingPriority[$task->id] = $task->priority;
            $this->editingDueDate[$task->id] = $task->due_date ? $task->due_date->format('Y-m-d') : null; 
            
            $this->originalStatus[$task->id] = $task->status;
            $this->originalPriority[$task->id] = $task->priority;
            $this->originalDueDate[$task->id] = $this->editingDueDate[$task->id];
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
                $this->editingDueDate[$task->id] = $task->due_date ? $task->due_date->format('Y-m-d') : null;
                
                $this->originalStatus[$task->id] = $task->status;
                $this->originalPriority[$task->id] = $task->priority;
                $this->originalDueDate[$task->id] = $this->editingDueDate[$task->id];
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
        $this->originalDueDate[$taskId] = $this->editingDueDate[$taskId];
        
    }

    public function cancelEdit()
    {
        if ($this->editingTaskId) {
            $this->editingStatus[$this->editingTaskId] = $this->originalStatus[$this->editingTaskId];
            $this->editingPriority[$this->editingTaskId] = $this->originalPriority[$this->editingTaskId];
            $this->editingDueDate[$this->editingTaskId] = $this->originalDueDate[$this->editingTaskId];
        }

        $this->editingTaskId = null;
        $this->editingTitle = '';
        $this->editingDescription = '';
    }

    public function saveEdit()
    {
$this->validate($this->taskRulesForEditing()); // **Nota abajo**

        $this->updateScoped(Task::class, $this->editingTaskId, [
            'title' => $this->editingTitle,
            'description' => $this->editingDescription,
            'status' => $this->editingStatus[$this->editingTaskId],
            'priority' => $this->editingPriority[$this->editingTaskId],
            'due_date' => $this->editingDueDate[$this->editingTaskId] ?: null,
        ]);

        $this->originalStatus[$this->editingTaskId] = $this->editingStatus[$this->editingTaskId];
        $this->originalPriority[$this->editingTaskId] = $this->editingPriority[$this->editingTaskId];
        $this->originalDueDate[$this->editingTaskId] = $this->editingDueDate[$this->editingTaskId];
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

    public function restoreTask(int $id)
    {
        $task = Task::withTrashed()->findOrFail($id);
        $task->restore();
        $this->notify("Tarea \"{$task->title}\" restaurada con éxito", 'success');
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
        $query = $this->scopedQuery(Task::class)
            ->with([
                'timeEntries.user'
            ]);

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

    public function toggleTimeTracking(int $taskId)
    {
        $task = $this->findScoped(Task::class, $taskId);

        $runningEntry = TaskTimeEntry::query()
            ->where('task_id', $task->id)
            ->where('user_id', auth()->id())
            ->where('is_running', true)
            ->first();

        if ($runningEntry) {
            $runningEntry->update([
                'ended_at' => now(),
                'duration_seconds' => now()->diffInSeconds($runningEntry->started_at),
                'is_running' => false,
            ]);

            $this->notify('Tiempo detenido', 'success');
            return;
        }

        TaskTimeEntry::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'started_at' => now(),
            'is_running' => true,
        ]);

        $this->notify('Tiempo iniciado', 'success');
    }

    public function openManualTimeModal(int $taskId)
    {
        $this->manualTimeTaskId = $taskId;
        $this->manualHours = 0;
        $this->manualMinutes = 0;
        $this->showManualTimeModal = true;
    }

    public function closeManualTimeModal()
    {
        $this->reset([
            'showManualTimeModal',
            'manualTimeTaskId',
            'manualHours',
            'manualMinutes',
        ]);
    }

    public function saveManualTime()
    {
        $this->validate([
            'manualHours' => 'required|integer|min:0|max:999',
            'manualMinutes' => 'required|integer|min:0|max:59',
        ]);

        $seconds = ($this->manualHours * 3600) + ($this->manualMinutes * 60);

        if ($seconds <= 0) {
            $this->notify('Debes introducir un tiempo válido', 'danger');
            return;
        }

        TaskTimeEntry::create([
            'task_id' => $this->manualTimeTaskId,
            'user_id' => auth()->id(),
            'started_at' => now(),
            'ended_at' => now(),
            'duration_seconds' => $seconds,
            'is_running' => false,
        ]);

        $this->notify('Tiempo añadido correctamente', 'success');
        
        $this->closeManualTimeModal();
    }
}