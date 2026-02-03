<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;
use App\Livewire\Traits\WithSearchAndPagination;
use App\Livewire\Traits\Confirmable;
use App\Livewire\Traits\HasInlineEditing;
use App\Livewire\Traits\Notifies;
use App\Livewire\Traits\FormValidationRules;

class Projects extends Component
{
    use WithSearchAndPagination, Confirmable, HasInlineEditing, Notifies, FormValidationRules;
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
        $project = Project::withTrashed()->findOrFail($projectId); // incluimos softdeleted
        $name = $project->name;

        if ($project->trashed()) {
            // Si ya está en softdelete, eliminar definitivamente
            $project->forceDelete();
            $this->notify("Proyecto \"$name\" eliminado definitivamente", 'danger');
        } else {
            // Si no estaba eliminado, hacer softdelete
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

        $query = Project::withCount([
            'tasks as total_tasks' => fn($q) => $this->showDeleted ? $q->withTrashed() : $q,
            'tasks as pending_tasks' => fn($q) => ($this->showDeleted ? $q->withTrashed() : $q)->where('status', 'pending'),
            'tasks as in_progress_tasks' => fn($q) => ($this->showDeleted ? $q->withTrashed() : $q)->where('status', 'in_progress'),
            'tasks as done_tasks' => fn($q) => ($this->showDeleted ? $q->withTrashed() : $q)->where('status', 'done'),
        ]);

        // Incluir softdeleted si el usuario lo indica
        if ($this->showDeleted) {
            $query = $query->withTrashed();
        }
        // Ordenar: primero activos, luego archivados
        $query = $query->orderByRaw("FIELD(status, 'active', 'archived')");

        // Aplicar filtros de búsqueda/paginación
        $projects = $this->applyFilters($query, 'name');

        return view('livewire.projects.projects', [
            'projects' => $projects,
            'isProjectList' => true,
        ]);
    }

    public function confirmDelete(int $projectId)
    {
        $project = Project::withTrashed()->findOrFail($projectId);

        $this->dispatchConfirmDelete(
            'Eliminar proyecto',
            $project->trashed()
                ? "Este proyecto <i>{$project->name}</i> ya está eliminado.<br>Se borrará permanentemente.<br>Esta acción <b>no se puede deshacer</b>."
                : "¿Seguro que quieres eliminar el proyecto <br> <i>{$project->name}</i>?<br>Esta acción solo la podrá deshacer el administrador.",
            'delete-project',
            $projectId,
            $project->trashed() // isPermanent
        );
    }


    public function deleteFromModal(int $id)
    {
        $this->delete($id);
    }

    public function restoreProject(int $projectId)
    {
        $project = Project::withTrashed()->findOrFail($projectId);
        $project->restore();

        // Restaurar también las tareas relacionadas
        foreach ($project->tasks()->withTrashed()->get() as $task) {
            $task->restore();
        }

        $this->notify("Proyecto y sus tareas restauradas con éxito", 'success');
    }
}
