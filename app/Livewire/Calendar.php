<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use Carbon\Carbon;

class Calendar extends Component
{
    public int $year;
    public int $month;

    public function mount(): void
    {
        $now         = Carbon::now();
        $this->year  = $now->year;
        $this->month = $now->month;
    }

    // ── Navegación ────────────────────────────────────────────────────────────

    public function previousMonth(): void
    {
        $date        = Carbon::createFromDate($this->year, $this->month, 1)->subMonth();
        $this->year  = $date->year;
        $this->month = $date->month;
    }

    public function nextMonth(): void
    {
        $date        = Carbon::createFromDate($this->year, $this->month, 1)->addMonth();
        $this->year  = $date->year;
        $this->month = $date->month;
    }

    public function goToToday(): void
    {
        $now         = Carbon::now();
        $this->year  = $now->year;
        $this->month = $now->month;
    }

    // ── Carga de datos ────────────────────────────────────────────────────────

    /**
     * Carga y agrupa las tareas del mes visible.
     * Se llama desde render() para que siempre refleje el estado actual
     * de $year y $month, evitando problemas de serialización de Livewire
     * con arrays asociativos de clave string como propiedad pública.
     */
    private function loadTasks(): array
    {
        $tenantId  = auth()->user()->tenant_id;
        $startDate = Carbon::createFromDate($this->year, $this->month, 1)->startOfMonth();
        $endDate   = $startDate->copy()->endOfMonth();

        $tasks = Task::query()
            ->join('projects', 'projects.id', '=', 'tasks.project_id')
            ->where('projects.tenant_id', $tenantId)
            ->whereNull('projects.deleted_at')
            ->whereNull('tasks.deleted_at')
            ->whereBetween('tasks.due_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->select('tasks.id', 'tasks.title', 'tasks.due_date', 'tasks.status', 'tasks.priority')
            ->orderBy('tasks.priority', 'asc')
            ->get();

        $grouped = [];
        foreach ($tasks as $task) {
            $key             = Carbon::parse($task->due_date)->format('Y-m-d');
            $grouped[$key][] = [
                'id'       => $task->id,
                'title'    => $task->title,
                'status'   => $task->status,
                'priority' => $task->priority,
            ];
        }

        return $grouped;
    }

    // ── Helpers para la vista ─────────────────────────────────────────────────

    private function getCalendarWeeks(): array
    {
        $firstDay    = Carbon::createFromDate($this->year, $this->month, 1);
        $daysInMonth = $firstDay->daysInMonth;

        // Lunes = 0 … Domingo = 6
        $startWeekday = ($firstDay->dayOfWeek + 6) % 7;

        $weeks = [];
        $week  = array_fill(0, $startWeekday, null);

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $week[] = $day;
            if (count($week) === 7) {
                $weeks[] = $week;
                $week    = [];
            }
        }

        if (!empty($week)) {
            while (count($week) < 7) {
                $week[] = null;
            }
            $weeks[] = $week;
        }

        return $weeks;
    }

    private function getMonthName(): string
    {
        return Carbon::createFromDate($this->year, $this->month, 1)
            ->locale('es')
            ->isoFormat('MMMM');
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.calendar', [
            'weeks'        => $this->getCalendarWeeks(),
            'monthName'    => $this->getMonthName(),
            'tasksByDay'   => $this->loadTasks(),
            'today'        => Carbon::now()->format('Y-m-d'),
            'currentYear'  => $this->year,
            'currentMonth' => $this->month,
        ]);
    }
}