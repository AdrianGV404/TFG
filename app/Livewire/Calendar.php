<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Livewire\Attributes\On;
use Carbon\Carbon;

/**
 * Calendario de Trabajo — Caso de Uso 07
 *
 * Responsabilidades:
 *  - Renderizar la cuadrícula mensual con tareas filtradas por proyecto/estado/usuario.
 *  - Filtrar automáticamente según el rol (CU07, paso 2b: empleado ve solo sus tareas no "done").
 *  - Persistir los filtros seleccionados en sesión (CU07, punto 3b de persistencia).
 *  - Manejar Drag & Drop validando permisos ACL (CU07, paso 5a).
 *  - Manejar cambio rápido de estado vía clic (CU07, paso 5b).
 *  - Calcular el Heatmap diario para la intensidad de color de cada celda.
 *  - Detectar cambios de otros usuarios en segundo plano (CU07, punto 3b de sincronización).
 */
class Calendar extends Component
{
    // ── Filtros ────────────────────────────────────────────────────────────────

    public string $projectId = '';
    public string $status    = '';
    public string $userId    = '';

    // ── Datos para los <select> ────────────────────────────────────────────────

    public array $projects = [];
    public array $users    = [];

    /** Mapa de estados disponibles (clave BD → etiqueta visual). */
    public array $statuses = [
        'pending'     => 'Pendiente',
        'in_progress' => 'En Progreso',
        'on_hold'     => 'En Pausa',
        'testing'     => 'En Pruebas',
        'done'        => 'Completada',
    ];

    /** Indica si el usuario autenticado puede ver el filtro de otros usuarios. */
    public bool $canFilterByUser = false;

    // ── Lifecycle ──────────────────────────────────────────────────────────────

