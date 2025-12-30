<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use App\Mail\TaskCompletedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskCompletedSendsEmailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_sends_an_email_when_task_is_marked_as_done()
    {
        Mail::fake();

        $task = Task::factory()->create([
            'status' => 'pending',
        ]);

        // Act: cambiar estado a done
        $task->update([
            'status' => 'done',
        ]);

        // Assert: se envía el email
        Mail::assertSent(TaskCompletedMail::class, function ($mail) use ($task) {
            return $mail->task->id === $task->id;
        });
    }
}
