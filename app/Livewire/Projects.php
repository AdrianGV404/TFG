<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;
use App\Livewire\Traits\WithSearchAndPagination;
use App\Livewire\Traits\HasInlineEditing;
use App\Livewire\Traits\Notifies;
use App\Livewire\Traits\FormValidationRules;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Projects extends Component
{
    use WithSearchAndPagination, HasInlineEditing, Notifies, FormValidationRules, AuthorizesRequests;

    public bool $showForm = false;
    public bool $showDeleted = false;

    public ?int $editingProjectId = null;
    public string $editingName = '';
    public string $editingDescription = '';
    public string $editingStatus = 'active';
    public array $searchLabels = [];
    protected $listeners = [
        'delete-project' => 'deleteFromModal',
        'restore-project' => 'restoreProject',
        'projectCreated' => 'onProjectCreated',
        'projectDeleted' => 'refreshProjects',
        'closeForm' => 'closeForm',
    ];

    public function openForm() { $this->showForm = true; }
    public function closeForm() { $this->showForm = false; }

    public function startEdit(int $projectId)
    {
        $project = Project::findOrFail($projectId);

        try {
            $this->authorize('update', $project);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
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
        $project = Project::findOrFail($this->editingProjectId);
        
        try {
            $this->authorize('update', $project);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notify('No tienes permisos para editar este proyecto', 'danger');
            $this->cancelEdit();
            return;
        }

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

        try {
            $this->authorize('delete', $project);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
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

    public function deleteFromModal(int $id)
    {
        $this->delete($id);
    }

    public function onProjectCreated(?string $name = null)
    {
        $this->notify($name ? "Proyecto \"$name\" creado con éxito" : "Proyecto creado con éxito", 'success');
    }

    public function refreshProjects() {}

    public function restoreProject(int $id)
    {
        $project = Project::withTrashed()->findOrFail($id);

        try {
            $this->authorize('delete', $project);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
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

public function render()
{
    $user = auth()->user();

    // 1. Iniciamos la consulta
    $query = Project::query()
        ->where('tenant_id', $user->tenant_id)
        ->with(['users']);

    // 2. Aplicamos filtros condicionales de etiquetas
    if (!empty($this->searchLabels)) {
        $query->whereHas('tasks', function ($q) {
            $q->whereHas('labels', function ($qLabel) {
                $qLabel->whereIn('labels.id', $this->searchLabels);
            });
        });
    }

    // 3. Consultas withCount
    $query->withCount([
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

    // Ordenamiento y paginación (usando tu Trait)
    $query = $query->orderByRaw("FIELD(status, 'active', 'archived')");
    $projects = $this->applyFilters($query, 'name');

    // 4. OBTENER ETIQUETAS Y ENVIAR A LA VISTA
    // Asegúrate de que el modelo Label tenga 'tenant_id'
    $labels = \App\Models\Label::where('tenant_id', $user->tenant_id)->get();

    return view('livewire.projects.projects', [
        'projects' => $projects,
        'labels'   => $labels,
        'isProjectList' => true,
    ]);
}
}