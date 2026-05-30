<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TaskTimeEntry;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use App\Models\HistoricoHorasDia;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    // ─── Filtros ────────────────────────────────────────────────────────────
    public ?int $userId    = null;
    public ?int $projectId = null;

    // ─── Listas para selects ────────────────────────────────────────────────
    public array $users    = [];
    public array $projects = [];

    // ─── KPIs ───────────────────────────────────────────────────────────────
    public float $avgHoursPerTask = 0;

    // ─── Datos para gráficas / listas ───────────────────────────────────────
    public array  $taskStatusData  = [];   // para el piechart
    public array  $taskTimeList    = [];   // lista id+titulo+horas
    public array  $heatmapData     = [];   // para el heatmap anual
    public array  $heatmapTooltips = [];   // desglose por día (tooltip)
    public $recentTasks            = [];   // últimas 15 modificadas

    // ─── Permisos de UI ─────────────────────────────────────────────────────
    public bool $isAdmin = false;

    // ────────────────────────────────────────────────────────────────────────

    public function mount(): void
    {
        $user           = auth()->user();
        $this->isAdmin  = in_array($user->role, ['admin', 'responsable']);

        // Proyectos del tenant (todos los roles)
        $this->projects = Project::select('id', 'name')
            ->where('tenant_id', $user->tenant_id)
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get()
            ->toArray();

        if ($this->isAdmin) {
            // Admin: ve todos los usuarios del tenant
            $this->users = User::select('id', 'name')
                ->where('tenant_id', $user->tenant_id)
                ->orderBy('name')
                ->get()
                ->toArray();
        } else {
            // Empleado: forzar su propio userId, sin selector de usuario
            $this->userId = $user->id;
        }

        $this->loadData();
    }

    // ❌ El método updated() ha sido eliminado para evitar recargas automáticas conflictivas

    // ────────────────────────────────────────────────────────────────────────

    public function loadData(): void
    {
        $tenantId = auth()->user()->tenant_id;

        // ── 1. Lista de tareas con tiempo total ──────────────────────────────
        $this->taskTimeList = $this->buildTaskTimeList($tenantId);

        // ── 2. Promedio de horas por tarea ───────────────────────────────────
        $totalHoras = collect($this->taskTimeList)->sum('horas');
        $numTareas  = count($this->taskTimeList);
        $this->avgHoursPerTask = $numTareas > 0
            ? round($totalHoras / $numTareas, 1)
            : 0;

        // ── 3. Estados de tareas ─────────────────────────────────────────────
        $this->taskStatusData = $this->buildStatusData($tenantId);

        // ── 4. Heatmap anual desde historico_horas_dia ───────────────────────
        $this->heatmapData     = $this->buildHeatmap($tenantId);
        $this->heatmapTooltips = $this->buildHeatmapTooltips($tenantId);

        // ── 5. Tareas recientes (últimas 15 por updated_at) ──────────────────
        $this->recentTasks = $this->buildRecentTasks($tenantId);
    }

    // ─── Builders privados ──────────────────────────────────────────────────

    /**
     * Devuelve [ ['id', 'title', 'horas'], … ] de las tareas con tiempo registrado,
     * aplicando los filtros activos, ordenado por horas DESC.
     */
    private function buildTaskTimeList(int $tenantId): array
    {
        $query = DB::table('task_time_entries as tte')
            ->join('tasks', 'tasks.id', '=', 'tte.task_id')
            ->join('projects', 'projects.id', '=', 'tasks.project_id')
            ->where('projects.tenant_id', $tenantId)
            ->whereNull('projects.deleted_at')
            ->whereNull('tasks.deleted_at')
            ->where('tte.is_running', false)
            ->selectRaw('tasks.id, tasks.title, SUM(tte.duration_seconds) as seconds');

        if ($this->userId) {
            $query->where('tte.user_id', $this->userId);
        }

        if ($this->projectId) {
            $query->where('tasks.project_id', $this->projectId);
        }

        return $query
            ->groupBy('tasks.id', 'tasks.title')
            ->orderByDesc('seconds')
            ->get()
            ->map(fn ($r) => [
                'id'    => $r->id,
                'title' => $r->title,
                'horas' => round($r->seconds / 3600, 1),
            ])
            ->toArray();
    }

    /**
     * Devuelve conteos y porcentajes de tareas por estado.
     */
    private function buildStatusData(int $tenantId): array
    {
        $query = Task::query()
            ->join('projects', 'projects.id', '=', 'tasks.project_id')
            ->where('projects.tenant_id', $tenantId)
            ->whereNull('projects.deleted_at')
            ->whereNull('tasks.deleted_at');

        if ($this->userId) {
            $query->whereHas('timeEntries', fn ($q) => $q->where('user_id', $this->userId));
        }

        if ($this->projectId) {
            $query->where('tasks.project_id', $this->projectId);
        }

        $counts = $query
            ->select('tasks.status', DB::raw('count(*) as total'))
            ->groupBy('tasks.status')
            ->pluck('total', 'status')
            ->toArray();

        $total = array_sum($counts);

        $labels = [
            'pending'     => 'Pendiente',
            'in_progress' => 'En progreso',
            'on_hold'     => 'En pausa',
            'testing'     => 'En pruebas',
            'done'        => 'Completada',
        ];

        $colors = [
            'pending'     => '#f59e0b',
            'in_progress' => '#3b82f6',
            'on_hold'     => '#6b7280',
            'testing'     => '#8b5cf6',
            'done'        => '#22c55e',
        ];

        $result = [];
        foreach ($labels as $key => $label) {
            $n = $counts[$key] ?? 0;
            $result[] = [
                'key'     => $key,
                'label'   => $label,
                'count'   => $n,
                'percent' => $total > 0 ? round($n / $total * 100) : 0,
                'color'   => $colors[$key],
            ];
        }

        return $result;
    }

    /**
     * Construye el array de celdas del heatmap (desde hace ~1 año hasta hoy).
     * Usa historico_horas_dia para obtener las horas por día.
     *
     * @return array  [ 'date' => 'Y-m-d', 'horas' => float, 'level' => 0-3 ][]
     */
    private function buildHeatmap(int $tenantId): array
    {
        $end   = now()->toDateString();
        $start = now()->subYear()->addDay()->toDateString();

        $query = DB::table('historico_horas_dia')
            ->where('tenant_id', $tenantId)
            ->whereBetween('dia', [$start, $end]);

        if ($this->projectId) {
            $query->where('project_id', $this->projectId);
        }

        // Agrupar por día (sumando todos los proyectos si no hay filtro)
        $rows = $query
            ->selectRaw('dia, SUM(horas) as horas')
            ->groupBy('dia')
            ->pluck('horas', 'dia')
            ->map(fn ($h) => (float) $h);

        $cells = [];
        foreach (CarbonPeriod::create($start, $end) as $date) {
            $key   = $date->toDateString();
            $horas = (float) ($rows[$key] ?? 0);

            $level = match (true) {
                $horas <= 0 => 0,
                $horas < 3  => 1,
                $horas < 6  => 2,
                default     => 3,
            };

            $cells[] = [
                'date'  => $key,
                'horas' => round($horas, 1),
                'level' => $level,
            ];
        }

        return $cells;
    }

    /**
     * Para cada día con actividad construye el desglose que mostrará el tooltip:
     * lista de [ task_id, title, horas ] del día.
     * Solo se calculan los días que tienen horas > 0 para no sobrecargar.
     *
     * @return array  [ 'Y-m-d' => [ ['task_id', 'title', 'horas'], … ] ]
     */
    private function buildHeatmapTooltips(int $tenantId): array
    {
        $end   = now()->toDateString();
        $start = now()->subYear()->addDay()->toDateString();

        $query = DB::table('task_time_entries as tte')
            ->join('tasks', 'tasks.id', '=', 'tte.task_id')
            ->join('projects', 'projects.id', '=', 'tasks.project_id')
            ->where('projects.tenant_id', $tenantId)
            ->whereNull('tasks.deleted_at')
            ->whereNull('projects.deleted_at')
            ->where('tte.is_running', false)
            ->whereBetween(DB::raw('DATE(tte.started_at)'), [$start, $end]);

        if ($this->projectId) {
            $query->where('tasks.project_id', $this->projectId);
        }

        if ($this->userId) {
            $query->where('tte.user_id', $this->userId);
        }

        $rows = $query
            ->selectRaw('DATE(tte.started_at) as dia, tasks.id as task_id, tasks.title, SUM(tte.duration_seconds) as seconds')
            ->groupBy('dia', 'tasks.id', 'tasks.title')
            ->orderBy('dia')
            ->orderByDesc('seconds')
            ->get();

        $tooltips = [];
        foreach ($rows as $row) {
            $tooltips[$row->dia][] = [
                'task_id' => $row->task_id,
                'title'   => $row->title,
                'horas'   => round($row->seconds / 3600, 1),
            ];
        }

        return $tooltips;
    }

    /**
     * Últimas 15 tareas modificadas del tenant (con filtros aplicados).
     */
    private function buildRecentTasks(int $tenantId)
    {
        $query = Task::query()
            ->join('projects', 'projects.id', '=', 'tasks.project_id')
            ->where('projects.tenant_id', $tenantId)
            ->whereNull('projects.deleted_at')
            ->whereNull('tasks.deleted_at')
            ->select('tasks.*');

        if ($this->userId) {
            $query->whereHas('timeEntries', fn ($q) => $q->where('user_id', $this->userId));
        }

        if ($this->projectId) {
            $query->where('tasks.project_id', $this->projectId);
        }

        return $query
            ->with('project')
            ->orderByDesc('tasks.updated_at')
            ->limit(15)
            ->get();
    }

    /**
     * Se ejecuta al pulsar el botón "Aplicar filtros".
     * Recarga los datos y emite un evento al navegador para que JS redibuje los gráficos.
     */
    public function applyFilters(): void
    {
        $this->loadData();
        $this->dispatchBrowserEvent('filters-applied');
    }

    // ────────────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.dashboard');
    }
}