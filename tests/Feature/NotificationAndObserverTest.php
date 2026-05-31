<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskTimeEntry;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserPermission;
use App\Models\UserSettings;
use App\Observers\TaskObserver;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * 7.1.3 Pruebas de rendimiento (básicas): N+1, índices, consultas
 * 7.1.1 Pruebas funcionales: notificaciones, observer de tareas
 */
class NotificationAndObserverTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helpers ────────────────────────────────────────────────────────────

    private function setupWorld(): array
    {
        $tenant = Tenant::create(['name' => 'Empresa Test', 'type' => 'empresa']);
        $admin  = User::create([
            'name'      => 'Admin',
            'email'     => 'admin@test.com',
            'password'  => Hash::make('password'),
            'tenant_id' => $tenant->id,
            'role'      => 'admin',
        ]);
        $worker = User::create([
            'name'      => 'Trabajador',
            'email'     => 'worker@test.com',
            'password'  => Hash::make('password'),
            'tenant_id' => $tenant->id,
            'role'      => 'user',
        ]);
        UserSettings::create([
            'user_id'             => $worker->id,
            'theme'               => 'light',
            'notif_tasks'         => true,
            'notif_alerts'        => true,
            'notif_reports'       => true,
            'notif_status_change' => true,
            'notif_channel'       => 'app',
        ]);
        UserPermission::create(['user_id' => $worker->id]);
        $project = Project::create([
            'name'       => 'Proyecto Test',
            'status'     => 'active',
            'tenant_id'  => $tenant->id,
            'created_by' => $admin->id,
        ]);
        $project->users()->attach([$admin->id, $worker->id]);

        return [$tenant, $admin, $worker, $project];
    }

    // ─── Tests: Observer de tareas ───────────────────────────────────────────

    /** @test */
    public function cambiar_tarea_a_done_dispara_notificaciones_a_otros_miembros(): void
    {
        [$tenant, $admin, $worker, $project] = $this->setupWorld();

        $task = Task::create([
            'project_id' => $project->id,
            'title'      => 'Tarea Observer',
            'status'     => 'pending',
            'priority'   => 5,
            'created_by' => $admin->id,
            'tenant_id'  => $tenant->id,
        ]);

        // Cambiar estado como admin (id del admin) → notifica al worker
        $this->actingAs($admin);
        $task->update(['status' => 'done']);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $worker->id,
            'type'    => Notification::TYPE_STATUS_CHANGE,
        ]);
    }

    /** @test */
    public function cambio_de_estado_no_se_notifica_al_propio_modificador(): void
    {
        [$tenant, $admin, $worker, $project] = $this->setupWorld();

        $task = Task::create([
            'project_id' => $project->id,
            'title'      => 'Tarea Observer 2',
            'status'     => 'pending',
            'priority'   => 5,
            'created_by' => $admin->id,
            'tenant_id'  => $tenant->id,
        ]);

        $this->actingAs($admin);
        $task->update(['status' => 'in_progress']);

        // El admin (quien cambió) NO debe recibir notificación
        $this->assertDatabaseMissing('notifications', [
            'user_id' => $admin->id,
            'type'    => Notification::TYPE_STATUS_CHANGE,
        ]);
    }

    // ─── Tests: NotificationService ─────────────────────────────────────────

    /** @test */
    public function notification_service_crea_notificacion_de_asignacion(): void
    {
        [$tenant, $admin, $worker, $project] = $this->setupWorld();

        NotificationService::notifyProjectAssignment($worker, 'Proyecto Nuevo');

        $this->assertDatabaseHas('notifications', [
            'user_id' => $worker->id,
            'type'    => Notification::TYPE_ASSIGNMENT,
        ]);
    }

    /** @test */
    public function notificacion_no_se_crea_si_el_usuario_la_desactiva(): void
    {
        [$tenant, $admin, $worker, $project] = $this->setupWorld();

        // Desactivar notificaciones de asignación para el worker
        UserSettings::where('user_id', $worker->id)->update(['notif_tasks' => false]);
        $worker->refresh()->load('settings');

        NotificationService::notifyProjectAssignment($worker, 'Proyecto Sin Notif');

        $this->assertDatabaseMissing('notifications', [
            'user_id' => $worker->id,
            'type'    => Notification::TYPE_ASSIGNMENT,
        ]);
    }

    /** @test */
    public function scope_unread_filtra_solo_no_leidas(): void
    {
        [$tenant, $admin, $worker, $project] = $this->setupWorld();

        Notification::create([
            'user_id' => $worker->id,
            'type'    => Notification::TYPE_ASSIGNMENT,
            'title'   => 'No leída',
            'body'    => 'Test',
            'read_at' => null,
        ]);
        Notification::create([
            'user_id' => $worker->id,
            'type'    => Notification::TYPE_ASSIGNMENT,
            'title'   => 'Leída',
            'body'    => 'Test',
            'read_at' => now(),
        ]);

        $unread = Notification::where('user_id', $worker->id)->unread()->count();

        $this->assertEquals(1, $unread);
    }

    /** @test */
    public function notificacion_is_read_devuelve_correcto(): void
    {
        [$tenant, $admin, $worker, $project] = $this->setupWorld();

        $unreadNotif = Notification::create([
            'user_id' => $worker->id,
            'type'    => Notification::TYPE_ASSIGNMENT,
            'title'   => 'Sin leer',
            'body'    => 'test',
            'read_at' => null,
        ]);
        $readNotif = Notification::create([
            'user_id' => $worker->id,
            'type'    => Notification::TYPE_ASSIGNMENT,
            'title'   => 'Leída',
            'body'    => 'test',
            'read_at' => now(),
        ]);

        $this->assertFalse($unreadNotif->isRead());
        $this->assertTrue($readNotif->isRead());
    }

    // ─── Tests: Rendimiento básico (N+1 check) ───────────────────────────────

    /** @test */
    public function cargar_proyectos_con_usuarios_no_genera_n_mas_uno_queries(): void
    {
        [$tenant, $admin, $worker, $project] = $this->setupWorld();

        // Crear más proyectos
        for ($i = 0; $i < 5; $i++) {
            $p = Project::create([
                'name'       => "Proyecto {$i}",
                'status'     => 'active',
                'tenant_id'  => $tenant->id,
                'created_by' => $admin->id,
            ]);
            $p->users()->attach($admin->id);
        }

        // Con eager loading debería ser 2 queries (projects + users)
        // Sin eager loading sería 1 + N queries
        \Illuminate\Support\Facades\DB::enableQueryLog();

        $projects = Project::where('tenant_id', $tenant->id)
            ->with('users')
            ->get();

        $queries = \Illuminate\Support\Facades\DB::getQueryLog();
        \Illuminate\Support\Facades\DB::disableQueryLog();

        // Máximo 3 queries: projects + users via pivot + posibles variantes
        $this->assertLessThanOrEqual(3, count($queries),
            'Demasiadas queries: posible problema N+1. ' . count($queries) . ' queries ejecutadas.');

        // Y los proyectos cargan bien
        $this->assertGreaterThan(0, $projects->count());
        foreach ($projects as $p) {
            $this->assertNotNull($p->users);
        }
    }

    /** @test */
    public function tiempo_de_respuesta_api_tasks_es_aceptable(): void
    {
        [$tenant, $admin, $worker, $project] = $this->setupWorld();

        // Crear 50 tareas
        for ($i = 0; $i < 50; $i++) {
            Task::create([
                'project_id' => $project->id,
                'title'      => "Tarea Rendimiento {$i}",
                'status'     => 'pending',
                'priority'   => 5,
                'created_by' => $admin->id,
                'tenant_id'  => $tenant->id,
            ]);
        }

        $startTime = microtime(true);
        $response  = $this->getJson('/api/tasks');
        $elapsed   = microtime(true) - $startTime;

        $response->assertOk();

        // La respuesta debe ser en menos de 2 segundos (test con SQLite en memoria)
        $this->assertLessThan(2.0, $elapsed,
            "La API tardó {$elapsed}s en responder con 50 tareas.");
    }

    /** @test */
    public function calcular_horas_totales_de_tarea_funciona_correctamente(): void
    {
        [$tenant, $admin, $worker, $project] = $this->setupWorld();

        $task = Task::create([
            'project_id' => $project->id,
            'title'      => 'Tarea con Tiempo',
            'status'     => 'in_progress',
            'priority'   => 5,
            'created_by' => $admin->id,
            'tenant_id'  => $tenant->id,
        ]);

        // 3600 + 1800 = 5400 segundos = 1.5 horas
        TaskTimeEntry::create([
            'task_id' => $task->id, 'user_id' => $admin->id,
            'started_at' => now()->subHours(2), 'ended_at' => now()->subHour(),
            'duration_seconds' => 3600, 'is_running' => false,
        ]);
        TaskTimeEntry::create([
            'task_id' => $task->id, 'user_id' => $worker->id,
            'started_at' => now()->subMinutes(45), 'ended_at' => now()->subMinutes(15),
            'duration_seconds' => 1800, 'is_running' => false,
        ]);

        $task->refresh()->load('timeEntries');
        $totalSeconds = $task->timeEntries->where('is_running', false)->sum('duration_seconds');
        $totalHours   = round($totalSeconds / 3600, 1);

        $this->assertEquals(5400, $totalSeconds);
        $this->assertEquals(1.5, $totalHours);
    }
}