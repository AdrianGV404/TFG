@extends('layouts.app')

@section('content')
    <div class="container container-sm">
        <h1 class="page-title text-center">Crear proyecto</h1>

        <form action="{{ route('projects.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name">Nombre</label>
                <input type="text" id="name" name="name" placeholder="Nombre del proyecto" required>
            </div>

            <div class="form-group">
                <label for="description">Descripción</label>
                <textarea id="description" name="description" placeholder="Describe el proyecto"></textarea>
            </div>

            <div class="form-group">
                <label for="status">Estado</label>
                <select id="status" name="status">
                    <option value="active">Activo</option>
                    <option value="archived">Archivado</option>
                </select>
            </div>

            <div class="actions">
                <a href="{{ route('projects') }}" class="btn btn-secondary">
                    Volver
                </a>

                <button type="submit" class="btn btn-primary">
                    Guardar proyecto
                </button>
            </div>
        </form>
    </div>
@endsection
