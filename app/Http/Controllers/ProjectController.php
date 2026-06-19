<?php

namespace App\Http\Controllers;

use App\Models\Project;

class ProjectController extends Controller
{
    public function show(Project $project)
    {
        $this->authorize('view', $project);

        return view('livewire.partials.wrapper', [
            'component' => 'project-detail',
            'project' => $project,
        ]);
    }
}