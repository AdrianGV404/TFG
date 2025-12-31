<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;

class Projects extends Component
{
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

        $this->editingProjectId = $project->id;
        $this->editingName = $project->name;
        $this->editingDescription = $project->description ?? '';
        $this->editingStatus = $project->status;
    }

    public function cancelEdit()
    {
        $this->reset([
            'editingProjectId',
            'editingName',
            'editingDescription',
            'editingStatus',
        ]);
    }

    public function saveEdit()
    {
        $this->validate([
            'editingName' => 'required|string|max:255',
            'editingDescription' => 'nullable|string',
            'editingStatus' => 'required|in:active,archived',
        ]);

        Project::where('id', $this->editingProjectId)->update([
            'name' => $this->editingName,
            'description' => $this->editingDescription,
            'status' => $this->editingStatus,
        ]);

        $this->dispatch(
            'notify',
            message: "Proyecto \"{$this->editingName}\" actualizado con éxito",
            type: 'success'
        );

        $this->cancelEdit();
    }

    public function delete(int $projectId)
    {
        $project = Project::findOrFail($projectId);
        $name = $project->name;

        $project->delete();

        $this->dispatch(
            'notify',
            message: "Proyecto \"$name\" eliminado con éxito",
            type: 'danger'
        );
    }
    public function onProjectCreated(?string $name = null)
    {
        $this->dispatch(
            'notify',
            message: $name
                ? "Proyecto \"$name\" creado con éxito"
                : "Proyecto creado con éxito",
            type: 'success'
        );
    }

    public function refreshProjects() {}

    public function render()
    {
        return view('livewire.projects', [
            'projects' => Project::latest()->get(),
        ]);
    }

    public function confirmDelete(int $projectId)
    {
        $project = Project::findOrFail($projectId);

        $this->dispatch(
            'confirm-delete',
            title: 'Eliminar proyecto',
            message: "¿Seguro que quieres eliminar el proyecto \"{$project->name}\"? Esta acción no se puede deshacer.",
            action: 'delete-project',
            id: $projectId
        );
    }

    public function deleteFromModal(int $id)
    {
        $this->delete($id);
    }

}
