<?php

use Illuminate\Support\Facades\Route;
use App\Models\Project;

// Lista de proyectos → Livewire
Route::get('/', fn() => view('livewire.partials.wrapper', [
    'component' => 'projects',
]))->name('projects');

// Detalle de proyecto → Livewire
Route::get('/projects/{project}', fn(Project $project) => view('livewire.partials.wrapper', [
    'component' => 'project-detail',
    'project' => $project,
]))->name('projects.show');

// CRUD de tareas → Livewire
Route::resource('projects.tasks', \App\Http\Controllers\TaskController::class)
    ->except(['index', 'show', 'create']);

// Otros recursos
Route::get('/external-posts', [\App\Http\Controllers\ExternalPostController::class, 'index']);