    public function mount(): void
    {
        $user = auth()->user();

        // CU07, paso 2: Lógica de filtrado inicial según rol
        if (in_array($user->role, ['admin', 'responsable'])) {
            // Responsable / admin: ven todo el tenant por defecto.
            $this->canFilterByUser = true;
            $this->users = User::where('tenant_id', $user->tenant_id)
                ->select('id', 'name')
                ->orderBy('name')
                ->get()
                ->toArray();

            // Restaurar filtros de sesión
            $filters           = session('calendar_filters', []);
            $this->projectId   = $filters['projectId'] ?? '';
            $this->status      = $filters['status']    ?? '';
            $this->userId      = $filters['userId']    ?? '';
        } else {
            // CU07, paso 2b — Empleado: forzar a sus tareas y excluir "done"
            $this->canFilterByUser = false;
            $this->userId          = (string) $user->id;

            $filters           = session('calendar_filters', []);
            $this->projectId   = $filters['projectId'] ?? '';
            // Empleado solo puede filtrar entre sus estados activos; si venía
            // un filtro previo de "done" se descarta para cumplir CU07.
            $savedStatus       = $filters['status'] ?? '';
            $this->status      = ($savedStatus === 'done') ? '' : $savedStatus;
        }

        // Proyectos del tenant (todos los roles los ven para poder filtrar)
        $this->projects = Project::where('tenant_id', $user->tenant_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    // ── Actualización reactiva de filtros ──────────────────────────────────────

    /**
     * Se dispara con wire:model.live cuando cambia cualquier filtro.
     * Persiste el estado en sesión y recarga el calendario (CU07, punto 3a).
     */
    public function updated(string $property): void
    {
        // Empleado no puede cambiar el userId al de otro usuario
        if ($property === 'userId' && !$this->canFilterByUser) {
            $this->userId = (string) auth()->id();
        }

        // CU07, punto 3a — Persistencia de filtros
        session(['calendar_filters' => [
            'projectId' => $this->projectId,
            'status'    => $this->status,
            'userId'    => $this->userId,
        ]]);

        $this->dispatch('refresh-calendar');
    }

    // ── API consumida por FullCalendar ─────────────────────────────────────────

    /**
     * Construye y envía los eventos al frontend.
     * Invocado por el evento JS 'fetch-tasks'.
     */
    #[On('fetch-tasks')]
    public function getTasksData(): void
    {
        $user  = auth()->user();
        $query = Task::query()
            ->with('project')
            ->where('tenant_id', $user->tenant_id)   // aislamiento multi-tenant
            ->whereNotNull('due_date');

        // Filtro: proyecto
        if ($this->projectId !== '') {
            $query->where('project_id', $this->projectId);
        }

        // Filtro: estado
        // CU07, paso 2b — empleado no ve tareas "done" si no hay filtro explícito
        if ($this->status !== '') {
            $query->where('status', $this->status);
        } elseif (!$this->canFilterByUser) {
            // Empleado sin filtro explícito → excluir completadas
            $query->where('status', '!=', 'done');
        }

        // Filtro: usuario asignado
        if ($this->userId !== '') {
            $query->whereHas(
                'users',
                fn ($u) => $u->where('users.id', $this->userId)
            );
        }

        $tasks = $query->get();

        // ── Heatmap: cantidad de tareas por día ───────────────────────────────
        $heatmapData = $tasks
            ->groupBy(fn ($t) => Carbon::parse($t->due_date)->format('Y-m-d'))
            ->map(fn ($dayTasks) => $dayTasks->count())
            ->toArray();

        // ── Formatear eventos para FullCalendar ───────────────────────────────
        $calendarEvents = $tasks->map(function (Task $task) {
            $color = $this->statusColor($task->status);

            return [
                'id'              => $task->id,
                'title'           => $task->title,
                'start'           => Carbon::parse($task->due_date)->format('Y-m-d'),
                'backgroundColor' => $color,
                'borderColor'     => $color,
                'extendedProps'   => [
                    'status'      => $task->status,
                    'statusLabel' => $this->statuses[$task->status] ?? $task->status,
                    'projectName' => $task->project->name ?? 'Sin Proyecto',
                    'canEdit'     => $this->canEdit($task),
                ],
            ];
        });

        $this->dispatch('load-calendar-data', events: $calendarEvents, heatmap: $heatmapData);
    }

    // ── Acción: Drag & Drop ────────────────────────────────────────────────────

    /**
     * Actualiza la fecha límite de una tarea al arrastrarla a otro día.
     * CU07, paso 5a.
     */
    #[On('update-task-date')]
    public function updateDate(int $taskId, string $newDate): void
    {
        $task = Task::where('tenant_id', auth()->user()->tenant_id)->find($taskId);

        if (! $task) {
            $this->dispatch('notify', type: 'error', message: 'Tarea no encontrada.');
            $this->dispatch('revert-event');
            return;
        }

        // CU07, paso 5a — validar permiso de edición
        if (! $this->canEdit($task)) {
            $this->dispatch('notify', type: 'error',
                message: 'No tienes permisos para modificar la fecha de esta tarea.');
            $this->dispatch('revert-event');   // JS revierte visualmente el drop
            return;
        }

        // Validar formato de fecha
        try {
            $date = Carbon::createFromFormat('Y-m-d', $newDate);
        } catch (\Exception) {
            $this->dispatch('notify', type: 'error', message: 'Fecha no válida.');
            $this->dispatch('revert-event');
            return;
        }

        $task->update(['due_date' => $date->toDateString()]);

        $this->dispatch('notify', type: 'success', message: 'Fecha límite actualizada.');
        $this->dispatch('refresh-calendar');
    }

    // ── Acción: Cambio rápido de estado ───────────────────────────────────────

    /**
     * Persiste el nuevo estado seleccionado desde el popup de SweetAlert2.
     * CU07, paso 5b.
     */
    #[On('update-task-status')]
    public function updateStatus(int $taskId, string $newStatus): void
    {
        $task = Task::where('tenant_id', auth()->user()->tenant_id)->find($taskId);

        if (! $task) {
            $this->dispatch('notify', type: 'error', message: 'Tarea no encontrada.');
            return;
        }

        if (! $this->canEdit($task)) {
            $this->dispatch('notify', type: 'error',
                message: 'No tienes permisos para editar esta tarea.');
            return;
        }

        if (! array_key_exists($newStatus, $this->statuses)) {
            $this->dispatch('notify', type: 'error', message: 'Estado no válido.');
            return;
        }

        $task->update(['status' => $newStatus]);

        $this->dispatch('notify', type: 'success',
            message: 'Estado actualizado a «' . $this->statuses[$newStatus] . '».');
        $this->dispatch('refresh-calendar');
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    /**
     * Verifica si el usuario autenticado puede editar la tarea dada.
     * Responsable/admin: siempre. Empleado: solo si está asignado (CU07, paso 5a / CU05).
     */
    private function canEdit(Task $task): bool
    {
        $user = auth()->user();

        if (in_array($user->role, ['admin', 'responsable'])) {
            return true;
        }

        return $task->users()->where('users.id', $user->id)->exists();
    }

    /**
     * Devuelve el color hexadecimal asociado a cada estado.
     */
    private function statusColor(string $status): string
    {
        return match ($status) {
            'done'                   => '#198754',  // verde
            'in_progress', 'testing' => '#0d6efd',  // azul (activo)
            'on_hold'                => '#6c757d',  // gris (pausa)
            'pending'                => '#ffc107',  // amarillo
            default                  => '#6c757d',
        };
    }

    // ── Render ─────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.calendar');
    }
}