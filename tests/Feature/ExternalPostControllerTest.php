<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\JsonPlaceholderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class ExternalPostControllerTest extends TestCase
{
    /** @test */
    public function it_displays_external_posts()
    {
        // Datos falsos que devolverá el service
        $fakePosts = [
            [
                'id' => 1,
                'title' => 'Post externo 1',
                'body' => 'Contenido del post 1',
            ],
            [
                'id' => 2,
                'title' => 'Post externo 2',
                'body' => 'Contenido del post 2',
            ],
        ];

        // Mock del service
        $this->mock(JsonPlaceholderService::class, function ($mock) use ($fakePosts) {
            $mock->shouldReceive('getPosts')
                ->once()
                ->andReturn($fakePosts);
        });

        // Llamada al controller
        $response = $this->get('/external-posts');

        // Assertions
        $response->assertOk();
        $response->assertViewIs('external.posts');
        $response->assertViewHas('posts', $fakePosts);
    }

    /** @test */
    public function it_handles_service_errors_gracefully()
    {
        // Mock que lanza excepción
        $this->mock(JsonPlaceholderService::class, function ($mock) {
            $mock->shouldReceive('getPosts')
                ->once()
                ->andThrow(new \Exception('API error'));
        });

        $response = $this->get('/external-posts');

        $response->assertOk();
        $response->assertViewIs('external.posts');
        $response->assertViewHas('posts', []);
        $response->assertSessionHas('error');
    }
}
