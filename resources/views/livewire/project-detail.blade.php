<div class="container">

    {{-- HEADER --}}
    <div class="page-header">
        <h1>{{ $project->name }}</h1>
    </div>

    {{-- DESCRIPCIÓN --}}
    @if ($project->description)
        <p class="text-muted">
            {{ $project->description }}
        </p>
    @endif

    <hr>

    {{-- TAREAS --}}
    <livewire:tasks :project="$project" />

</div>
