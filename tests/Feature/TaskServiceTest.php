<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use App\Services\TaskService;
use App\Events\TaskCompleted;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_it_updates_a_task()
    {
        $task = Task::factory()->create([
            'status' => 'pending',
        ]);

        $service = new TaskService();

        $updatedTask = $service->update($task, [
            'status' => 'done',
        ]);

        $this->assertEquals('done', $updatedTask->status);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'done',
        ]);
    }
    
    /** @test */
    public function test_it_does_not_dispatch_event_if_status_is_not_done()
    {
        Event::fake();

        $task = Task::factory()->create([
            'status' => 'pending',
        ]);

        $service = new TaskService();

        $service->update($task, [
            'title' => 'Updated title',
        ]);

        Event::assertNotDispatched(TaskCompleted::class);
    }
}
