<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\JsonPlaceholderService;
use Illuminate\Support\Facades\Http;

class JsonPlaceholderServiceTest extends TestCase
{
    /** @test */
    public function it_fetches_posts_from_external_api()
    {
        // Fake de la API externa
        Http::fake([
            'jsonplaceholder.typicode.com/posts' => Http::response([
                [
                    'id' => 1,
                    'title' => 'Fake Post 1',
                    'body' => 'Contenido fake',
                ],
                [
                    'id' => 2,
                    'title' => 'Fake Post 2',
                    'body' => 'Contenido fake',
                ],
            ], 200),
        ]);

        $service = new JsonPlaceholderService();

        $posts = $service->getPosts();

        $this->assertCount(2, $posts);
        $this->assertEquals('Fake Post 1', $posts[0]['title']);
    }

    /** @test */
    public function it_throws_exception_when_api_fails()
    {
        Http::fake([
            'jsonplaceholder.typicode.com/posts' => Http::response(null, 500),
        ]);

        $this->expectException(\Exception::class);

        $service = new JsonPlaceholderService();
        $service->getPosts();
    }
}
