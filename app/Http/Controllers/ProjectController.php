<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;


/**
 * Controlador web para el CRUD de proyectos.
 * Gestiona únicamente la interacción HTTP y las vistas Blade.
 */
class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::all();
        return view('projects.projects-page', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    /**
     * Guarda un nuevo proyecto en la base de datos.
     * La validación se realiza mediante FormRequest.
     */
    public function store(StoreProjectRequest $request)
    {
        Project::create($request->validated());

        return redirect()->route('projects.index');
    }

    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $project->update($request->validated());

        return redirect()->route('projects.index');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index');
    }

    public function show(Project $project)
    {
        return view('livewire.projects.projects', compact('project'));
    }
}
