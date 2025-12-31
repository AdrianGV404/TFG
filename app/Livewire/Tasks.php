<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Task;
use App\Models\Project;

class Tasks extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public Project $project;

    public string $searchTitle = '';
    public string $searchId = '';
    public string $orderBy = 'id_desc';
    public int $perPage = 10;

    /* =========================
       EDICIÓN INLINE
    ========================= */

    public ?int $editingTaskId = null;
    public string $editingTitle = '';
    public string $editingDescription = '';

    protected $listeners = [
        'taskCreated' => 'onTaskCreated',
    ];

    public function onTaskCreated()
    {
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

        if ($this->searchTitle !== '') {
            $query->where('title', 'like', '%' . $this->searchTitle . '%');
        }

        if ($this->searchId !== '') {
            $query->where('id', 'like', $this->searchId . '%');
        }

        match ($this->orderBy) {
            'id_asc' => $query->orderBy('id', 'asc'),
            'status' => $query->orderByRaw(
                "FIELD(status, 'pending', 'in_progress', 'done')"
            ),
            default => $query->orderByDesc('id'),
        };

        return view('livewire.tasks', [
            'tasks' => $query->paginate($this->perPage),
        ]);
    }
}
