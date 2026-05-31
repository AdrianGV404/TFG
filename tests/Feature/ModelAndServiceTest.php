<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserPermission;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * 7.1.1 Pruebas funcionales: lógica de negocio de modelos y servicios
 */
class ModelAndServiceTest extends TestCase
{
    use RefreshDatabase;

    private function createAdminUser(): User
    {
        $tenant = Tenant::create(['name' => 'Test', 'type' => 'empresa']);
        return User::create([
            'name'      => 'Admin',
            'email'     => 'admin@test.com',
            'password'  => Hash::make('password'),
            'tenant_id' => $tenant->id,
            'role'      => 'admin',
        ]);
    }

    // ─── Tests: Task priority labels ─────────────────────────────────────────

    /** @test */
    public function priority_labels_son_correctos_para_todos_los_valores(): void
    {
        $expected = [
            0  => ['label' => 'Muy Alta',  'class' => 'very_high'],
            1  => ['label' => 'Alta',      'class' => 'high'],
            4  => ['label' => 'Media',     'class' => 'mid'],
            7  => ['label' => 'Baja',      'class' => 'low'],
            9  => ['label' => 'Muy Baja',  'class' => 'very_low'],
            10 => ['label' => 'Muy Baja',  'class' => 'very_low'],
        ];

        foreach ($expected as $priority => $data) {
            $this->assertEquals(
                $data['label'],
                Task::PRIORITY_LABELS[$priority],
                "Fallo en priority={$priority}"
            );
            $this->assertEquals(
                $data['class'],
                Task::PRIORITY_CLASSES[$priority],
                "Fallo en priority={$priority}"
            );
        }
    }

    // ─── Tests: User::isAdmin ────────────────────────────────────────────────

    /** @test */
    public function is_admin_devuelve_true_para_rol_admin(): void
    {
        $tenant = Tenant::create(['name' => 'T', 'type' => 'empresa']);
        $user = User::create([
            'name' => 'A', 'email' => 'a@a.com',
            'password' => Hash::make('p'),
            'tenant_id' => $tenant->id, 'role' => 'admin',
        ]);
        $this->assertTrue($user->isAdmin());
    }

    /** @test */
    public function is_admin_devuelve_false_para_rol_user(): void
    {
        $tenant = Tenant::create(['name' => 'T', 'type' => 'empresa']);
        $user = User::create([
            'name' => 'U', 'email' => 'u@u.com',
            'password' => Hash::make('p'),
            'tenant_id' => $tenant->id, 'role' => 'user',
        ]);
        $this->assertFalse($user->isAdmin());
    }

    // ─── Tests: User::getProfilePhotoUrlAttribute ────────────────────────────

    /** @test */
    public function sin_foto_devuelve_url_de_avatar_generado(): void
    {
        $tenant = Tenant::create(['name' => 'T', 'type' => 'empresa']);
        $user = User::create([
            'name' => 'Juan García', 'email' => 'juan@test.com',
            'password' => Hash::make('p'),
            'tenant_id' => $tenant->id, 'role' => 'user',
        ]);

        $url = $user->profile_photo_url;
        $this->assertStringContainsString('ui-avatars.com', $url);
        $this->assertStringContainsString('Juan', $url);
    }

    // ─── Tests: UserPermission hasCustomPermission ───────────────────────────

    /** @test */
    public function user_sin_fila_de_permisos_deniega_todo(): void
    {
        $tenant = Tenant::create(['name' => 'T', 'type' => 'empresa']);
        $user = User::create([
            'name' => 'U', 'email' => 'u@u.com',
            'password' => Hash::make('p'),
            'tenant_id' => $tenant->id, 'role' => 'user',
        ]);
        // Sin UserPermission creado

        $this->assertFalse($user->hasCustomPermission('create_project'));
        $this->assertFalse($user->hasCustomPermission('create_task'));
        $this->assertFalse($user->hasCustomPermission('edit_task'));
    }

    /** @test */
    public function admin_bypasa_todos_los_permisos(): void
    {
        $admin = $this->createAdminUser();

        $this->assertTrue($admin->hasCustomPermission('create_project'));
        $this->assertTrue($admin->hasCustomPermission('create_task'));
        $this->assertTrue($admin->hasCustomPermission('edit_task'));
        $this->assertTrue($admin->hasCustomPermission('delete_task'));
        $this->assertTrue($admin->hasCustomPermission('reassign_users'));
    }

    // ─── Tests: TaskService ──────────────────────────────────────────────────

    /** @test */
    public function task_service_create_crea_tarea_correctamente(): void
    {
        $tenant  = Tenant::create(['name' => 'T', 'type' => 'empresa']);
        $admin   = User::create([
            'name' => 'A', 'email' => 'a@a.com',
            'password' => Hash::make('p'),
            'tenant_id' => $tenant->id, 'role' => 'admin',
        ]);
        $project = \App\Models\Project::create([
            'name' => 'P', 'status' => 'active',
            'tenant_id' => $tenant->id, 'created_by' => $admin->id,
        ]);

        $service = new TaskService();
        $task = $service->create([
            'project_id' => $project->id,
            'title'      => 'Tarea via Service',
            'status'     => 'pending',
            'priority'   => 5,
        ]);

        $this->assertDatabaseHas('tasks', [
            'title'      => 'Tarea via Service',
            'project_id' => $project->id,
        ]);
        $this->assertInstanceOf(Task::class, $task);
    }

    /** @test */
    public function task_service_update_actualiza_tarea(): void
    {
        $tenant  = Tenant::create(['name' => 'T', 'type' => 'empresa']);
        $admin   = User::create([
            'name' => 'A', 'email' => 'a@a.com',
            'password' => Hash::make('p'),
            'tenant_id' => $tenant->id, 'role' => 'admin',
        ]);
        $project = \App\Models\Project::create([
            'name' => 'P', 'status' => 'active',
            'tenant_id' => $tenant->id, 'created_by' => $admin->id,
        ]);
        $task = Task::create([
            'project_id' => $project->id,
            'title'      => 'Original',
            'status'     => 'pending',
            'priority'   => 5,
            'created_by' => $admin->id,
            'tenant_id'  => $tenant->id,
        ]);

        $service = new TaskService();
        $updated = $service->update($task, ['title' => 'Actualizado', 'status' => 'done']);

        $this->assertEquals('Actualizado', $updated->title);
        $this->assertEquals('done', $updated->status);
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => 'done']);
    }

    /** @test */
    public function task_service_list_all_devuelve_todas_las_tareas(): void
    {
        $tenant  = Tenant::create(['name' => 'T', 'type' => 'empresa']);
        $admin   = User::create([
            'name' => 'A', 'email' => 'a@a.com',
            'password' => Hash::make('p'),
            'tenant_id' => $tenant->id, 'role' => 'admin',
        ]);
        $project = \App\Models\Project::create([
            'name' => 'P', 'status' => 'active',
            'tenant_id' => $tenant->id, 'created_by' => $admin->id,
        ]);

        Task::create(['project_id' => $project->id, 'title' => 'T1', 'status' => 'pending', 'priority' => 5, 'created_by' => $admin->id, 'tenant_id' => $tenant->id]);
        Task::create(['project_id' => $project->id, 'title' => 'T2', 'status' => 'done',    'priority' => 3, 'created_by' => $admin->id, 'tenant_id' => $tenant->id]);

        $service = new TaskService();
        $tasks = $service->listAll();

        $this->assertCount(2, $tasks);
    }
}