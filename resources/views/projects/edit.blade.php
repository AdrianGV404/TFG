@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar proyecto</h1>

    <form method="POST" action="{{ route('projects.update', $project) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Nombre</label>
            <input
                type="text"
                name="name"
                value="{{ old('name', $project->name) }}"
            >
        </div>

        <div class="form-group">
            <label>Descripción</label>
            <textarea name="description">{{ old('description', $project->description) }}</textarea>
        </div>

        <div class="form-group">
            <label>Estado</label>
            <select name="status">
                <option value="active" @selected($project->status === 'active')>Activo</option>
                <option value="archived" @selected($project->status === 'archived')>Archivado</option>
            </select>
        </div>

        <div class="actions">
            <button type="submit" class="btn btn-primary">
                Actualizar proyecto
            </button>

            <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary">
                Volver al proyecto
            </a>
        </div>
    </form>

    <hr>

    @include('projects._tasks', ['project' => $project])
</div>
@endsection
