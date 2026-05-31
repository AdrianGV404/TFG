<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Task;

class TaskModelTest extends TestCase
{
    /** @test */
    public function it_returns_true_if_task_is_done()
    {
        $task = new Task([
            'status' => 'done',
        ]);

        $this->assertTrue($task->isDone());
    }

    /** @test */
    public function it_returns_false_if_task_is_not_done()
    {
        $task = new Task([
            'status' => 'pending',
        ]);

        $this->assertFalse($task->isDone());
    }
}
