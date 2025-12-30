<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;

class TaskCard extends Component
{
    public Task $task;

    public string $status;

    public function mount()
    {
        $this->status = $this->task->status;
    }

    public function updatedStatus()
    {
        $this->task->update([
            'status' => $this->status,
        ]);

        $this->dispatch('taskUpdated');
    }

    public function delete()
    {
        $this->task->delete();
        $this->dispatch('taskUpdated');
    }

    public function render()
    {
        return view('livewire.task-card');
    }
}
