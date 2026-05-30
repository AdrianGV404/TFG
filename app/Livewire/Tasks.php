<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use App\Models\Project;
use App\Models\Label;
use App\Livewire\Traits\WithSearchAndPagination;
use App\Livewire\Traits\HasInlineEditing;
use App\Livewire\Traits\ScopedByProject;
use App\Livewire\Traits\Notifies;
use App\Livewire\Traits\FormValidationRules;
use App\Livewire\Traits\RegistraHistoricoHoras;   // ← NUEVO
use App\Models\TaskTimeEntry;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Tasks extends Component
{
    use WithSearchAndPagination,
        HasInlineEditing,
        Notifies,
        ScopedByProject,
        FormValidationRules,
        AuthorizesRequests,
        RegistraHistoricoHoras;   // ← NUEVO

    public bool $showForm = false;
    public Project $project;
    public bool $showDeleted = false;

    /* =========================
       EDICIÓN INLINE
    ========================= */
    public ?int $editingTaskId = null;
    public string $editingTitle = '';
    public string $editingDescription = '';
    public array $editingStatus = [];
    public array $editingPriority = [];
    public array $originalStatus = [];
    public array $originalPriority = [];
    public int $taskFormKey = 0;
    public array $editingDueDate = [];
    public array $originalDueDate = [];
    public array $editingLabelIds = [];
    public array $originalLabelIds = [];

    /* =========================
       MODAL TIEMPO MANUAL
    ========================= */
    public bool $showManualTimeModal = false;
    public ?int $manualTimeTaskId = null;
    public int $manualHours = 0;
    public int $manualMinutes = 0;

    public array $searchLabels = [];

    protected $listeners = [
        'delete-task'   => 'deleteFromModal',
        'restore-task'  => 'restoreTask',
        'taskCreated'   => 'onTaskCreated',
        'closeForm'     => 'closeForm',
    ];

    public function mount()
    {
        $this->orderBy = 'priority';

        foreach ($this->project->tasks as $task) {
            $this->editingStatus[$task->id]   = $task->status;
            $this->editingPriority[$task->id] = $task->priority;
            $this->editingDueDate[$task->id]  = $task->due_date
                ? $task->due_date->format('Y-m-d')
                : null;

            $this->originalStatus[$task->id]   = $task->status;
            $this->originalPriority[$task->id] = $task->priority;
            $this->originalDueDate[$task->id]  = $this->editingDueDate[$task->id];

            $this->editingLabelIds[$task->id]  = ($task->labels ?? collect())->pluck('id')->toArray();
            $this->originalLabelIds[$task->id] = $this->editingLabelIds[$task->id];
        }
    }

    public function onTaskCreated()
    {
        $this->taskFormKey++;
        $this->resetPage();

        foreach ($this->project->tasks as $task) {
            if (! isset($this->editingStatus[$task->id])) {
                $this->editingStatus[$task->id]   = $task->status;
                $this->editingPriority[$task->id] = $task->priority;
                $this->editingDueDate[$task->id]  = $task->due_date
                    ? $task->due_date->format('Y-m-d')
                    : null;

                $this->originalStatus[$task->id]   = $task->status;
                $this->originalPriority[$task->id] = $task->priority;
                $this->originalDueDate[$task->id]  = $this->editingDueDate[$task->id];

                $this->editingLabelIds[$task->id]  = ($task->labels ?? collect())->pluck('id')->toArray();
                $this->originalLabelIds[$task->id] = $this->editingLabelIds[$task->id];
            }
        }
    }

    public function startEdit(int $taskId)
    {
        $task = $this->findScoped(Task::class, $taskId);

        try {
            $this->authorize('update', $task);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notify('No tienes permiso para editar esta tarea.', 'danger');
            return;
        }

        $this->startEditingModel($task, 'editingTaskId', [
            'editingTitle'       => 'title',
            'editingDescription' => 'description',
        ]);

        $this->originalStatus[$taskId]   = $task->status;
        $this->originalPriority[$taskId] = $task->priority;
        $this->originalDueDate[$taskId]  = $this->editingDueDate[$taskId];

        $this->editingLabelIds[$taskId]  = $task->labels->pluck('id')->toArray();
        $this->originalLabelIds[$taskId] = $this->editingLabelIds[$taskId];
    }

    public function cancelEdit()
    {
        if ($this->editingTaskId) {
            $this->editingStatus[$this->editingTaskId]   = $this->originalStatus[$this->editingTaskId];
            $this->editingPriority[$this->editingTaskId] = $this->originalPriority[$this->editingTaskId];
            $this->editingDueDate[$this->editingTaskId]  = $this->originalDueDate[$this->editingTaskId];
            $this->editingLabelIds[$this->editingTaskId] = $this->originalLabelIds[$this->editingTaskId];
        }

        $this->editingTaskId       = null;
        $this->editingTitle        = '';
        $this->editingDescription  = '';
    }

    public function saveEdit()
    {
        $taskModel = Task::find($this->editingTaskId);

        try {
            if ($taskModel) {
                $this->authorize('update', $taskModel);
            }
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notify('No tienes permiso para actualizar esta tarea.', 'danger');
            return;
        }

        $rules = array_merge($this->taskRulesForEditing(), [
            "editingLabelIds.{$this->editingTaskId}"   => 'nullable|array',
            "editingLabelIds.{$this->editingTaskId}.*" => 'distinct|exists:labels,id',
        ]);

        $this->validate($rules);

        $this->updateScoped(Task::class, $this->editingTaskId, [
            'title'       => $this->editingTitle,
            'description' => $this->editingDescription,
            'status'      => $this->editingStatus[$this->editingTaskId],
            'priority'    => $this->editingPriority[$this->editingTaskId],
            'due_date'    => $this->editingDueDate[$this->editingTaskId] ?: null,
        ]);

        if ($taskModel) {
            $taskModel->labels()->sync($this->editingLabelIds[$this->editingTaskId] ?? []);
        }

        $this->originalStatus[$this->editingTaskId]   = $this->editingStatus[$this->editingTaskId];
        $this->originalPriority[$this->editingTaskId] = $this->editingPriority[$this->editingTaskId];
        $this->originalDueDate[$this->editingTaskId]  = $this->editingDueDate[$this->editingTaskId];
        $this->originalLabelIds[$this->editingTaskId] = $this->editingLabelIds[$this->editingTaskId];

        $this->notify("Tarea \"{$this->editingTitle}\" actualizada con éxito", 'success');

        $this->editingTaskId       = null;
        $this->editingTitle        = '';
        $this->editingDescription  = '';
    }

    public function delete(int $taskId)
    {
        $task = Task::withTrashed()->findOrFail($taskId);

        try {
            $this->authorize('delete', $task);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notify('No tienes permiso para eliminar esta tarea.', 'danger');
            return;
        }

        $name = $task->title;

        if ($task->trashed()) {
            $task->forceDelete();
            $this->notify("Tarea \"$name\" eliminada definitivamente", 'danger');
        } else {
            $task->delete();
            $this->notify("Tarea \"$name\" eliminada con éxito", 'danger');
        }

        $this->resetPage();
    }

    public function restoreTask(int $id)
    {
        $task = Task::withTrashed()->findOrFail($id);

        try {
            $this->authorize('delete', $task);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notify('No tienes permiso para restaurar esta tarea.', 'danger');
            return;
        }

        $task->restore();
        $this->notify("Tarea \"{$task->title}\" restaurada con éxito", 'success');
    }

    public function deleteFromModal(int $id) { $this->delete($id); }

    public function openForm()  { $this->showForm = true; }
    public function closeForm() { $this->showForm = false; }

    public function render()
    {
        $query = $this->scopedQuery(Task::class)
            ->with(['labels', 'timeEntries.user']);

        if ($this->showDeleted) {
            $query = $query->withTrashed();
        }

        if (! empty($this->searchLabels)) {
            $query->whereHas('labels', function ($q) {
                $q->whereIn('labels.id', $this->searchLabels);
            });
        }

        $labels = Label::where('tenant_id', auth()->user()->tenant_id)->get();

        return view('livewire.tasks.tasks', [
            'tasks' => $this->applyFilters(
                $query,
                'title',
                "FIELD(status, 'pending', 'in_progress', 'done')",
                'priority'
            ),
            'labels' => $labels,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  TIME TRACKING
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Inicia o detiene el contador de tiempo para una tarea.
     * Al detener, registra las horas en historico_horas_dia.
     */
    public function toggleTimeTracking(int $taskId)
    {
        $task         = $this->findScoped(Task::class, $taskId);
        $runningEntry = TaskTimeEntry::query()
            ->where('task_id', $task->id)
            ->where('user_id', auth()->id())
            ->where('is_running', true)
            ->first();

        if ($runningEntry) {
            // ── Detener: cierra entrada, pone en pausa y acumula histórico ───
            $this->detenerYRegistrar($runningEntry, $task);
            $this->notify('Tiempo detenido — tarea en pausa', 'success');
            return;
        }

        // ── Iniciar: crea entrada y pone en progreso ──────────────────────────
        $this->iniciarGrabacion($task);
        $this->notify('Tiempo iniciado — tarea en progreso', 'success');
    }

    public function openManualTimeModal(int $taskId)
    {
        $this->manualTimeTaskId = $taskId;
        $this->manualHours      = 0;
        $this->manualMinutes    = 0;
        $this->showManualTimeModal = true;
    }

    public function closeManualTimeModal()
    {
        $this->reset(['showManualTimeModal', 'manualTimeTaskId', 'manualHours', 'manualMinutes']);
    }

    /**
     * Guarda tiempo manual y lo acumula en historico_horas_dia.
     */
    public function saveManualTime()
    {
        $this->validate([
            'manualHours'   => 'required|integer|min:0|max:999',
            'manualMinutes' => 'required|integer|min:0|max:59',
        ]);

        $segundos = ($this->manualHours * 3600) + ($this->manualMinutes * 60);

        if ($segundos <= 0) {
            $this->notify('Debes introducir un tiempo válido', 'danger');
            return;
        }

        $task = $this->findScoped(Task::class, $this->manualTimeTaskId);

        // 1. Guardar entrada de tiempo
        TaskTimeEntry::create([
            'task_id'          => $task->id,
            'user_id'          => auth()->id(),
            'started_at'       => now(),
            'ended_at'         => now(),
            'duration_seconds' => $segundos,
            'is_running'       => false,
        ]);

        // 2. Acumular en histórico diario ← NUEVO
        $this->registrarTiempoManual($task, $segundos);

        $this->notify('Tiempo añadido correctamente', 'success');
        $this->closeManualTimeModal();
    }
}