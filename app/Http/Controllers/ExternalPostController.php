<?php

namespace App\Http\Controllers;

use App\Services\JsonPlaceholderService;
use Illuminate\View\View;

class ExternalPostController extends Controller
{
    public function index(JsonPlaceholderService $service): View
    {
        try {
            $posts = $service->getPosts();
        } catch (\Exception $e) {
            $posts = [];
            session()->flash('error', 'No se pudieron cargar los datos externos');
        }

        return view('external.posts', compact('posts'));
    }
}
