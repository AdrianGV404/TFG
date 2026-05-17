<div class="container py-3">

    <h2>📊 Dashboard</h2>

    {{-- FILTROS --}}
    <div class="d-flex gap-2 mb-3">
        <button class="btn btn-sm btn-outline-primary"
                wire:click="setRange('week')">
            Semana
        </button>

        <button class="btn btn-sm btn-outline-primary"
                wire:click="setRange('month')">
            Mes
        </button>
    </div>

    {{-- KPIs --}}
    <div class="row mb-4">

        <div class="col">
            <div class="card p-3">
                <h6>Horas totales</h6>
                <strong>
                    {{ $timeData->sum('seconds') / 3600 }} h
                </strong>
            </div>
        </div>

        <div class="col">
            <div class="card p-3">
                <h6>Tareas recientes</h6>
                <strong>{{ $recentTasks->count() }}</strong>
            </div>
        </div>

    </div>

    {{-- GRAFICO SIMPLE (sin librerías aún) --}}
    <div class="card p-3 mb-3">
        <h5>📈 Tiempo por día</h5>

        @foreach($timeData as $row)
            <div>
                {{ $row->date }}:
                <strong>{{ round($row->seconds / 3600, 2) }}h</strong>
            </div>
        @endforeach
    </div>

    {{-- ESTADOS --}}
    <div class="card p-3 mb-3">
        <h5>📊 Estado de tareas</h5>

        @foreach($statusData as $status)
            <div>
                {{ $status->status }}: {{ $status->total }}
            </div>
        @endforeach
    </div>

    {{-- ACTIVIDAD RECIENTE --}}
    <div class="card p-3">
        <h5>🕒 Actividad reciente</h5>

        @foreach($recentTasks as $task)
            <div>
                • {{ $task->title }} ({{ $task->updated_at->diffForHumans() }})
            </div>
        @endforeach
    </div>

</div>