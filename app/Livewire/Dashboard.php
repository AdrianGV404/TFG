<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TaskTimeEntry;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public string $range = 'week'; // week | month
    public ?int $userId = null;
    public ?int $projectId = null;

    public function setRange($range)
    {
        $this->range = $range;
    }

    public function getDateRange()
    {
        return match ($this->range) {
            'month' => now()->subMonth(),
            default => now()->subWeek(),
        };
    }

    public function getTimeData()
    {
        return TaskTimeEntry::query()
            ->where('created_at', '>=', $this->getDateRange())
            ->when($this->userId, fn($q) => $q->where('user_id', $this->userId))
            ->selectRaw('DATE(created_at) as date, SUM(duration_seconds) as seconds')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public function getStatusData()
    {
        return Task::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();
    }

    public function getRecentTasks()
    {
        return Task::query()
            ->latest('updated_at')
            ->take(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'timeData' => $this->getTimeData(),
            'statusData' => $this->getStatusData(),
            'recentTasks' => $this->getRecentTasks(),
        ]);
    }
}