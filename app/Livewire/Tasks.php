<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use App\Models\Project;
use App\Livewire\Traits\WithSearchAndPagination;

class Tasks extends Component
{
    use WithSearchAndPagination;

    protected $paginationTheme = 'bootstrap';

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
        $task = Task::where('project_id', $this->project->id)
            ->where('id', $taskId)
            ->firstOrFail();

        $this->editingTaskId = $task->id;
        $this->editingTitle = $task->title;
        $this->editingDescription = $task->description ?? '';
    }

    public function cancelEdit()
    {
        $this->reset([
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

        Task::where('project_id', $this->project->id)
            ->where('id', $this->editingTaskId)
            ->update([
                'title' => $this->editingTitle,
                'description' => $this->editingDescription,
            ]);

        $this->dispatch(
            'notify',
            message: "Tarea \"{$this->editingTitle}\" actualizada con éxito",
            type: 'success'
        );

        $this->cancelEdit();
    }

    public function delete(int $taskId)
    {
        Task::where('project_id', $this->project->id)
            ->where('id', $taskId)
            ->delete();

        $this->dispatch(
            'notify',
            message: 'Tarea eliminada con éxito',
            type: 'danger'
        );

        $this->resetPage();
    }

    public function updateStatus(int $taskId, string $status)
    {
        $task = Task::where('project_id', $this->project->id)
            ->where('id', $taskId)
            ->firstOrFail();

        $task->status = $status;
        $task->save();
    }

    public function render()
    {
        $query = Task::where('project_id', $this->project->id);

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
        $task = Task::where('project_id', $this->project->id)
            ->where('id', $taskId)
            ->firstOrFail();

        $this->dispatch(
            'confirm-delete',
            title: 'Eliminar tarea',
            message: "¿Seguro que quieres eliminar la tarea \"{$task->title}\"? Esta acción no se puede deshacer.",
            action: 'delete-task',
            id: $taskId
        );
    }

    public function deleteFromModal(int $id)
    {
        $this->delete($id);
    }

}
