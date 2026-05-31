<?php

namespace Tests\Feature;

use App\Models\Label;
use App\Models\Project;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * 7.1.1 Pruebas funcionales: Proyectos y Tareas
 * 7.1.2 Pruebas de integración: relaciones entre modelos
 * 7.1.5 Pruebas de seguridad: autorización por permiso/rol
 */
class ProjectAndTaskTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helpers ────────────────────────────────────────────────────────────

    private function setupTenantWithAdmin(): array
    {
        $tenant = Tenant::create(['name' => 'Empresa Test', 'type' => 'empresa']);
        $admin  = User::create([
            'name'      => 'Admin',
            'email'     => 'admin@test.com',
            'password'  => Hash::make('password'),
            'tenant_id' => $tenant->id,
            'role'      => 'admin',
        ]);
        return [$tenant, $admin];
    }

    private function createNormalUser(Tenant $tenant, string $email = 'user@test.com'): User
    {
        $user = User::create([
            'name'      => 'Usuario Normal',
            'email'     => $email,
            'password'  => Hash::make('password'),
            'tenant_id' => $tenant->id,
            'role'      => 'user',
        ]);
        UserPermission::create(['user_id' => $user->id]);
        return $user;
    }

    private function createProject(Tenant $tenant, User $creator): Project
    {
        return Project::create([
            'name'       => 'Proyecto Test',
            'description'=> 'Descripción del proyecto',
            'status'     => 'active',
            'tenant_id'  => $tenant->id,
            'created_by' => $creator->id,
        ]);
    }

    private function createTask(Project $project, User $creator): Task
    {
        return Task::create([
            'project_id' => $project->id,
            'title'      => 'Tarea Test',
            'description'=> 'Descripción de la tarea',
            'status'     => 'pending',
            'priority'   => 5,
            'created_by' => $creator->id,
            'tenant_id'  => $project->tenant_id,
        ]);
    }

    // ─── Tests: Proyectos ────────────────────────────────────────────────────

    /** @test */
    public function admin_puede_crear_proyecto(): void
    {
        [$tenant, $admin] = $this->setupTenantWithAdmin();

        $project = $this->createProject($tenant, $admin);

        $this->assertDatabaseHas('projects', [
            'name'       => 'Proyecto Test',
            'tenant_id'  => $tenant->id,
            'created_by' => $admin->id,
            'status'     => 'active',
        ]);
        $this->assertNotNull($project->id);
    }

    /** @test */
    public function usuario_sin_permiso_no_puede_crear_proyecto_via_policy(): void
    {
        [$tenant, $admin] = $this->setupTenantWithAdmin();
        $user = $this->createNormalUser($tenant);

        $this->actingAs($user);
        $this->assertFalse($user->can('create', Project::class));
    }

    /** @test */
    public function usuario_con_permiso_puede_crear_proyecto_via_policy(): void
    {
        [$tenant, $admin] = $this->setupTenantWithAdmin();
        $user = $this->createNormalUser($tenant);
        $user->customPermissions()->update(['can_create_projects' => true]);
        $user->refresh()->load('customPermissions');

        $this->actingAs($user);
        $this->assertTrue($user->can('create', Project::class));
    }

    /** @test */
    public function proyecto_no_existe_devuelve_404(): void
    {
        [$tenant, $admin] = $this->setupTenantWithAdmin();

        $response = $this->actingAs($admin)->get('/projects/99999');
        $response->assertNotFound();
    }

    /** @test */
    public function admin_puede_ver_proyecto_existente(): void
    {
        [$tenant, $admin] = $this->setupTenantWithAdmin();
        $project = $this->createProject($tenant, $admin);

        $response = $this->actingAs($admin)->get("/projects/{$project->id}");
        $response->assertOk();
    }

    /** @test */
    public function soft_delete_de_proyecto_elimina_sus_tareas(): void
    {
        [$tenant, $admin] = $this->setupTenantWithAdmin();
        $project = $this->createProject($tenant, $admin);
        $task    = $this->createTask($project, $admin);

        $project->delete();

        $this->assertSoftDeleted('projects', ['id' => $project->id]);
        $this->assertSoftDeleted('tasks',    ['id' => $task->id]);
    }

    /** @test */
    public function restaurar_proyecto_restaura_sus_tareas(): void
    {
        [$tenant, $admin] = $this->setupTenantWithAdmin();
        $project = $this->createProject($tenant, $admin);
        $task    = $this->createTask($project, $admin);

        $project->delete();
        $project->restoreWithTasks();

        $this->assertDatabaseHas('projects', ['id' => $project->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('tasks',    ['id' => $task->id,    'deleted_at' => null]);
    }

    // ─── Tests: Tareas ───────────────────────────────────────────────────────

    /** @test */
    public function admin_puede_crear_tarea_en_proyecto(): void
    {
        [$tenant, $admin] = $this->setupTenantWithAdmin();
        $project = $this->createProject($tenant, $admin);
        $task    = $this->createTask($project, $admin);

        $this->assertDatabaseHas('tasks', [
            'title'      => 'Tarea Test',
            'project_id' => $project->id,
            'status'     => 'pending',
            'priority'   => 5,
        ]);
    }

    /** @test */
    public function tarea_pertenece_a_un_proyecto(): void
    {
        [$tenant, $admin] = $this->setupTenantWithAdmin();
        $project = $this->createProject($tenant, $admin);
        $task    = $this->createTask($project, $admin);

        $this->assertEquals($project->id, $task->project->id);
    }

    /** @test */
    public function usuario_sin_permisos_no_puede_crear_tarea_via_policy(): void
    {
        [$tenant, $admin] = $this->setupTenantWithAdmin();
        $project = $this->createProject($tenant, $admin);
        $user    = $this->createNormalUser($tenant);

        $this->actingAs($user);
        $this->assertFalse($user->can('create', [Task::class, $project]));
    }

    /** @test */
    public function usuario_con_permiso_puede_crear_tarea_via_policy(): void
    {
        [$tenant, $admin] = $this->setupTenantWithAdmin();
        $project = $this->createProject($tenant, $admin);
        $user    = $this->createNormalUser($tenant);
        $user->customPermissions()->update(['can_create_any_task' => true]);
        $user->refresh()->load('customPermissions');

        $this->actingAs($user);
        $this->assertTrue($user->can('create', [Task::class, $project]));
    }

    /** @test */
    public function usuario_sin_permisos_no_puede_editar_tarea_ajena(): void
    {
        [$tenant, $admin] = $this->setupTenantWithAdmin();
        $project = $this->createProject($tenant, $admin);
        $task    = $this->createTask($project, $admin); // creada por admin
        $user    = $this->createNormalUser($tenant);

        $this->actingAs($user);
        $this->assertFalse($user->can('update', $task));
    }

    /** @test */
    public function usuario_con_permiso_editar_any_puede_editar_tarea_ajena(): void
    {
        [$tenant, $admin] = $this->setupTenantWithAdmin();
        $project = $this->createProject($tenant, $admin);
        $task    = $this->createTask($project, $admin);
        $user    = $this->createNormalUser($tenant);
        $user->customPermissions()->update(['can_edit_any_task' => true]);
        $user->refresh()->load('customPermissions');

        $this->actingAs($user);
        $this->assertTrue($user->can('update', $task));
    }

    /** @test */
    public function usuario_sin_permisos_no_puede_borrar_tarea(): void
    {
        [$tenant, $admin] = $this->setupTenantWithAdmin();
        $project = $this->createProject($tenant, $admin);
        $task    = $this->createTask($project, $admin);
        $user    = $this->createNormalUser($tenant);

        $this->actingAs($user);
        $this->assertFalse($user->can('delete', $task));
    }

    /** @test */
    public function usuario_con_permiso_delete_any_puede_borrar_tarea(): void
    {
        [$tenant, $admin] = $this->setupTenantWithAdmin();
        $project = $this->createProject($tenant, $admin);
        $task    = $this->createTask($project, $admin);
        $user    = $this->createNormalUser($tenant);
        $user->customPermissions()->update(['can_delete_any_task' => true]);
        $user->refresh()->load('customPermissions');

        $this->actingAs($user);
        $this->assertTrue($user->can('delete', $task));
    }

    /** @test */
    public function tarea_tiene_prioridad_y_etiqueta_de_prioridad_correctas(): void
    {
        [$tenant, $admin] = $this->setupTenantWithAdmin();
        $project = $this->createProject($tenant, $admin);

        $task = Task::create([
            'project_id' => $project->id,
            'title'      => 'Tarea Alta Prioridad',
            'status'     => 'pending',
            'priority'   => 0,
            'created_by' => $admin->id,
            'tenant_id'  => $tenant->id,
        ]);

        $this->assertEquals('Muy Alta', $task->priority_label);
        $this->assertEquals('very_high', $task->priority_class);
    }

    /** @test */
    public function tarea_done_es_reconocida_como_completada(): void
    {
        [$tenant, $admin] = $this->setupTenantWithAdmin();
        $project = $this->createProject($tenant, $admin);
        $task    = $this->createTask($project, $admin);

        $task->update(['status' => 'done']);
        $task->refresh();

        $this->assertTrue($task->isDone());
    }

    // ─── Tests: Permisos tras cambio de permisos ────────────────────────────

    /** @test */
    public function quitar_permiso_impide_accion_antes_permitida(): void
    {
        [$tenant, $admin] = $this->setupTenantWithAdmin();
        $project = $this->createProject($tenant, $admin);
        $task    = $this->createTask($project, $admin);
        $user    = $this->createNormalUser($tenant);

        // Dar permiso
        $user->customPermissions()->update(['can_edit_any_task' => true]);
        $user->refresh()->load('customPermissions');
        $this->assertTrue($user->can('update', $task));

        // Quitar permiso
        $user->customPermissions()->update(['can_edit_any_task' => false]);
        $user->refresh()->load('customPermissions');
        $this->assertFalse($user->can('update', $task));
    }
}