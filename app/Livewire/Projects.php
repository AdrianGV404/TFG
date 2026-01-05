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
        $project = Project::findOrFail($projectId);
        $name = $project->name;

        $project->delete();

        $this->notify("Proyecto \"$name\" eliminado con éxito", 'danger');
    }
    public function onProjectCreated(?string $name = null)
    {
        $this->notify($name ? "Proyecto \"$name\" creado con éxito" : "Proyecto creado con éxito", 'success');
    }

    public function refreshProjects() {}

    public function render()
    {
        $query = Project::withCount([
            'tasks as total_tasks',
            'tasks as pending_tasks' => fn ($q) => $q->where('status', 'pending'),
            'tasks as in_progress_tasks' => fn ($q) => $q->where('status', 'in_progress'),
            'tasks as done_tasks' => fn ($q) => $q->where('status', 'done'),
        ]);

        $projects = $this->applyFilters(
            $query,
            'name',
            "FIELD(status, 'active', 'archived')"
        );

        return view('livewire.projects', [
            'projects' => $projects,
        ]);
    }


    public function confirmDelete(int $projectId)
    {
        $project = Project::findOrFail($projectId);

        $this->dispatchConfirmDelete(
            'Eliminar proyecto',
            "¿Seguro que quieres eliminar el proyecto \"{$project->name}\"? Esta acción no se puede deshacer.",
            'delete-project',
            $projectId
        );
    }

    public function deleteFromModal(int $id)
    {
        $this->delete($id);
    }

}
