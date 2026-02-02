<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ExternalPostController;
use App\Models\Project;

// Lista de proyectos
Route::get('/', function () {
    return view('livewire.partials.wrapper', [
        'component' => 'projects',
    ]);
})->name('projects');

// Detalle de proyecto
Route::get('/projects/{project}', function (Project $project) {
    return view('livewire.partials.wrapper', [
        'component' => 'project-detail',
        'project' => $project,
    ]);
})->name('projects.show');

// CRUD de tareas
Route::resource('projects.tasks', TaskController::class)
    ->except(['index', 'show', 'create']); 

// External posts
Route::get('/external-posts', [ExternalPostController::class, 'index']);
