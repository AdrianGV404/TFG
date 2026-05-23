<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;

class TaskSeeder extends Seeder
{
    private array $titles = [
        'Diseñar mockup de pantalla de login',
        'Implementar autenticación con Laravel Sanctum',
        'Crear migración para tabla de notificaciones',
        'Revisar y corregir errores de validación del formulario',
        'Optimizar consultas SQL del panel de proyectos',
        'Añadir paginación a la vista de tareas',
        'Configurar entorno Docker para producción',
        'Escribir tests unitarios del módulo de usuarios',
        'Integrar Chart.js en el dashboard',
        'Implementar sistema de permisos por rol',
        'Refactorizar controlador de proyectos',
        'Crear endpoint REST para exportar tareas',
        'Revisar flujo de soft delete en proyectos',
        'Diseñar diagrama de clases del sistema',
        'Añadir filtro por prioridad en la lista de tareas',
        'Corregir bug en el cálculo de tiempo acumulado',
        'Actualizar dependencias de Composer',
        'Implementar notificaciones por email',
        'Rediseñar sidebar para modo oscuro',
        'Crear seeders de datos de prueba',
        'Revisar política de protección de datos RGPD',
        'Añadir campo deadline a la tabla de tareas',
        'Implementar drag and drop en el calendario',
        'Configurar caché de consultas frecuentes',
        'Documentar API en formato OpenAPI',
        'Crear componente Livewire para gestión de etiquetas',
        'Validar formulario de registro de empresa',
        'Añadir avatar de usuario en el perfil',
        'Implementar búsqueda en tiempo real de tareas',
        'Revisar responsive del layout principal',
        'Configurar GitHub Actions para CI/CD',
        'Crear vista de estadísticas por proyecto',
        'Añadir soporte para adjuntos en tareas',
        'Implementar exportación a CSV del dashboard',
        'Revisar seguridad del sistema multi-tenant',
        'Optimizar carga inicial del dashboard',
        'Implementar sistema de subtareas',
        'Añadir descripción enriquecida en tareas',
        'Corregir error de CORS en la API',
        'Diseñar flujo de onboarding para nuevos usuarios',
    ];

    private array $descriptions = [
        'Revisar los requisitos y aplicar los cambios según el documento de especificaciones.',
        'Tener en cuenta los casos de uso definidos y asegurarse de que la implementación cubre todos los escenarios.',
        'Coordinar con el equipo antes de empezar para alinear criterios de aceptación.',
        'Incluir tests de regresión para evitar que futuros cambios rompan esta funcionalidad.',
        'Documentar los cambios en el README y actualizar el historial de versiones.',
        'Prioridad alta: afecta a otros módulos en desarrollo.',
        'Pendiente de revisión por parte del responsable antes del merge.',
        'Verificar que funciona correctamente en modo claro y modo oscuro.',
        null,
        null,
    ];

    public function run(): void
    {
        // Carga proyectos con sus usuarios asignados (project_user pivot)
        $projects = Project::with('users')->whereNull('deleted_at')->get();

        if ($projects->isEmpty()) {
            $this->command->warn('No hay proyectos. Ejecuta primero ProjectSeeder.');
            return;
        }

        // Todos los usuarios agrupados por tenant para el fallback
        $allUsersByTenant = User::all()->groupBy('tenant_id');

        foreach ($projects as $project) {

            // Usuarios asignados al proyecto; si no hay, usa todos los del tenant
            $users = $project->users->isNotEmpty()
                ? $project->users
                : ($allUsersByTenant->get($project->tenant_id) ?? collect());

            if ($users->isEmpty()) {
                $this->command->warn("Sin usuarios para proyecto [{$project->name}], saltando.");
                continue;
            }

            $taskCount = rand(20, 50);
            $this->command->info("Proyecto [{$project->name}]: creando {$taskCount} tareas...");

            for ($i = 0; $i < $taskCount; $i++) {
                $status    = $this->weightedStatus();
                $createdAt = now()->subDays(rand(5, 60));

                // ── Crear tarea ──────────────────────────────────────────────
                // Nota: 'tenant_id' NO está en $fillable ni en la migración de tasks
                $task = Task::create([
                    'project_id'  => $project->id,
                    'title'       => $this->titles[array_rand($this->titles)],
                    'description' => $this->descriptions[array_rand($this->descriptions)],
                    'status'      => $status,
                    'priority'    => rand(0, 10),
                    'created_at'  => $createdAt,
                    'updated_at'  => $createdAt->copy()->addDays(rand(0, 5)),
                ]);

                // ── Time entries ─────────────────────────────────────────────
                // done → siempre tiene; in_progress → casi siempre; pending → raramente
                $shouldHaveEntries = match ($status) {
                    'done'        => true,
                    'in_progress' => (bool) rand(0, 1),
                    default       => rand(0, 4) === 0,
                };

                if ($shouldHaveEntries) {
                    $entryCount = rand(1, 5);

                    for ($j = 0; $j < $entryCount; $j++) {
                        $worker = $users->random();

                        $startedAt = now()
                            ->subDays(rand(0, 30))
                            ->setTime(rand(8, 16), rand(0, 59), 0);

                        // Bloques de 15 min, entre 15 min y 4 horas
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
                    }
                }
            }
        }

        $this->command->info('TaskSeeder completado correctamente.');
    }

    // 30% pendiente / 30% en progreso / 40% hecha
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