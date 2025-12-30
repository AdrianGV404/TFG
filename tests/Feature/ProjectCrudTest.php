<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectCrudTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_project()
    {
        $response = $this->post(route('projects.store'), [
            'name' => 'Proyecto de prueba',
            'description' => 'Descripción del proyecto',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('projects.index'));

        $this->assertDatabaseHas('projects', [
            'name' => 'Proyecto de prueba',
            'status' => 'active',
        ]);
    }

    /** @test */
    public function it_shows_a_project()
    {
        $project = Project::factory()->create();

        $response = $this->get(route('projects.show', $project));

        $response->assertStatus(200);
        $response->assertSee($project->name);
    }

    /** @test */
    public function it_updates_a_project()
    {
        $project = Project::factory()->create([
            'status' => 'active',
        ]);

        $response = $this->put(route('projects.update', $project), [
            'name' => 'Proyecto actualizado',
            'description' => 'Nueva descripción',
            'status' => 'archived',
        ]);

        $response->assertRedirect(route('projects.index'));

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Proyecto actualizado',
            'status' => 'archived',
        ]);
    }

    /** @test */
    public function it_deletes_a_project()
    {
        $project = Project::factory()->create();

        $response = $this->delete(route('projects.destroy', $project));

        $response->assertRedirect(route('projects.index'));

        $this->assertDatabaseMissing('projects', [
            'id' => $project->id,
        ]);
    }
}
