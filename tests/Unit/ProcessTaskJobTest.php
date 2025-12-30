<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Task;
use App\Jobs\ProcessTaskJob;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProcessTaskJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_marks_task_as_processed()
    {
        // Arrange: crear una tarea sin procesar
        $task = Task::factory()->create([
            'processed_at' => null,
        ]);

        // Act: ejecutar el Job directamente
        $job = new ProcessTaskJob($task);
        $job->handle();

        // Assert: la tarea queda marcada como procesada
        $this->assertNotNull(
            $task->fresh()->processed_at,
            'The task was not marked as processed'
        );
    }
}
