<?php

namespace Tests\Feature;

use App\Models\Label;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskTimeEntry;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * 7.1.1 Pruebas funcionales: Etiquetas y registro de tiempo
 * 7.1.2 Pruebas de integración: relaciones muchos a muchos
 */
class LabelAndTimeTrackingTest extends TestCase
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
        $task = Task::create([
            'project_id' => $project->id,
            'title'      => 'Tarea Test',
            'status'     => 'pending',
            'priority'   => 5,
            'created_by' => $admin->id,
            'tenant_id'  => $tenant->id,
        ]);
        return [$tenant, $admin, $project, $task];
    }

    // ─── Tests: Etiquetas ───────────────────────────────────────────────────

    /** @test */
    public function puede_crear_etiqueta_en_un_tenant(): void
    {
        [$tenant] = $this->setupWorld();

        $label = Label::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Urgente',
        ]);

        $this->assertDatabaseHas('labels', [
            'tenant_id' => $tenant->id,
            'name'      => 'Urgente',
        ]);
    }

    /** @test */
    public function no_puede_haber_etiquetas_duplicadas_en_el_mismo_tenant(): void
    {
        [$tenant] = $this->setupWorld();

        Label::create(['tenant_id' => $tenant->id, 'name' => 'Bug']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Label::create(['tenant_id' => $tenant->id, 'name' => 'Bug']);
    }

    /** @test */
    public function puede_asignar_etiqueta_existente_a_una_tarea(): void
    {
        [$tenant, $admin, $project, $task] = $this->setupWorld();

        $label = Label::create(['tenant_id' => $tenant->id, 'name' => 'Frontend']);
        $task->labels()->attach($label->id);

        $this->assertDatabaseHas('label_task', [
            'label_id' => $label->id,
            'task_id'  => $task->id,
        ]);
        $this->assertTrue($task->labels->contains($label->id));
    }

    /** @test */
    public function asignar_etiqueta_que_no_existe_lanza_error(): void
    {
        [$tenant, $admin, $project, $task] = $this->setupWorld();

        $this->expectException(\Illuminate\Database\QueryException::class);

        // ID 99999 no existe en labels → FK violation
        $task->labels()->attach(99999);
    }

    /** @test */
    public function puede_asignar_multiples_etiquetas_a_una_tarea(): void
    {
        [$tenant, $admin, $project, $task] = $this->setupWorld();

        $label1 = Label::create(['tenant_id' => $tenant->id, 'name' => 'Backend']);
        $label2 = Label::create(['tenant_id' => $tenant->id, 'name' => 'QA']);
        $label3 = Label::create(['tenant_id' => $tenant->id, 'name' => 'Urgente']);

        $task->labels()->sync([$label1->id, $label2->id, $label3->id]);
        $task->refresh()->load('labels');

        $this->assertCount(3, $task->labels);
    }

    /** @test */
    public function puede_desasignar_etiqueta_de_una_tarea(): void
    {
        [$tenant, $admin, $project, $task] = $this->setupWorld();

        $label = Label::create(['tenant_id' => $tenant->id, 'name' => 'Revisión']);
        $task->labels()->attach($label->id);
        $task->labels()->detach($label->id);

        $this->assertDatabaseMissing('label_task', [
            'label_id' => $label->id,
            'task_id'  => $task->id,
        ]);
    }

    /** @test */
    public function eliminar_etiqueta_la_quita_de_las_tareas_asociadas(): void
    {
        [$tenant, $admin, $project, $task] = $this->setupWorld();

        $label = Label::create(['tenant_id' => $tenant->id, 'name' => 'Temporal']);
        $task->labels()->attach($label->id);

        $label->delete();

        $this->assertDatabaseMissing('labels',    ['id' => $label->id]);
        $this->assertDatabaseMissing('label_task', ['label_id' => $label->id]);
    }

    // ─── Tests: Registro de tiempo ──────────────────────────────────────────

    /** @test */
    public function puede_iniciar_entrada_de_tiempo_en_una_tarea(): void
    {
        [$tenant, $admin, $project, $task] = $this->setupWorld();

        $entry = TaskTimeEntry::create([
            'task_id'    => $task->id,
            'user_id'    => $admin->id,
            'started_at' => now(),
            'is_running' => true,
        ]);

        $this->assertDatabaseHas('task_time_entries', [
            'task_id'    => $task->id,
            'user_id'    => $admin->id,
            'is_running' => true,
        ]);
    }

    /** @test */
    public function puede_cerrar_entrada_de_tiempo_con_duracion(): void
    {
        [$tenant, $admin, $project, $task] = $this->setupWorld();

        $startedAt = now()->subMinutes(30);
        $entry = TaskTimeEntry::create([
            'task_id'    => $task->id,
            'user_id'    => $admin->id,
            'started_at' => $startedAt,
            'is_running' => true,
        ]);

        $now      = now();
        $segundos = $now->diffInSeconds($startedAt);

        $entry->update([
            'ended_at'         => $now,
            'duration_seconds' => $segundos,
            'is_running'       => false,
        ]);

        $this->assertDatabaseHas('task_time_entries', [
            'id'         => $entry->id,
            'is_running' => false,
        ]);
        $this->assertGreaterThan(0, $entry->fresh()->duration_seconds);
    }

    /** @test */
    public function tarea_acumula_tiempo_de_multiples_entradas(): void
    {
        [$tenant, $admin, $project, $task] = $this->setupWorld();

        TaskTimeEntry::create([
            'task_id'          => $task->id,
            'user_id'          => $admin->id,
            'started_at'       => now()->subHour(),
            'ended_at'         => now()->subHour()->addMinutes(30),
            'duration_seconds' => 1800,
            'is_running'       => false,
        ]);
        TaskTimeEntry::create([
            'task_id'          => $task->id,
            'user_id'          => $admin->id,
            'started_at'       => now()->subMinutes(45),
            'ended_at'         => now()->subMinutes(15),
            'duration_seconds' => 1800,
            'is_running'       => false,
        ]);

        $task->refresh()->load('timeEntries');
        $totalSeconds = $task->timeEntries->where('is_running', false)->sum('duration_seconds');

        $this->assertEquals(3600, $totalSeconds); // 1 hora total
    }
}