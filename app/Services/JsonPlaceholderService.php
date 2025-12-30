<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Exception;

class JsonPlaceholderService
{
    protected string $baseUrl = 'https://jsonplaceholder.typicode.com';
    
    /**
     * Obtiene la lista de posts desde la API externa.
     *
     * - Cachea la respuesta durante 60 segundos para evitar llamadas innecesarias.
     * - Usa el HTTP Client de Laravel con timeout para evitar bloqueos.
     * - Lanza una excepción si la API responde con error.
     */
    public function getPosts()
    {
        return Cache::remember('jsonplaceholder.posts', 60, function () {
            $response = Http::timeout(5)->get("{$this->baseUrl}/posts");

            if ($response->failed()) {
                throw new Exception('Error al consumir la API externa');
            }

            return $response->json();
        });
    }
}
