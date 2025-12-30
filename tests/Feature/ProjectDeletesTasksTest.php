<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectDeletesTasksTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function deleting_a_project_also_deletes_its_tasks()
    {
        $project = Project::factory()->create();

        $tasks = Task::factory()->count(3)->create([
            'project_id' => $project->id,
        ]);

        $this->assertDatabaseCount('tasks', 3);

        $this->delete(route('projects.destroy', $project));

        $this->assertDatabaseMissing('projects', [
            'id' => $project->id,
        ]);

        foreach ($tasks as $task) {
            $this->assertDatabaseMissing('tasks', [
                'id' => $task->id,
            ]);
        }
    }
}
