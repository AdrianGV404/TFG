<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Task;
use App\Models\Project;
use Faker\Factory as Faker;
use Carbon\Carbon;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_ES');
        $projects = Project::with('users')->get();

        foreach ($projects as $project) {
            $assignedUsers = $project->users;

            for ($i = 0; $i < 50; $i++) {
                $status = $this->weightedStatus();

                // Fecha de creación de la tarea (hace 1 a 3 meses)
                $createdAt = now()->subDays(rand(30, 90));

                $task = Task::create([
                    'project_id'  => $project->id,
                    'title'       => ucfirst($faker->words(rand(3, 6), true)),
                    'description' => $faker->paragraph(2),
                    'status'      => $status,
                    'priority'    => rand(0, 10),
                    'due_date'    => $createdAt->copy()->addDays(rand(10, 40)),
                    'created_at'  => $createdAt,
                    'updated_at'  => $createdAt->copy()->addDays(rand(1, 10)),
                ]);

                // Generar de 0 a 3 entradas de tiempo por tarea, dependiendo del estado
                $numEntries = ($status === 'done') ? rand(1, 4) : (($status === 'in_progress') ? rand(1, 2) : 0);

                for ($e = 0; $e < $numEntries; $e++) {
                    if ($assignedUsers->isEmpty()) continue;

                    $worker = $assignedUsers->random();

                    // --- Lógica de tiempos aleatorios ---
                    // Elegir un día aleatorio entre hoy y hace 60 días
                    $randomDays = rand(0, 60);
                    // Empezar en horario laboral (08:00 a 17:00)
                    $startedAt = now()->subDays($randomDays)->setTime(rand(8, 16), rand(0, 59), 0);
                    
                    // Duración: bloques de 15 min (1 a 16 bloques = 15min a 4 horas)
                    $durationSeconds = rand(1, 16) * 900; 
                    $endedAt = $startedAt->copy()->addSeconds($durationSeconds);

                    DB::table('task_time_entries')->insert([
                        'task_id'          => $task->id,
                        'user_id'          => $worker->id,
                        'started_at'       => $startedAt,
                        'ended_at'         => $endedAt,
                        'duration_seconds' => $durationSeconds,
                        'is_running'       => false,
                        'created_at'       => $startedAt, // El log se creó cuando empezó
                        'updated_at'       => $endedAt,
                    ]);
                }
            }
        }
    }

    private function weightedStatus(): string 
    {
        $rand = rand(1, 100);
        return match (true) {
            $rand <= 30 => 'pending',
            $rand <= 60 => 'in_progress',
            default     => 'done',
        };
    }
}