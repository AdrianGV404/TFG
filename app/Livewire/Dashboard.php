<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TaskTimeEntry;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    // ─── Filtros ────────────────────────────────────────────────────────────
    public string $range    = 'week';
    public ?int   $userId   = null;
    public ?int   $projectId = null;

    // ─── Listas para selects ────────────────────────────────────────────────
    public array $users    = [];
    public array $projects = [];

    // ─── KPIs ───────────────────────────────────────────────────────────────
    public float $totalHours      = 0;
    public float $avgDailyHours   = 0;
    public int   $completedTasks  = 0;
    public int   $inProgressTasks = 0;
    public int   $pendingTasks    = 0;

    // ─── Datos para gráficas ────────────────────────────────────────────────
    public array $chartLabels    = [];
    public array $chartData      = [];
    public array $taskStatusData = [];

    // ─── Heatmap (array de ['date','hours','level']) ─────────────────────────
    public array $heatmapData = [];

    // ─── Listas ─────────────────────────────────────────────────────────────
    public $recentTasks    = [];
    public array $userBreakdown = [];

    // ────────────────────────────────────────────────────────────────────────

    public function mount(): void
    {
        $user = auth()->user();

        if (in_array($user->role, ['admin', 'responsable'])) {
            $this->users = User::select('id', 'name')
                ->when(
                    isset($user->tenant_id),
                    fn ($q) => $q->where('tenant_id', $user->tenant_id)
                )
                ->orderBy('name')
                ->get()
                ->toArray();
        } else {
            // Empleado: fuerza su propio ID, no ve otros usuarios
            $this->userId = $user->id;
        }

        $this->projects = Project::select('id', 'name')
            ->when(
                isset($user->tenant_id),
                fn ($q) => $q->where('tenant_id', $user->tenant_id)
            )
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get()
            ->toArray();

        $this->loadData();
    }

    public function updated(string $propertyName): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        [$startDate, $endDate] = $this->dateRange();

        // ── 1. Time entries → chart + heatmap ───────────────────────────────
        $timeEntries = TaskTimeEntry::query()
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->when($this->userId,    fn ($q) => $q->where('user_id', $this->userId))
            ->when($this->projectId, fn ($q) => $q->whereHas(
                'task', fn ($t) => $t->where('project_id', $this->projectId)
            ))
            ->selectRaw('DATE(created_at) as date, SUM(duration_seconds) as seconds')
            ->groupBy('date')
            ->pluck('seconds', 'date');

        $this->chartLabels  = [];
        $this->chartData    = [];
        $this->heatmapData  = [];
        $this->totalHours   = 0;

        foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
            $key     = $date->format('Y-m-d');
            $seconds = $timeEntries->get($key, 0);
            $hours   = round($seconds / 3600, 2);

            $this->chartLabels[] = $date->format('d M');
            $this->chartData[]   = $hours;
            $this->totalHours   += $hours;

            // Level 0-4 para el heatmap (escala visual)
            $level = match (true) {
                $hours <= 0  => 0,
                $hours < 2   => 1,
                $hours < 4   => 2,
                $hours < 6   => 3,
                default      => 4,
            };

            $this->heatmapData[] = [
                'date'  => $date->format('d M'),
                'hours' => $hours,
                'level' => $level,
            ];
        }

        $days = max(1, count($this->chartData));
        $this->avgDailyHours = round($this->totalHours / $days, 1);

        // ── 2. Estado de tareas ──────────────────────────────────────────────
        $statuses = Task::query()
            ->when($this->userId,    fn ($q) => $q->whereHas(
                'users', fn ($u) => $u->where('users.id', $this->userId)
            ))
            ->when($this->projectId, fn ($q) => $q->where('project_id', $this->projectId))
            ->whereNull('deleted_at')
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $this->completedTasks  = $statuses['done']        ?? 0;
        $this->inProgressTasks = $statuses['in_progress'] ?? 0;
        $this->pendingTasks    = $statuses['pending']      ?? 0;

        $this->taskStatusData = [
            'Pendientes'  => $this->pendingTasks,
            'En progreso' => $this->inProgressTasks,
            'Completadas' => $this->completedTasks,
        ];

        // ── 3. Actividad reciente ────────────────────────────────────────────
        $this->recentTasks = Task::query()
            ->with('project')
            ->when($this->userId,    fn ($q) => $q->whereHas(
                'users', fn ($u) => $u->where('users.id', $this->userId)
            ))
            ->when($this->projectId, fn ($q) => $q->where('project_id', $this->projectId))
            ->whereNull('deleted_at')
            ->latest('updated_at')
            ->take(8)
            ->get();

        // ── 4. Desglose por usuario (solo admin/responsable) ─────────────────
        $user = auth()->user();
        $this->userBreakdown = [];

        if (in_array($user->role, ['admin', 'responsable'])) {
            $rows = DB::table('task_time_entries as tte')
                ->join('users', 'users.id', '=', 'tte.user_id')
                ->whereBetween('tte.created_at', [$startDate, $endDate])
                ->when($this->projectId, function ($q) {
                    $q->join('tasks', 'tasks.id', '=', 'tte.task_id')
                      ->where('tasks.project_id', $this->projectId);
                })
                ->selectRaw('users.id, users.name, SUM(tte.duration_seconds) as seconds')
                ->groupBy('users.id', 'users.name')
                ->orderByDesc('seconds')
                ->take(5)
                ->get();

            $totalSeconds = $rows->sum('seconds') ?: 1;

            $this->userBreakdown = $rows->map(fn ($r) => [
                'name'    => $r->name,
                'hours'   => round($r->seconds / 3600, 1),
                'percent' => (int) round($r->seconds / $totalSeconds * 100),
            ])->toArray();
        }
    }

    // ────────────────────────────────────────────────────────────────────────

    private function dateRange(): array
    {
        return match ($this->range) {
            'month' => [now()->subDays(29)->startOfDay(), now()->endOfDay()],
            default => [now()->subDays(6)->startOfDay(),  now()->endOfDay()],
        };
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}