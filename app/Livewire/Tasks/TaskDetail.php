<?php

namespace App\Livewire\Tasks;

use Livewire\Component;
use App\Models\Task;
use App\Models\Label;
use App\Models\TaskTimeEntry;
use App\Livewire\Traits\Notifies;
use App\Livewire\Traits\RegistraHistoricoHoras;   // ← NUEVO
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskDetail extends Component
{
    use Notifies, AuthorizesRequests, RegistraHistoricoHoras;   // ← NUEVO

    public Task $task;

    // Campos editables
    public string $title       = '';
    public string $description = '';
    public string $status      = 'pending';
    public int    $priority    = 5;
    public ?string $due_date   = null;
    public array  $selected_labels = [];

    // Modal tiempo manual
    public bool $showManualTimeModal = false;
    public int  $manualHours         = 0;
    public int  $manualMinutes       = 0;

    // Control edición
    public bool $isEditing = false;

    protected $rules = [
        'title'             => 'required|string|max:255',
        'description'       => 'nullable|string',
        'status'            => 'required|in:pending,in_progress,on_hold,testing,done',
        'priority'          => 'required|integer|min:0|max:10',
        'due_date'          => 'nullable|date',
        'selected_labels'   => 'nullable|array',
        'selected_labels.*' => 'distinct|exists:labels,id',
    ];

    public function mount(Task $task): void
    {
        try {
            $this->authorize('view', $task);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            abort(403, 'No tienes permiso para ver esta tarea.');
        }

        $this->task = $task->load(['project', 'labels', 'timeEntries.user', 'creator']);
        $this->syncFromTask();
    }

    private function syncFromTask(): void
    {
        $this->title           = $this->task->title;
        $this->description     = $this->task->description ?? '';
        $this->status          = $this->task->status;
        $this->priority        = $this->task->priority;
        $this->due_date        = $this->task->due_date?->format('Y-m-d');
        $this->selected_labels = $this->task->labels->pluck('id')->toArray();
    }

    public function startEdit(): void
    {
        try {
            $this->authorize('update', $this->task);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notify('No tienes permiso para editar esta tarea.', 'danger');
            return;
        }
        $this->isEditing = true;
    }

    public function cancelEdit(): void
    {
        $this->syncFromTask();
        $this->isEditing = false;
    }

    public function save(): void
    {
        try {
            $this->authorize('update', $this->task);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notify('No tienes permiso para editar esta tarea.', 'danger');
            return;
        }

        $this->validate();

        $this->task->update([
            'title'       => $this->title,
            'description' => $this->description,
            'status'      => $this->status,
            'priority'    => $this->priority,
            'due_date'    => $this->due_date ?: null,
        ]);

        $this->task->labels()->sync($this->selected_labels);
        $this->task->refresh()->load(['labels', 'timeEntries.user', 'creator', 'project']);

        $this->isEditing = false;
        $this->notify('Tarea actualizada con éxito', 'success');
    }

    public function delete(): void
    {
        try {
            $this->authorize('delete', $this->task);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notify('No tienes permiso para eliminar esta tarea.', 'danger');
            return;
        }

        $projectId = $this->task->project_id;
        $this->task->delete();
        $this->notify('Tarea eliminada', 'danger');

        $this->redirect(route('projects.show', $projectId));
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  TIME TRACKING
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Inicia o detiene el contador.
     * Al detener acumula en historico_horas_dia a través del trait.
     */
    public function toggleTimeTracking(): void
    {
        $running = TaskTimeEntry::where('task_id', $this->task->id)
            ->where('user_id', auth()->id())
            ->where('is_running', true)
            ->first();

        if ($running) {
            // ── Detener: cierra entrada, pone en pausa y acumula histórico ───
            $this->detenerYRegistrar($running, $this->task);
            $this->notify('Tiempo detenido — tarea en pausa', 'success');
        } else {
            // ── Iniciar: crea entrada y pone en progreso ──────────────────────
            $this->iniciarGrabacion($this->task);
            $this->notify('Tiempo iniciado — tarea en progreso', 'success');
        }

        $this->task->refresh()->load(['timeEntries.user', 'labels', 'creator', 'project']);

        // Sincronizar la propiedad del componente con el nuevo estado de la tarea
        // para que el select de estado se actualice en el frontend
        $this->status = $this->task->status;
    }

    public function openManualTimeModal(): void
    {
        $this->manualHours         = 0;
        $this->manualMinutes       = 0;
        $this->showManualTimeModal = true;
    }

    public function closeManualTimeModal(): void
    {
        $this->reset(['showManualTimeModal', 'manualHours', 'manualMinutes']);
    }

    /**
     * Guarda tiempo manual y lo acumula en historico_horas_dia.
     */
    public function saveManualTime(): void
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

        // 1. Guardar entrada de tiempo
        TaskTimeEntry::create([
            'task_id'          => $this->task->id,
            'user_id'          => auth()->id(),
            'started_at'       => now(),
            'ended_at'         => now(),
            'duration_seconds' => $segundos,
            'is_running'       => false,
        ]);

        // 2. Acumular en histórico diario ← NUEVO
        $this->registrarTiempoManual($this->task, $segundos);

        $this->task->refresh()->load(['timeEntries.user', 'labels', 'creator', 'project']);
        $this->notify('Tiempo añadido correctamente', 'success');
        $this->closeManualTimeModal();
    }

    public function render()
    {
        $labels       = Label::where('tenant_id', auth()->user()->tenant_id)->get();
        $runningEntry = TaskTimeEntry::where('task_id', $this->task->id)
            ->where('user_id', auth()->id())
            ->where('is_running', true)
            ->first();

        $totalSeconds = $this->task->timeEntries
            ->where('is_running', false)
            ->sum('duration_seconds');

        return view('livewire.tasks.task-detail', compact('labels', 'runningEntry', 'totalSeconds'));
    }
}