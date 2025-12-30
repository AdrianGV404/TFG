<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_task_status_via_api()
    {
        $task = Task::factory()->create([
            'status' => 'pending',
        ]);

        $response = $this->patchJson("/api/tasks/{$task->id}", [
            'status' => 'done',
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'status' => 'done',
                 ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'done',
        ]);
    }
}
