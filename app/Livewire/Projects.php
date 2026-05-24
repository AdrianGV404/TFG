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

    public function startEdit(int $projectId)
    {
        $project = Project::findOrFail($projectId);

        if (!$this->canManage($project)) {
            $this->notify('No tienes permisos para editar este proyecto', 'danger');
            return;
        }

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

        if (!$this->canManage($project)) {
            $this->notify('No tienes permisos para eliminar este proyecto', 'danger');
            return;
        }

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
    $user = auth()->user();

    $query = Project::query()
        ->where('tenant_id', $user->tenant_id)
        ->with(['users'])
        ->where(function ($q) use ($user) {

            // ADMIN: ve todo el tenant
            if ($user->isAdmin()) {
                return;
            }

            // CREADOR o ASIGNADO
            $q->where('created_by', $user->id)
              ->orWhereHas('users', function ($q2) use ($user) {
                  $q2->where('users.id', $user->id);
              });
        })
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

        if (!$this->canManage($project)) {
            $this->notify('No tienes permisos para restaurar este proyecto', 'danger');
            return;
        }

        $project->restore();
        $project->status = 'archived';
        $project->saveQuietly();

        foreach ($project->tasks()->withTrashed()->get() as $task) {
            $task->restore();
        }

        $this->notify("Proyecto \"{$project->name}\" y sus tareas restauradas con éxito", 'success');
    }

    private function canManage(Project $project): bool
    {
        $user = auth()->user();

        return $user->isAdmin()
            || $project->created_by === $user->id;
    }
}