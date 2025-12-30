<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ExternalPostController;
use App\Livewire\ProjectDetail;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::resource('projects', ProjectController::class);
Route::resource('projects.tasks', TaskController::class)->except(['index', 'show', 'create']);
Route::get('/external-posts', [ExternalPostController::class, 'index']);
Route::get('/livewire/projects', function () {
    return view('livewire.projects-page');
})->name('livewire.projects');
Route::get('/livewire/projects/{project}', ProjectDetail::class)
    ->name('livewire.projects.show');