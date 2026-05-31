<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * 7.1.2 Pruebas de integración: API REST de tareas
 * Verifica que los endpoints devuelven los datos correctos al frontend/cliente.
 */
class ApiTaskTest extends TestCase
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
            'status'     => 'active',
            'tenant_id'  => $tenant->id,
            'created_by' => $admin->id,
        ]);
        return [$tenant, $admin, $project];
    }

    // ─── Tests: GET /api/tasks ───────────────────────────────────────────────

    /** @test */
    public function api_devuelve_lista_de_tareas(): void
    {
        [$tenant, $admin, $project] = $this->setupWorld();

        Task::create([
            'project_id' => $project->id,
            'title'      => 'Tarea API 1',
            'status'     => 'pending',
            'priority'   => 5,
            'created_by' => $admin->id,
            'tenant_id'  => $tenant->id,
        ]);

        $response = $this->getJson('/api/tasks');

        $response->assertOk()
                 ->assertJsonStructure([
                     'data' => [['id', 'title', 'status', 'priority', 'project_id']],
                 ]);
    }

    /** @test */
    public function api_devuelve_tareas_filtradas_por_proyecto(): void
    {
        [$tenant, $admin, $project] = $this->setupWorld();

        $task = Task::create([
            'project_id' => $project->id,
            'title'      => 'Tarea del Proyecto',
            'status'     => 'pending',
            'priority'   => 3,
            'created_by' => $admin->id,
            'tenant_id'  => $tenant->id,
        ]);

        $response = $this->getJson("/api/tasks?project_id={$project->id}");

        $response->assertOk()
                 ->assertJsonFragment(['title' => 'Tarea del Proyecto']);
    }

    /** @test */
    public function api_devuelve_404_para_proyecto_inexistente(): void
    {
        $response = $this->getJson('/api/tasks?project_id=99999');
        $response->assertNotFound();
    }

    /** @test */
    public function api_rechaza_creacion_de_tarea_sin_titulo(): void
    {
        [$tenant, $admin, $project] = $this->setupWorld();

        $response = $this->postJson('/api/tasks', [
            'project_id' => $project->id,
            'status'     => 'pending',
            'priority'   => 'mid',
        ]);

        $response->assertUnprocessable()
                 ->assertJsonValidationErrors(['title']);
    }

    /** @test */
    public function api_rechaza_tarea_con_proyecto_inexistente(): void
    {
        $response = $this->postJson('/api/tasks', [
            'project_id' => 99999,
            'title'      => 'Tarea Huérfana',
            'status'     => 'pending',
            'priority'   => 'mid',
        ]);

        $response->assertUnprocessable()
                 ->assertJsonValidationErrors(['project_id']);
    }

    /** @test */
    public function api_rechaza_tarea_con_status_invalido(): void
    {
        [$tenant, $admin, $project] = $this->setupWorld();

        $response = $this->postJson('/api/tasks', [
            'project_id' => $project->id,
            'title'      => 'Tarea Inválida',
            'status'     => 'flying',
            'priority'   => 'mid',
        ]);

        $response->assertUnprocessable()
                 ->assertJsonValidationErrors(['status']);
    }

    // ─── Tests: GET /api/tasks/{id} ─────────────────────────────────────────

    /** @test */
    public function api_devuelve_detalle_de_tarea_existente(): void
    {
        [$tenant, $admin, $project] = $this->setupWorld();

        $task = Task::create([
            'project_id' => $project->id,
            'title'      => 'Detalle Tarea',
            'status'     => 'in_progress',
            'priority'   => 2,
            'created_by' => $admin->id,
            'tenant_id'  => $tenant->id,
        ]);

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertOk()
                 ->assertJsonFragment([
                     'title'  => 'Detalle Tarea',
                     'status' => 'in_progress',
                 ]);
    }

    /** @test */
    public function api_devuelve_404_para_tarea_inexistente(): void
    {
        $response = $this->getJson('/api/tasks/99999');
        $response->assertNotFound();
    }

    // ─── Tests: PUT /api/tasks/{id} ─────────────────────────────────────────

    /** @test */
    public function api_puede_actualizar_estado_de_tarea(): void
    {
        [$tenant, $admin, $project] = $this->setupWorld();

        $task = Task::create([
            'project_id' => $project->id,
            'title'      => 'Tarea a Actualizar',
            'status'     => 'pending',
            'priority'   => 5,
            'created_by' => $admin->id,
            'tenant_id'  => $tenant->id,
        ]);

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'status' => 'done',
        ]);

        $response->assertOk()
                 ->assertJsonFragment(['status' => 'done']);

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => 'done']);
    }

    // ─── Tests: DELETE /api/tasks/{id} ──────────────────────────────────────

    /** @test */
    public function api_puede_eliminar_una_tarea(): void
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

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertNoContent();
        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }

    // ─── Tests: Estructura de respuesta (integración frontend) ──────────────

    /** @test */
    public function api_responde_con_estructura_de_datos_correcta_para_tarea(): void
    {
        [$tenant, $admin, $project] = $this->setupWorld();

        $task = Task::create([
            'project_id' => $project->id,
            'title'      => 'Tarea Estructura',
            'status'     => 'pending',
            'priority'   => 5,
            'created_by' => $admin->id,
            'tenant_id'  => $tenant->id,
        ]);

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertOk()
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'project_id',
                         'title',
                         'description',
                         'status',
                         'priority',
                         'due_date',
                         'created_at',
                         'updated_at',
                     ],
                 ]);
    }
}