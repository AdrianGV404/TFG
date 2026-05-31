<?php

namespace Tests\Feature;

use App\Livewire\Login;
use App\Livewire\Projects;
use App\Livewire\Tasks;
use App\Livewire\Tasks\TaskDetail;
use App\Models\Label;
use App\Models\Project;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * 7.1.1 Pruebas funcionales: Componentes Livewire
 * 7.1.2 Pruebas de integración: frontend ↔ backend via Livewire
 *
 * Livewire 3 tiene soporte nativo para testing con Livewire::test().
 * Estos tests verifican el comportamiento de los componentes sin
 * necesidad de abrir el navegador.
 */
class LivewireTest extends TestCase
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
        $project = Project::create([
            'name'       => 'Proyecto Test',
            'description'=> 'Descripción',
            'status'     => 'active',
            'tenant_id'  => $tenant->id,
            'created_by' => $admin->id,
        ]);
        return [$tenant, $admin, $project];
    }

    private function makeNormalUser(Tenant $tenant): User
    {
        $user = User::create([
            'name'      => 'Usuario Normal',
            'email'     => 'user@test.com',
            'password'  => Hash::make('password'),
            'tenant_id' => $tenant->id,
            'role'      => 'user',
        ]);
        UserPermission::create(['user_id' => $user->id]);
        return $user;
    }

    // ─── Tests: Componente Login ─────────────────────────────────────────────

    /** @test */
    public function login_component_se_renderiza_correctamente(): void
    {
        Livewire::test(Login::class)
            ->assertStatus(200);
    }

    /** @test */
    public function login_component_muestra_error_con_credenciales_incorrectas(): void
    {
        [$tenant, $admin] = $this->setupWorld();

        Livewire::test(Login::class)
            ->set('email', 'admin@test.com')
            ->set('password', 'wrongpassword')
            ->call('login')
            ->assertHasErrors(['email']);
    }

    /** @test */
    public function login_component_redirige_con_credenciales_correctas(): void
    {
        [$tenant, $admin] = $this->setupWorld();

        Livewire::test(Login::class)
            ->set('email', 'admin@test.com')
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect('/projects');
    }

    // ─── Tests: Componente Projects ──────────────────────────────────────────

    /** @test */
    public function projects_component_se_renderiza_para_admin(): void
    {
        [$tenant, $admin] = $this->setupWorld();

        Livewire::actingAs($admin)
            ->test(Projects::class)
            ->assertStatus(200);
    }

    /** @test */
    public function projects_component_muestra_proyectos_del_tenant(): void
    {
        [$tenant, $admin, $project] = $this->setupWorld();

        Livewire::actingAs($admin)
            ->test(Projects::class)
            ->assertSee('Proyecto Test');
    }

    /** @test */
    public function projects_component_no_muestra_proyectos_de_otro_tenant(): void
    {
        [$tenantA, $adminA] = $this->setupWorld();

        $tenantB = Tenant::create(['name' => 'Empresa B', 'type' => 'empresa']);
        $adminB  = User::create([
            'name'      => 'Admin B',
            'email'     => 'adminb@test.com',
            'password'  => Hash::make('password'),
            'tenant_id' => $tenantB->id,
            'role'      => 'admin',
        ]);
        Project::create([
            'name'       => 'Proyecto Privado B',
            'status'     => 'active',
            'tenant_id'  => $tenantB->id,
            'created_by' => $adminB->id,
        ]);

        Livewire::actingAs($adminA)
            ->test(Projects::class)
            ->assertDontSee('Proyecto Privado B');
    }

    /** @test */
    public function projects_component_permite_buscar_por_nombre(): void
    {
        [$tenant, $admin] = $this->setupWorld();

        Project::create([
            'name'       => 'Alpha Project',
            'status'     => 'active',
            'tenant_id'  => $tenant->id,
            'created_by' => $admin->id,
        ]);
        Project::create([
            'name'       => 'Beta Project',
            'status'     => 'active',
            'tenant_id'  => $tenant->id,
            'created_by' => $admin->id,
        ]);

        Livewire::actingAs($admin)
            ->test(Projects::class)
            ->set('searchText', 'Alpha')
            ->assertSee('Alpha Project')
            ->assertDontSee('Beta Project');
    }

    /** @test */
    public function projects_component_puede_eliminar_proyecto(): void
    {
        [$tenant, $admin, $project] = $this->setupWorld();

        Livewire::actingAs($admin)
            ->test(Projects::class)
            ->call('delete', $project->id);

        $this->assertSoftDeleted('projects', ['id' => $project->id]);
    }

    /** @test */
    public function projects_component_usuario_sin_permiso_no_puede_editar(): void
    {
        [$tenant, $admin, $project] = $this->setupWorld();
        $user = $this->makeNormalUser($tenant);

        // El usuario sin permiso llama a startEdit → recibe notify de error
        Livewire::actingAs($user)
            ->test(Projects::class)
            ->call('startEdit', $project->id)
            ->assertDispatched('notify');

        // El editingProjectId no debería haberse establecido
        Livewire::actingAs($user)
            ->test(Projects::class)
            ->call('startEdit', $project->id)
            ->assertSet('editingProjectId', null);
    }

    // ─── Tests: Componente Tasks ─────────────────────────────────────────────

    /** @test */
    public function tasks_component_se_renderiza_para_proyecto(): void
    {
        [$tenant, $admin, $project] = $this->setupWorld();

        Livewire::actingAs($admin)
            ->test(Tasks::class, ['project' => $project])
            ->assertStatus(200);
    }

    /** @test */
    public function tasks_component_muestra_tareas_del_proyecto(): void
    {
        [$tenant, $admin, $project] = $this->setupWorld();

        Task::create([
            'project_id' => $project->id,
            'title'      => 'Tarea Visible',
            'status'     => 'pending',
            'priority'   => 5,
            'created_by' => $admin->id,
            'tenant_id'  => $tenant->id,
        ]);

        Livewire::actingAs($admin)
            ->test(Tasks::class, ['project' => $project])
            ->assertSee('Tarea Visible');
    }

    /** @test */
    public function tasks_component_puede_abrir_formulario_de_nueva_tarea(): void
    {
        [$tenant, $admin, $project] = $this->setupWorld();

        Livewire::actingAs($admin)
            ->test(Tasks::class, ['project' => $project])
            ->call('openForm')
            ->assertSet('showForm', true);
    }

    /** @test */
    public function tasks_component_puede_cerrar_formulario(): void
    {
        [$tenant, $admin, $project] = $this->setupWorld();

        Livewire::actingAs($admin)
            ->test(Tasks::class, ['project' => $project])
            ->call('openForm')
            ->call('closeForm')
            ->assertSet('showForm', false);
    }

    /** @test */
    public function tasks_component_permite_eliminar_tarea(): void
    {
        [$tenant, $admin, $project] = $this->setupWorld();

        $task = Task::create([
            'project_id' => $project->id,
            'title'      => 'Tarea a Borrar',
            'status'     => 'pending',
            'priority'   => 5,
            'created_by' => $admin->id,
            'tenant_id'  => $tenant->id,
        ]);

        Livewire::actingAs($admin)
            ->test(Tasks::class, ['project' => $project])
            ->call('delete', $task->id);

        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }

    // ─── Tests: Componente TaskDetail ────────────────────────────────────────

    /** @test */
    public function task_detail_se_renderiza_para_su_creador(): void
    {
        [$tenant, $admin, $project] = $this->setupWorld();

        $task = Task::create([
            'project_id' => $project->id,
            'title'      => 'Tarea Detalle',
            'status'     => 'pending',
            'priority'   => 5,
            'created_by' => $admin->id,
            'tenant_id'  => $tenant->id,
        ]);

        Livewire::actingAs($admin)
            ->test(TaskDetail::class, ['task' => $task])
            ->assertStatus(200)
            ->assertSee('Tarea Detalle');
    }

    /** @test */
    public function task_detail_usuario_sin_permiso_no_puede_ver_tarea_ajena(): void
    {
        [$tenant, $admin, $project] = $this->setupWorld();
        $user = $this->makeNormalUser($tenant);

        $task = Task::create([
            'project_id' => $project->id,
            'title'      => 'Tarea del Admin',
            'status'     => 'pending',
            'priority'   => 5,
            'created_by' => $admin->id,  // creada por admin
            'tenant_id'  => $tenant->id,
        ]);

        // user no es el creador ni tiene permisos → 403
        $this->actingAs($user)
             ->get("/tasks/{$task->id}")
             ->assertForbidden();
    }

    /** @test */
    public function task_detail_puede_cambiar_a_modo_edicion(): void
    {
        [$tenant, $admin, $project] = $this->setupWorld();

        $task = Task::create([
            'project_id' => $project->id,
            'title'      => 'Tarea Editable',
            'status'     => 'pending',
            'priority'   => 5,
            'created_by' => $admin->id,
            'tenant_id'  => $tenant->id,
        ]);

        Livewire::actingAs($admin)
            ->test(TaskDetail::class, ['task' => $task])
            ->call('startEdit')
            ->assertSet('isEditing', true);
    }

    /** @test */
    public function task_detail_puede_guardar_cambios(): void
    {
        [$tenant, $admin, $project] = $this->setupWorld();

        $task = Task::create([
            'project_id' => $project->id,
            'title'      => 'Tarea Original',
            'status'     => 'pending',
            'priority'   => 5,
            'created_by' => $admin->id,
            'tenant_id'  => $tenant->id,
        ]);

        Livewire::actingAs($admin)
            ->test(TaskDetail::class, ['task' => $task])
            ->call('startEdit')
            ->set('title', 'Tarea Actualizada')
            ->set('status', 'in_progress')
            ->call('save')
            ->assertSet('isEditing', false);

        $this->assertDatabaseHas('tasks', [
            'id'     => $task->id,
            'title'  => 'Tarea Actualizada',
            'status' => 'in_progress',
        ]);
    }

    /** @test */
    public function task_detail_puede_cancelar_edicion_sin_guardar(): void
    {
        [$tenant, $admin, $project] = $this->setupWorld();

        $task = Task::create([
            'project_id' => $project->id,
            'title'      => 'Tarea Sin Cambiar',
            'status'     => 'pending',
            'priority'   => 5,
            'created_by' => $admin->id,
            'tenant_id'  => $tenant->id,
        ]);

        Livewire::actingAs($admin)
            ->test(TaskDetail::class, ['task' => $task])
            ->call('startEdit')
            ->set('title', 'Cambio Temporal')
            ->call('cancelEdit')
            ->assertSet('isEditing', false)
            ->assertSet('title', 'Tarea Sin Cambiar'); // título restaurado

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Tarea Sin Cambiar']);
    }

    /** @test */
    public function task_detail_muestra_modal_de_tiempo_manual(): void
    {
        [$tenant, $admin, $project] = $this->setupWorld();

        $task = Task::create([
            'project_id' => $project->id,
            'title'      => 'Tarea Tiempo',
            'status'     => 'pending',
            'priority'   => 5,
            'created_by' => $admin->id,
            'tenant_id'  => $tenant->id,
        ]);

        Livewire::actingAs($admin)
            ->test(TaskDetail::class, ['task' => $task])
            ->call('openManualTimeModal')
            ->assertSet('showManualTimeModal', true);
    }

    /** @test */
    public function task_detail_guarda_tiempo_manual_correctamente(): void
    {
        [$tenant, $admin, $project] = $this->setupWorld();

        $task = Task::create([
            'project_id' => $project->id,
            'title'      => 'Tarea Tiempo Manual',
            'status'     => 'pending',
            'priority'   => 5,
            'created_by' => $admin->id,
            'tenant_id'  => $tenant->id,
        ]);

        Livewire::actingAs($admin)
            ->test(TaskDetail::class, ['task' => $task])
            ->call('openManualTimeModal')
            ->set('manualHours', 2)
            ->set('manualMinutes', 30)
            ->call('saveManualTime')
            ->assertSet('showManualTimeModal', false);

        $this->assertDatabaseHas('task_time_entries', [
            'task_id'          => $task->id,
            'user_id'          => $admin->id,
            'duration_seconds' => (2 * 3600) + (30 * 60), // 9000 segundos
            'is_running'       => false,
        ]);
    }

    /** @test */
    public function task_detail_rechaza_tiempo_manual_de_cero(): void
    {
        [$tenant, $admin, $project] = $this->setupWorld();

        $task = Task::create([
            'project_id' => $project->id,
            'title'      => 'Tarea Cero',
            'status'     => 'pending',
            'priority'   => 5,
            'created_by' => $admin->id,
            'tenant_id'  => $tenant->id,
        ]);

        Livewire::actingAs($admin)
            ->test(TaskDetail::class, ['task' => $task])
            ->call('openManualTimeModal')
            ->set('manualHours', 0)
            ->set('manualMinutes', 0)
            ->call('saveManualTime')
            // El modal debe seguir abierto (no guardó)
            ->assertSet('showManualTimeModal', true);
    }

    // ─── Tests: Datos enviados al frontend (integración) ────────────────────

    /** @test */
    public function projects_component_envia_labels_a_la_vista(): void
    {
        [$tenant, $admin, $project] = $this->setupWorld();

        Label::create(['tenant_id' => $tenant->id, 'name' => 'Etiqueta Visible']);

        Livewire::actingAs($admin)
            ->test(Projects::class)
            ->assertSee('Etiqueta Visible'); // aparece en el dropdown de filtros
    }
}