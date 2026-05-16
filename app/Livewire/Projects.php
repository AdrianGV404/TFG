<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;
use App\Livewire\Traits\WithSearchAndPagination;
use App\Livewire\Traits\HasInlineEditing;
use App\Livewire\Traits\Notifies;
use App\Livewire\Traits\FormValidationRules;

class Projects extends Component
{
    use WithSearchAndPagination, HasInlineEditing, Notifies, FormValidationRules;

    public bool $showForm = false;
    public bool $showDeleted = false;

    /* =========================
       EDICIÓN INLINE
    ========================= */
    public ?int $editingProjectId = null;
    public string $editingName = '';
    public string $editingDescription = '';
    public string $editingStatus = 'active';

    protected $listeners = [
        'delete-project' => 'deleteFromModal',
        'restore-project' => 'restoreProject',
        'projectCreated' => 'onProjectCreated',
        'projectDeleted' => 'refreshProjects',
        'closeForm' => 'closeForm',
    ];

    public function openForm()
    {
        $this->showForm = true;
    }

    public function closeForm()
    {
        $this->showForm = false;
    }

    /* =========================
       EDICIÓN INLINE
    ========================= */

    public function startEdit(int $projectId)
    {
        $project = Project::findOrFail($projectId);

        $this->startEditingModel($project, 'editingProjectId', [
            'editingName' => 'name',
            'editingDescription' => 'description',
            'editingStatus' => 'status',
        ]);
    }

    public function cancelEdit()
    {
        $this->cancelEditing([
            'editingProjectId',
            'editingName',
            'editingDescription',
            'editingStatus',
        ]);
    }

    public function saveEdit()
    {
        $this->validate($this->projectRulesForEditing());

        $this->applyModelUpdate(Project::class, $this->editingProjectId, [
            'name' => $this->editingName,
            'description' => $this->editingDescription,
            'status' => $this->editingStatus,
        ]);

        $this->notify("Proyecto \"{$this->editingName}\" actualizado con éxito", 'success');

        $this->cancelEdit();
    }

    public function delete(int $projectId)
    {
        $project = Project::withTrashed()->findOrFail($projectId);
        $name = $project->name;

        if ($project->trashed()) {
            $project->forceDelete();
            $this->notify("Proyecto \"$name\" eliminado definitivamente", 'danger');
        } else {
            $project->delete();
            $this->notify("Proyecto \"$name\" eliminado con éxito", 'danger');
        }
    }

    public function onProjectCreated(?string $name = null)
    {
        $this->notify($name ? "Proyecto \"$name\" creado con éxito" : "Proyecto creado con éxito", 'success');
    }

    public function refreshProjects() {}

    public function render()
    {
        $query = Project::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->withCount([
                'tasks as total_tasks' => function ($q) {
                    if ($this->showDeleted) $q->withTrashed();
                },
                'tasks as pending_tasks' => function ($q) {
                    if ($this->showDeleted) $q->withTrashed();
                    $q->where('status', 'pending');
                },
                'tasks as in_progress_tasks' => function ($q) {
                    if ($this->showDeleted) $q->withTrashed();
                    $q->where('status', 'in_progress');
                },
                'tasks as done_tasks' => function ($q) {
                    if ($this->showDeleted) $q->withTrashed();
                    $q->where('status', 'done');
                },
            ]);

        if ($this->showDeleted) {
            $query = $query->withTrashed();
        }

        $query = $query->orderByRaw("FIELD(status, 'active', 'archived')");
        $projects = $this->applyFilters($query, 'name');

        return view('livewire.projects.projects', [
            'projects' => $projects,
            'isProjectList' => true,
        ]);
    }

    public function deleteFromModal(int $id)
    {
        $this->delete($id);
    }

    public function restoreProject(int $id)
    {
        $project = Project::withTrashed()->findOrFail($id);
        $project->restore();
        $project->status = 'archived';
        $project->saveQuietly();

        foreach ($project->tasks()->withTrashed()->get() as $task) {
            $task->restore();
        }

        $this->notify("Proyecto \"{$project->name}\" y sus tareas restauradas con éxito", 'success');
    }
}
