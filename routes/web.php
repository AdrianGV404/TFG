<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Models\Project;
use Illuminate\Support\Facades\Route;

// Página de inicio → Livewire
Route::get('/', fn() => view('livewire.partials.wrapper', [
    'component' => 'landingpage',
]))->name('landingpage');

// Lista de proyectos → Livewire
Route::get('/projects', fn() => view('livewire.partials.wrapper', [
    'component' => 'projects',
]))->name('projects');

// Detalle de proyecto → Livewire
Route::get('/projects/{project}', fn(Project $project) => view('livewire.partials.wrapper', [
    'component' => 'project-detail',
    'project' => $project,
]))->name('projects.show');

// CRUD de tareas → Livewire
Route::resource('projects.tasks', TaskController::class)
    ->except(['index', 'show', 'create']);

// Crear usuario personal → Livewire
Route::get('/register-personal', fn() => view('livewire.partials.wrapper', [
    'component' => 'register-personal',
]))->name('register.personal');

// Login → Livewire
Route::get('/login', fn() => view('livewire.partials.wrapper', [
    'component' => 'login',
]))->name('login');

// Logout → controlador normal
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
