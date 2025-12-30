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

    // Filtros
    public string $searchTitle = '';
    public string $searchId = '';
    public string $orderBy = 'id_desc';
    public int $perPage = 10;

    protected $listeners = [
        'taskCreated' => 'onTaskCreated',
    ];

    /* =========================
       RESETEOS + NORMALIZACIÓN
    ========================= */

    public function updatedSearchTitle()
    {
        // Limpieza básica (UX + rendimiento)
        $this->searchTitle = trim($this->searchTitle);
        $this->resetPage();
    }

    public function updatedSearchId()
    {
        // SOLO números → evita basura, mejora queries y seguridad
        $this->searchId = preg_replace('/[^0-9]/', '', $this->searchId);
        $this->resetPage();
    }

    public function updatedOrderBy()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    /* =========================
       EVENTOS
    ========================= */

    public function onTaskCreated()
    {
        $this->resetPage();
    }

    /* =========================
       ACCIONES
    ========================= */

    public function delete(int $taskId)
    {
        Task::where('project_id', $this->project->id)
            ->where('id', $taskId)
            ->delete();

        $this->resetPage();
    }

    /**
     * IMPORTANTE:
     * Usar instancia del modelo
     * → dispara observer (updated)
     */
    public function updateStatus(int $taskId, string $status)
    {
        $task = Task::where('project_id', $this->project->id)
            ->where('id', $taskId)
            ->firstOrFail();

        $task->status = $status;
        $task->save();
    }

    /* =========================
       RENDER
    ========================= */

    public function render()
    {
        $query = Task::where('project_id', $this->project->id);

        /* ===== BUSCAR POR TÍTULO ===== */
        if ($this->searchTitle !== '') {
            $query->where('title', 'like', '%' . $this->searchTitle . '%');
        }

        /* ===== BUSCAR POR ID (PARCIAL, EFICIENTE) ===== */
        if ($this->searchId !== '') {
            $query->where('id', 'like', $this->searchId . '%');
        }

        /* ===== ORDEN ===== */
        match ($this->orderBy) {
            'id_asc' => $query->orderBy('id', 'asc'),
            'status' => $query->orderByRaw(
                "FIELD(status, 'pending', 'in_progress', 'done')"
            ),
            default => $query->orderByDesc('id'),
        };

        $tasks = $query->paginate($this->perPage);

        return view('livewire.tasks', compact('tasks'));
    }
}
