<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Models\Project;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS (Visitantes)
|--------------------------------------------------------------------------
*/

// Página de inicio: si está logueado va a proyectos, si no, a la landing
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('projects');
    }
    return view('livewire.partials.wrapper', ['component' => 'landingpage']);
})->name('landingpage');

// Rutas para usuarios NO logueados (Guest)
Route::middleware(['guest'])->group(function () {
    // Login con Livewire
    Route::get('/login', fn() => view('livewire.partials.wrapper', [
        'component' => 'login',
    ]))->name('login');

    // Registros
    Route::get('/register-personal', fn() => view('livewire.partials.wrapper', [
        'component' => 'register-personal',
    ]))->name('register.personal');

    Route::get('/register-empresa', fn() => view('livewire.partials.wrapper', [
        'component' => 'register-empresa',
    ]))->name('register.empresa');
});

// Logout (POST) - Siempre debe estar accesible para usuarios autenticados
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS (Solo usuarios autenticados)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', fn () => view('livewire.partials.wrapper', [
        'component' => 'dashboard',
    ]))->name('dashboard');

    Route::view('/calendario', 'livewire.partials.wrapper', [
        'component' => 'calendario'
    ])->name('calendario');

    // Mi Perfil (Configuración del Sistema y Usuario)
    Route::get('/profile', fn() => view('livewire.partials.wrapper', [
        'component' => 'user-profile',
    ]))->name('profile');

    // Gestión de Proyectos
    Route::get('/projects', fn() => view('livewire.partials.wrapper', [
        'component' => 'projects',
    ]))->name('projects');

    Route::get('/projects/{project}', fn(Project $project) => view('livewire.partials.wrapper', [
        'component' => 'project-detail',
        'project' => $project,
    ]))->name('projects.show');

    // CRUD de tareas
    Route::resource('projects.tasks', TaskController::class)
        ->except(['index', 'show', 'create']);

    /*
    |--- SOLO ADMINISTRADORES ---
    */
    Route::middleware(['isAdmin'])->group(function () {
        Route::get('/users', fn() => view('livewire.partials.wrapper', [
            'component' => 'user-management',
        ]))->name('users.index');

        Route::get('/users/create', fn() => view('livewire.partials.wrapper', [
            'component' => 'user-form',
        ]))->name('users.create');
    });
});