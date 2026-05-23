<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Livewire\Attributes\On;

class Calendar extends Component
{
    // Filtros
    public $projectId = '';
    public $status = '';
    public $userId = '';

    // Datos para los Selects
    public $projects = [];
    public $users = [];
    public $statuses = [
        'pending' => 'Pendiente',
        'in_progress' => 'En Progreso',
        'testing' => 'En Pruebas',
        'done' => 'Completada',
        'blocked' => 'Bloqueado'
    ];

    public function mount()
    {
        $user = auth()->user();

        // 3. Persistencia de filtros: Recuperar de sesión
        $filters = session('calendar_filters', []);
        $this->projectId = $filters['projectId'] ?? '';
        $this->status = $filters['status'] ?? '';

        // Control de Visibilidad según Rol
        if (in_array($user->role, ['admin', 'responsable'])) {
            $this->users = User::select('id', 'name')->get()->toArray();
            $this->userId = $filters['userId'] ?? '';
        } else {
            $this->userId = $user->id; // Forzar al empleado a ver solo lo suyo
        }

        $this->projects = Project::select('id', 'name')->get()->toArray();
    }

    // Se ejecuta cada vez que un filtro cambia (wire:model.live)
    public function updated($property)
    {
        // Guardamos los filtros en sesión
        session(['calendar_filters' => [
            'projectId' => $this->projectId,
            'status' => $this->status,
            'userId' => $this->userId,
        ]]);

        // Disparamos evento para que JS recargue el calendario
        $this->dispatch('refresh-calendar');
    }

    // API para que lo consuma FullCalendar mediante JS
    #[On('fetch-tasks')]
    public function getTasksData()
    {
        $query = Task::query()->with('project');

        // Aplicación de Filtros
        $query->when($this->projectId, fn($q) => $q->where('project_id', $this->projectId));
        $query->when($this->status, fn($q) => $q->where('status', $this->status));
        
        // Filtro de usuario (propio o del equipo)
        $query->when($this->userId, fn($q) => $q->whereHas('users', fn($u) => $u->where('users.id', $this->userId)));

        // Solo tareas que tengan fecha límite asignada
        $tasks = $query->whereNotNull('due_date')->get();

        // Calcular el Heatmap (cantidad de tareas por día filtrado)
        $heatmapData = $tasks->groupBy(fn($t) => \Carbon\Carbon::parse($t->due_date)->format('Y-m-d'))
                             ->map(fn($dayTasks) => $dayTasks->count())
                             ->toArray();

        // Formatear para FullCalendar
        $calendarEvents = $tasks->map(function($task) {
            $color = match($task->status) {
                'done' => '#198754', // Verde
                'in_progress', 'testing' => '#ffc107', // Amarillo
                'blocked' => '#dc3545', // Rojo
                default => '#6c757d', // Gris
            };

            return [
                'id' => $task->id,
                'title' => $task->title,
                'start' => \Carbon\Carbon::parse($task->due_date)->format('Y-m-d'),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'status' => $task->status,
                    'projectName' => $task->project->name ?? 'Sin Proyecto'
                ]
            ];
        });

        // Enviar datos al Frontend
        $this->dispatch('load-calendar-data', events: $calendarEvents, heatmap: $heatmapData);
    }

    // Acción: Arrastrar y Soltar (Drag & Drop)
    #[On('update-task-date')]
    public function updateDate($taskId, $newDate)
    {
        $task = Task::query()->find($taskId);
        
        if (!$this->canEdit($task)) {
            $this->dispatch('notify', type: 'error', message: 'No tienes permisos para editar esta tarea.');
            $this->dispatch('refresh-calendar'); // Revertir visualmente el cambio
            return;
        }

        $task->update(['due_date' => $newDate]);
        $this->dispatch('notify', type: 'success', message: 'Fecha límite actualizada.');
        $this->dispatch('refresh-calendar'); // Recalcular heatmap
    }

    // Acción: Clic rápido para cambiar estado
    #[On('update-task-status')]
    public function updateStatus($taskId, $newStatus)
    {
        $task = Task::query()->find($taskId);

        if (!$this->canEdit($task)) {
            $this->dispatch('notify', type: 'error', message: 'No tienes permisos para editar esta tarea.');
            return;
        }

        $task->update(['status' => $newStatus]);
        $this->dispatch('notify', type: 'success', message: 'Estado actualizado correctamente.');
        $this->dispatch('refresh-calendar');
    }

    // Validar permisos básicos
    private function canEdit($task)
    {
        $user = auth()->user();
        if (in_array($user->role, ['admin', 'responsable'])) return true;
        
        // El empleado solo edita si está asignado a la tarea
        return $task->users()->where('users.id', $user->id)->exists();
    }

    public function render()
    {
        return view('livewire.calendar');
    }
}