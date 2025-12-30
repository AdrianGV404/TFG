<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskCrudTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_task_for_a_project()
    {
        $project = Project::factory()->create();

        $response = $this->post(
            route('projects.tasks.store', $project),
            [
                'title' => 'Nueva tarea',
                'description' => 'Descripción de la tarea',
                'status' => 'pending',
            ]
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('tasks', [
            'project_id' => $project->id,
            'title' => 'Nueva tarea',
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function it_updates_a_task()
    {
        $project = Project::factory()->create();
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'status' => 'pending',
        ]);

        $response = $this->put(
            route('projects.tasks.update', [$project, $task]),
            [
                'title' => 'Tarea actualizada',
                'description' => 'Nueva descripción',
                'status' => 'in_progress',
            ]
        );

        $response->assertRedirect(
            route('projects.show', $project)
        );

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Tarea actualizada',
            'status' => 'in_progress',
        ]);
    }

    /** @test */
    public function it_deletes_a_task()
    {
        $project = Project::factory()->create();
        $task = Task::factory()->create([
            'project_id' => $project->id,
        ]);

        $response = $this->delete(
            route('projects.tasks.destroy', [$project, $task])
        );

        $response->assertRedirect();

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }
}
