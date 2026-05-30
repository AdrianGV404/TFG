<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Task;
use App\Models\Project;
use App\Models\HistoricoHorasDia;
use Faker\Factory as Faker;
use Carbon\Carbon;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $faker    = Faker::create('es_ES');
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

                // Número de entradas de tiempo según estado:
                //   done        → 2–5 entradas (ya terminó, tuvo bastante actividad)
                //   in_progress → 1–3 entradas (activa ahora)
                //   on_hold     → 1–2 entradas (estuvo activa, ahora pausada)
                //   testing     → 1–2 entradas (también tuvo actividad previa)
                //   pending     → 0   entradas (aún no se ha empezado)
                $numEntries = match ($status) {
                    'done'        => rand(2, 5),
                    'in_progress' => rand(1, 3),
                    'on_hold'     => rand(1, 2),
                    'testing'     => rand(1, 2),
                    default       => 0,              // pending
                };

                for ($e = 0; $e < $numEntries; $e++) {
                    if ($assignedUsers->isEmpty()) continue;

                    $worker = $assignedUsers->random();

                    // Día aleatorio entre hoy y hace 60 días, en horario laboral
                    $randomDays      = rand(0, 60);
                    $startedAt       = now()->subDays($randomDays)
                                           ->setTime(rand(8, 16), rand(0, 59), 0);
                    // Bloques de 15 min (1–16 bloques = 15 min – 4 h)
                    $durationSeconds = rand(1, 16) * 900;
                    $endedAt         = $startedAt->copy()->addSeconds($durationSeconds);

                    DB::table('task_time_entries')->insert([
                        'task_id'          => $task->id,
                        'user_id'          => $worker->id,
                        'started_at'       => $startedAt,
                        'ended_at'         => $endedAt,
                        'duration_seconds' => $durationSeconds,
                        'is_running'       => false,
                        'created_at'       => $startedAt,
                        'updated_at'       => $endedAt,
                    ]);

                    // ── Acumular en historico_horas_dia ───────────────────────
                    // Usa el mismo método del modelo que usa la app en producción,
                    // así los datos del seeder son coherentes con los reales.
                    HistoricoHorasDia::acumular(
                        tenantId:  $project->tenant_id,
                        projectId: $project->id,
                        dia:       $startedAt->toDateString(),
                        segundos:  $durationSeconds
                    );
                }
            }
        }
    }

    /**
     * Distribución de estados con los 5 valores actuales:
     *   pending     30 %
     *   in_progress 25 %
     *   on_hold     15 %  ← nuevo
     *   testing     10 %  ← nuevo
     *   done        20 %
     */
    private function weightedStatus(): string
    {
        $rand = rand(1, 100);

        return match (true) {
            $rand <= 30 => 'pending',
            $rand <= 55 => 'in_progress',
            $rand <= 70 => 'on_hold',
            $rand <= 80 => 'testing',
            default     => 'done',
        };
    }
}