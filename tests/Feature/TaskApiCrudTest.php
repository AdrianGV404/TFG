<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskApiCrudTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_lists_tasks()
    {
        Task::factory()->count(2)->create();

        $response = $this->getJson('/api/tasks');

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function it_creates_a_task()
    {
        $project = \App\Models\Project::factory()->create();

        $response = $this->postJson('/api/tasks', [
            'project_id' => $project->id,
            'title' => 'API Task',
            'description' => 'Created via API',
            'status' => 'pending',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.title', 'API Task')
            ->assertJsonPath('data.project_id', $project->id);

        $this->assertDatabaseHas('tasks', [
            'project_id' => $project->id,
            'title' => 'API Task',
        ]);
    }


    /** @test */
    public function it_shows_a_task()
    {
        $task = Task::factory()->create();

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $task->id);
    }

    /** @test */
    public function it_updates_a_task()
    {
        $task = Task::factory()->create([
            'status' => 'pending',
        ]);

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'title' => 'Updated via API',
            'status' => 'in_progress',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.title', 'Updated via API')
            ->assertJsonPath('data.status', 'in_progress');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'in_progress',
        ]);
    }

    /** @test */
    public function it_deletes_a_task()
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    /** @test */
    public function it_returns_404_when_task_not_found()
    {
        $response = $this->getJson('/api/tasks/999');

        $response->assertNotFound();
    }

    /** @test */
    public function it_validates_task_creation()
    {
        $response = $this->postJson('/api/tasks', []);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'status']);
    }
}
