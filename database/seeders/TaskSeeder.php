<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Task;
use App\Models\Project;
use App\Models\Label;
use App\Models\HistoricoHorasDia;
use Faker\Factory as Faker;
use Carbon\Carbon;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $faker    = Faker::create('es_ES');
        $projects = Project::with('users')->get();

        // 1. GENERAR 20 ETIQUETAS POR TENANT
        // Agrupamos los tenants únicos de los proyectos existentes
        $tenantIds = $projects->pluck('tenant_id')->unique();
        $tenantLabels = []; // Guardará los IDs de las etiquetas creadas por cada tenant

        // Nombres de etiquetas realistas para evitar el límite de palabras únicas del faker
        $labelNames = [
            'Urgente', 'Frontend', 'Backend', 'Bug', 'Diseño', 'Mejora', 'QA',
            'Documentación', 'Despliegue', 'Revisión', 'Optimización', 'Soporte',
            'Cliente', 'Interno', 'Bloqueado', 'Ventas', 'Marketing', 'Prioridad Alta',
            'Legal', 'Infraestructura', 'Seguridad', 'Base de datos', 'API'
        ];

        foreach ($tenantIds as $tenantId) {
            $labelsIds = [];
            // Seleccionamos 20 nombres aleatorios sin repetir de nuestra lista
            $selectedNames = $faker->randomElements($labelNames, 20);
            
            foreach ($selectedNames as $name) {
                $label = Label::firstOrCreate([
                    'tenant_id' => $tenantId,
                    'name'      => $name
                ]);
                $labelsIds[] = $label->id;
            }
            // Guardamos los IDs disponibles para este tenant
            $tenantLabels[$tenantId] = $labelsIds;
        }

        foreach ($projects as $project) {
            $assignedUsers = $project->users;
            
            // PRECAUCIÓN: Si el proyecto no tiene usuarios asignados, saltamos al siguiente
            // Esto asegura que "created_by" nunca intente ser nulo.
            if ($assignedUsers->isEmpty()) {
                continue;
            }
            
            // Obtenemos las etiquetas disponibles para el tenant de este proyecto
            $projectLabelsPool = $tenantLabels[$project->tenant_id] ?? [];

            for ($i = 0; $i < 50; $i++) {
                $status = $this->weightedStatus();

                // 2. FECHAS DE CREACIÓN Y CADUCIDAD
                // Creación: un día aleatorio desde hace 1 año (0 a 365 días atrás)
                $createdAt = now()->subDays(rand(0, 365));
                
                // Caducidad: un día aleatorio desde mañana hasta 1 año en el futuro
                $dueDate = now()->addDays(rand(1, 365));

                // 3. SELECCIONAR CREADOR DE LA TAREA
                // Seleccionamos aleatoriamente uno de los usuarios que ya están asignados a este proyecto
                $taskCreator = $assignedUsers->random();

                $task = Task::create([
                    'project_id'  => $project->id,
                    'created_by'  => $taskCreator->id,
                    'title'       => ucfirst($faker->words(rand(3, 6), true)),
                    'description' => $faker->paragraph(2),
                    'status'      => $status,
                    'priority'    => rand(0, 10),
                    'due_date'    => $dueDate,
                    'created_at'  => $createdAt,
                    'updated_at'  => $createdAt->copy()->addDays(rand(1, 10)),
                ]);

                // 4. ASIGNAR ENTRE 0 y 3 ETIQUETAS
                if (!empty($projectLabelsPool)) {
                    $numLabelsToAttach = rand(0, 3);
                    if ($numLabelsToAttach > 0) {
                        // Selecciona IDs aleatorios sin repetir
                        $randomLabelIds = $faker->randomElements($projectLabelsPool, $numLabelsToAttach);
                        // Asegúrate de tener la relación public function labels() en tu modelo Task
                        $task->labels()->attach($randomLabelIds);
                    }
                }

                // Número de entradas de tiempo según estado:
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

                    // Ajuste: El día aleatorio de la imputación debe ser estrictamente
                    // posterior a la creación de la tarea y como máximo hoy.
                    $maxDaysSinceCreation = max(0, $createdAt->diffInDays(now()));
                    $randomDaysAdd   = rand(0, $maxDaysSinceCreation);
                    
                    $startedAt       = $createdAt->copy()->addDays($randomDaysAdd)
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
                    HistoricoHorasDia::acumular(
                        tenantId:  $project->tenant_id,
                        projectId: $project->id,
                        dia:       $startedAt->toDateString(),
                        segundos:  $durationSeconds
                    );
                }
            }

            // ── 30 tareas extra con vencimiento en el mes actual ──────────
            for ($i = 0; $i < 30; $i++) {
                $status = $this->weightedStatus();

                $createdAt = now()->subDays(rand(0, 365));

                // Due date: día aleatorio dentro del mes en curso
                $dueDate = Carbon::create(now()->year, now()->month, rand(1, now()->daysInMonth));

                $taskCreator = $assignedUsers->random();

                $task = Task::create([
                    'project_id'  => $project->id,
                    'created_by'  => $taskCreator->id,
                    'title'       => ucfirst($faker->words(rand(3, 6), true)),
                    'description' => $faker->paragraph(2),
                    'status'      => $status,
                    'priority'    => rand(0, 10),
                    'due_date'    => $dueDate,
                    'created_at'  => $createdAt,
                    'updated_at'  => $createdAt->copy()->addDays(rand(1, 10)),
                ]);

                if (!empty($projectLabelsPool)) {
                    $numLabelsToAttach = rand(0, 3);
                    if ($numLabelsToAttach > 0) {
                        $randomLabelIds = $faker->randomElements($projectLabelsPool, $numLabelsToAttach);
                        $task->labels()->attach($randomLabelIds);
                    }
                }

                $numEntries = match ($status) {
                    'done'        => rand(2, 5),
                    'in_progress' => rand(1, 3),
                    'on_hold'     => rand(1, 2),
                    'testing'     => rand(1, 2),
                    default       => 0,
                };

                for ($e = 0; $e < $numEntries; $e++) {
                    if ($assignedUsers->isEmpty()) continue;

                    $worker = $assignedUsers->random();

                    $maxDaysSinceCreation = max(0, $createdAt->diffInDays(now()));
                    $randomDaysAdd        = rand(0, $maxDaysSinceCreation);

                    $startedAt       = $createdAt->copy()->addDays($randomDaysAdd)
                                                 ->setTime(rand(8, 16), rand(0, 59), 0);
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

                    HistoricoHorasDia::acumular(
                        tenantId:  $project->tenant_id,
                        projectId: $project->id,
                        dia:       $startedAt->toDateString(),
                        segundos:  $durationSeconds
                    );
                }
            }
            // ─────────────────────────────────────────────────────────────
        }
    }

    /**
     * Distribución de estados con los 5 valores actuales.
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