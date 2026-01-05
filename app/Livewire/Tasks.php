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

class Tasks extends Component
{
    use WithSearchAndPagination, Confirmable, HasInlineEditing, Notifies, ScopedByProject;

    public Project $project;

    /* =========================
       EDICIÓN INLINE
    ========================= */

    public ?int $editingTaskId = null;
    public string $editingTitle = '';
    public string $editingDescription = '';
    public int $taskFormKey = 0;

    protected $listeners = [
        'taskCreated' => 'onTaskCreated',
        'delete-task' => 'deleteFromModal'
    ];

    public function onTaskCreated()
    {
        $this->taskFormKey++;
        $this->resetPage();
    }

    public function startEdit(int $taskId)
    {
        $task = $this->findScoped(Task::class, $taskId);

        $this->startEditingModel($task, 'editingTaskId', [
            'editingTitle' => 'title',
            'editingDescription' => 'description',
        ]);
    }

    public function cancelEdit()
    {
        $this->cancelEditing([
            'editingTaskId',
            'editingTitle',
            'editingDescription',
        ]);
    }

    public function saveEdit()
    {
        $this->validate([
            'editingTitle' => 'required|string|max:255',
            'editingDescription' => 'nullable|string',
        ]);

        $this->updateScoped(Task::class, $this->editingTaskId, [
            'title' => $this->editingTitle,
            'description' => $this->editingDescription,
        ]);

        $this->notify("Tarea \"{$this->editingTitle}\" actualizada con éxito", 'success');

        $this->cancelEdit();
    }

    public function delete(int $taskId)
    {
        $this->deleteScoped(Task::class, $taskId);

        $this->notify('Tarea eliminada con éxito', 'danger');

        $this->resetPage();
    }

    public function updateStatus(int $taskId, string $status)
    {
        $this->updateScoped(Task::class, $taskId, [
            'status' => $status,
        ]);
    }

    public function render()
    {
        $query = $this->scopedQuery(Task::class);

        return view('livewire.tasks', [
            'tasks' => $this->applyFilters(
                $query,
                'title',
                "FIELD(status, 'pending', 'in_progress', 'done')"
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
