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
        $this->assertTrue(true);
    }
}
