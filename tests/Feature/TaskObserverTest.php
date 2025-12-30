<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskObserverTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_logs_when_task_status_changes_to_done()
    {
        Log::spy();

        $task = Task::factory()->create([
            'status' => 'pending',
            'title' => 'Test task',
        ]);

        $task->update([
            'status' => 'done',
        ]);

        Log::shouldHaveReceived('info')
            ->once()
            ->with(
                'Task completed',
                [
                    'task_id' => $task->id,
                    'title' => $task->title,
                    'status' => 'done',
                ]
            );
    }

    /** @test */
    public function it_does_not_log_if_status_does_not_change_to_done()
    {
        Log::spy();

        $task = Task::factory()->create([
            'status' => 'pending',
        ]);

        $task->update([
            'status' => 'in_progress',
        ]);

        Log::shouldNotHaveReceived('info');
    }
}
