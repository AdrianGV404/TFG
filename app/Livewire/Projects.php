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
        'projectCreated' => 'refreshProjects',
        'projectDeleted' => 'refreshProjects',
        'closeForm' => 'closeForm',
    ];

    /* =========================
       FORMULARIO CREAR
    ========================= */

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

        $this->cancelEdit();
    }

    /* =========================
       ACCIONES
    ========================= */

    public function delete(int $projectId)
    {
        Project::findOrFail($projectId)->delete();
        $this->dispatch('projectDeleted');
    }

    public function refreshProjects()
    {
        // fuerza re-render
    }

    /* =========================
       RENDER
    ========================= */

    public function render()
    {
        return view('livewire.projects', [
            'projects' => Project::latest()->get(),
        ]);
    }
}
