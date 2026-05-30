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

    @if ($project->users->count())
        <div class="mb-2">
            <small class="text-muted">Usuarios asignados:</small>
            <br>
            @foreach ($project->users as $user)
                <span class="badge bg-secondary">
                    {{ $user->name }}
                </span>
            @endforeach
        </div>
    @endif
    <hr>

    {{-- TAREAS --}}
    <livewire:tasks :project="$project" />

</div>
