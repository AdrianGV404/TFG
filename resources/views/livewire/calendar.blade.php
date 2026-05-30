{{-- ============================================================
     Calendario de Tareas — ProMaTi
     Muestra las tareas con fecha de vencimiento (due_date)
     agrupadas por día en una vista de mes navegable.
     ============================================================ --}}
<div>{{-- Raíz única requerida por Livewire --}}

<style>
/* ── Variables ──────────────────────────────────────────── */
:root {
    --cal-bg:          #f0f4f8;
    --cal-card:        #ffffff;
    --cal-border:      #e2e8f0;
    --cal-text:        #1a202c;
    --cal-muted:       #718096;
    --cal-accent:      #4f46e5;
    --cal-accent-soft: #eef2ff;
    --cal-today-bg:    #4f46e5;
    --cal-today-text:  #ffffff;
    --cal-header-bg:   #1a202c;
    --cal-header-text: #f7fafc;
    --cal-weekend:     #fef3f2;
    --cal-cell-min:    110px;
    --cal-radius:      14px;
    --cal-shadow:      0 4px 24px rgba(0,0,0,.07);

    /* colores de estado */
    --s-pending:     #f59e0b;
    --s-in_progress: #3b82f6;
    --s-on_hold:     #6b7280;
    --s-testing:     #8b5cf6;
    --s-done:        #22c55e;

    /* colores de prioridad */
    --p-veryhigh: #ef4444;
    --p-high:     #f97316;
    --p-mid:      #eab308;
    --p-low:      #60a5fa;
    --p-verylow:  #94a3b8;
}

.dark-mode-active {
    --cal-bg:          #0f172a;
    --cal-card:        #1e293b;
    --cal-border:      #334155;
    --cal-text:        #f1f5f9;
    --cal-muted:       #94a3b8;
    --cal-accent:      #818cf8;
    --cal-accent-soft: #1e1b4b;
    --cal-today-bg:    #6366f1;
    --cal-today-text:  #fff;
    --cal-header-bg:   #0f172a;
    --cal-header-text: #f1f5f9;
    --cal-weekend:     #1a1a2e;
    --cal-shadow:      0 4px 24px rgba(0,0,0,.3);
}

/* ── Wrapper ────────────────────────────────────────────── */
.cal-wrap {
    background: var(--cal-bg);
    min-height: 100vh;
    padding: 28px 20px 60px;
    font-family: 'Segoe UI', system-ui, sans-serif;
    color: var(--cal-text);
    transition: background .3s, color .3s;
}

/* ── Cabecera ────────────────────────────────────────────── */
.cal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--cal-header-bg);
    color: var(--cal-header-text);
    border-radius: var(--cal-radius);
    padding: 18px 28px;
    margin-bottom: 20px;
    box-shadow: var(--cal-shadow);
}

.cal-header h2 {
    margin: 0;
    font-size: 1.45rem;
    font-weight: 700;
    letter-spacing: -.02em;
    text-transform: capitalize;
}

.cal-header .month-year {
    font-size: .8rem;
    font-weight: 500;
    opacity: .6;
    margin-top: 2px;
}

.cal-nav-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 10px;
    border: 1px solid rgba(255,255,255,.15);
    background: rgba(255,255,255,.08);
    color: var(--cal-header-text);
    cursor: pointer;
    transition: background .15s, transform .1s;
    font-size: 1rem;
}
.cal-nav-btn:hover {
    background: rgba(255,255,255,.18);
    transform: scale(1.07);
}

.cal-today-btn {
    padding: 7px 16px;
    border-radius: 8px;
    border: 1px solid rgba(255,255,255,.2);
    background: rgba(255,255,255,.1);
    color: var(--cal-header-text);
    cursor: pointer;
    font-size: .8rem;
    font-weight: 600;
    letter-spacing: .04em;
    transition: background .15s;
}
.cal-today-btn:hover { background: rgba(255,255,255,.22); }

/* ── Días de semana ─────────────────────────────────────── */
.cal-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;
    margin-bottom: 4px;
}

.cal-weekday {
    text-align: center;
    font-size: .7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--cal-muted);
    padding: 6px 0;
}

/* ── Grid del mes ───────────────────────────────────────── */
.cal-grid {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.cal-week-row {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;
}

/* ── Celda de día ───────────────────────────────────────── */
.cal-cell {
    background: var(--cal-card);
    border: 1px solid var(--cal-border);
    border-radius: 10px;
    min-height: var(--cal-cell-min);
    padding: 8px;
    transition: border-color .15s, box-shadow .15s;
    overflow: hidden;
    position: relative;
}

.cal-cell:hover {
    border-color: var(--cal-accent);
    box-shadow: 0 2px 12px rgba(79,70,229,.12);
}

.cal-cell.empty {
    background: transparent;
    border: 1px dashed var(--cal-border);
    opacity: .4;
}

.cal-cell.weekend {
    background: var(--cal-weekend);
}

.cal-cell.today {
    border: 2px solid var(--cal-today-bg);
    box-shadow: 0 0 0 3px rgba(79,70,229,.12);
}

/* ── Número de día ───────────────────────────────────────── */
.cal-day-num {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    font-size: .82rem;
    font-weight: 700;
    color: var(--cal-muted);
    margin-bottom: 5px;
    flex-shrink: 0;
}

.cal-cell.today .cal-day-num {
    background: var(--cal-today-bg);
    color: var(--cal-today-text);
}

/* ── Chips de tarea ─────────────────────────────────────── */
.cal-tasks {
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.cal-task-chip {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 4px 7px;
    border-radius: 6px;
    font-size: .72rem;
    font-weight: 500;
    line-height: 1.3;
    text-decoration: none;
    transition: transform .1s, filter .15s, box-shadow .15s;
    cursor: pointer;
    overflow: hidden;
    border: 1px solid transparent;
    position: relative;
}

.cal-task-chip:hover {
    transform: translateY(-1px);
    filter: brightness(1.07);
    box-shadow: 0 3px 10px rgba(0,0,0,.13);
    z-index: 5;
}

.cal-task-chip .chip-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    flex-shrink: 0;
}

.cal-task-chip .chip-title {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    flex: 1;
}

/* Colores de chip por estado */
.chip-pending     { background: #fffbeb; color: #92400e; border-color: #fde68a; }
.chip-in_progress { background: #eff6ff; color: #1e40af; border-color: #bfdbfe; }
.chip-on_hold     { background: #f9fafb; color: #374151; border-color: #e5e7eb; }
.chip-testing     { background: #f5f3ff; color: #4c1d95; border-color: #ddd6fe; }
.chip-done        { background: #f0fdf4; color: #14532d; border-color: #bbf7d0; }

.dark-mode-active .chip-pending     { background: #451a03; color: #fcd34d; border-color: #78350f; }
.dark-mode-active .chip-in_progress { background: #1e3a5f; color: #93c5fd; border-color: #1d4ed8; }
.dark-mode-active .chip-on_hold     { background: #1f2937; color: #9ca3af; border-color: #374151; }
.dark-mode-active .chip-testing     { background: #2e1065; color: #c4b5fd; border-color: #4c1d95; }
.dark-mode-active .chip-done        { background: #052e16; color: #86efac; border-color: #166534; }

/* ── Barra lateral de prioridad ─────────────────────────── */
.cal-task-chip::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 3px;
    border-radius: 6px 0 0 6px;
}
.chip-p0::before  { background: var(--p-veryhigh); }
.chip-p1::before,
.chip-p2::before,
.chip-p3::before  { background: var(--p-high); }
.chip-p4::before,
.chip-p5::before,
.chip-p6::before  { background: var(--p-mid); }
.chip-p7::before,
.chip-p8::before  { background: var(--p-low); }
.chip-p9::before,
.chip-p10::before { background: var(--p-verylow); }

.cal-task-chip { padding-left: 11px; }

/* ── Overflow de tareas ──────────────────────────────────── */
.cal-overflow {
    margin-top: 3px;
    font-size: .68rem;
    color: var(--cal-accent);
    font-weight: 600;
    cursor: default;
    padding: 2px 4px;
    border-radius: 4px;
    background: var(--cal-accent-soft);
    display: inline-block;
}

/* ── Loading ────────────────────────────────────────────── */
.cal-loading {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: .8rem;
    color: var(--cal-muted);
}

/* ── Leyenda ────────────────────────────────────────────── */
.cal-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    padding: 14px 18px;
    background: var(--cal-card);
    border: 1px solid var(--cal-border);
    border-radius: var(--cal-radius);
    margin-bottom: 16px;
    font-size: .72rem;
    color: var(--cal-muted);
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 5px;
    font-weight: 500;
}

.legend-dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
}

/* ── Responsivo ─────────────────────────────────────────── */
@media (max-width: 768px) {
    :root { --cal-cell-min: 70px; }
    .cal-task-chip .chip-title { font-size: .65rem; }
    .cal-header h2 { font-size: 1.1rem; }
    .cal-cell { padding: 5px; }
    .cal-day-num { width: 22px; height: 22px; font-size: .75rem; }
}
</style>

<div class="cal-wrap {{ auth()->user()->settings?->theme === 'dark' ? 'dark-mode-active' : '' }}">

    {{-- ── CABECERA ──────────────────────────────────────────────────────── --}}
    <div class="cal-header">
        {{-- IMPORTANTE: Añadido type="button" a todos los botones --}}
        <button type="button" wire:click="previousMonth" class="cal-nav-btn" title="Mes anterior">
            ‹
        </button>

        <div class="text-center">
            <h2>{{ ucfirst($monthName) }}</h2>
            <div class="month-year">{{ $currentYear }}</div>
        </div>

        <div class="d-flex gap-2 align-items-center">
            <button type="button" wire:click="goToToday" class="cal-today-btn">Hoy</button>
            <button type="button" wire:click="nextMonth" class="cal-nav-btn" title="Mes siguiente">
                ›
            </button>
        </div>
    </div>

    {{-- ── LOADING ───────────────────────────────────────────────────────── --}}
    <div wire:loading class="cal-loading mb-3">
        <div class="spinner-border spinner-border-sm" role="status"></div>
        Cargando tareas…
    </div>

    {{-- ── LEYENDA DE ESTADOS ────────────────────────────────────────────── --}}
    <div class="cal-legend">
        <span style="font-weight:700; color:var(--cal-text); font-size:.72rem; margin-right:4px;">Estados:</span>
        <div class="legend-item"><div class="legend-dot" style="background:#f59e0b;"></div> Pendiente</div>
        <div class="legend-item"><div class="legend-dot" style="background:#3b82f6;"></div> En progreso</div>
        <div class="legend-item"><div class="legend-dot" style="background:#6b7280;"></div> En pausa</div>
        <div class="legend-item"><div class="legend-dot" style="background:#8b5cf6;"></div> En pruebas</div>
        <div class="legend-item"><div class="legend-dot" style="background:#22c55e;"></div> Hecha</div>
        <span class="ms-3" style="font-weight:700; color:var(--cal-text); font-size:.72rem; margin-right:4px;">Prioridad (barra izq.):</span>
        <div class="legend-item"><div style="width:3px;height:14px;border-radius:2px;background:#ef4444;"></div> Muy alta</div>
        <div class="legend-item"><div style="width:3px;height:14px;border-radius:2px;background:#f97316;"></div> Alta</div>
        <div class="legend-item"><div style="width:3px;height:14px;border-radius:2px;background:#eab308;"></div> Media</div>
        <div class="legend-item"><div style="width:3px;height:14px;border-radius:2px;background:#60a5fa;"></div> Baja</div>
        <div class="legend-item"><div style="width:3px;height:14px;border-radius:2px;background:#94a3b8;"></div> Muy baja</div>
    </div>

    {{-- ── DÍAS DE SEMANA ────────────────────────────────────────────────── --}}
    <div class="cal-weekdays">
        @foreach(['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'] as $i => $dayName)
            <div class="cal-weekday" style="{{ $i >= 5 ? 'color:#ef4444;' : '' }}">
                {{ $dayName }}
            </div>
        @endforeach
    </div>

    {{-- ── GRID DEL MES ──────────────────────────────────────────────────── --}}
    <div class="cal-grid" wire:loading.class="opacity-50">
        {{-- IMPORTANTE: Añadidos los wire:key para que Livewire sepa cómo actualizar el DOM --}}
        @foreach($weeks as $weekIndex => $week)
            <div class="cal-week-row" wire:key="week-{{ $currentYear }}-{{ $currentMonth }}-{{ $weekIndex }}">
                @foreach($week as $colIndex => $day)
                    @php
                        $cellClasses = ['cal-cell'];

                        if ($day === null) {
                            $cellClasses[] = 'empty';
                        } else {
                            $dateKey   = sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $day);
                            $isToday   = ($dateKey === $today);
                            $isWeekend = ($colIndex >= 5);

                            if ($isToday)   $cellClasses[] = 'today';
                            if ($isWeekend) $cellClasses[] = 'weekend';

                            $dayTasks   = $tasksByDay[$dateKey] ?? [];
                            $maxVisible = 3;
                            $overflow   = max(0, count($dayTasks) - $maxVisible);
                        }
                    @endphp

                    @if($day === null)
                        <div class="{{ implode(' ', $cellClasses) }}" wire:key="empty-{{ $currentYear }}-{{ $currentMonth }}-{{ $weekIndex }}-{{ $colIndex }}"></div>
                    @else
                        <div class="{{ implode(' ', $cellClasses) }}" wire:key="day-{{ $currentYear }}-{{ $currentMonth }}-{{ $day }}">
                            {{-- Número de día --}}
                            <div class="cal-day-num">{{ $day }}</div>

                            {{-- Chips de tareas --}}
                            @if(!empty($dayTasks))
                                <div class="cal-tasks">
                                    @foreach(array_slice($dayTasks, 0, $maxVisible) as $task)
                                        @php
                                            $statusColors = [
                                                'pending'     => '#f59e0b',
                                                'in_progress' => '#3b82f6',
                                                'on_hold'     => '#6b7280',
                                                'testing'     => '#8b5cf6',
                                                'done'        => '#22c55e',
                                            ];
                                            $dotColor   = $statusColors[$task['status']] ?? '#94a3b8';
                                            $chipClass  = 'chip-' . $task['status'];
                                            $prioClass  = 'chip-p' . $task['priority'];
                                        @endphp
                                        <a href="{{ route('tasks.show', $task['id']) }}"
                                           wire:key="task-{{ $task['id'] }}-{{ $currentYear }}-{{ $currentMonth }}"
                                           class="cal-task-chip {{ $chipClass }} {{ $prioClass }}"
                                           title="{{ $task['title'] }} ({{ $task['status'] }})">
                                            <div class="chip-dot" style="background:{{ $dotColor }};"></div>
                                            <span class="chip-title">{{ $task['title'] }}</span>
                                        </a>
                                    @endforeach

                                    @if($overflow > 0)
                                        <div class="cal-overflow">+{{ $overflow }} más</div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        @endforeach
    </div>

</div>
</div>{{-- /raíz --}}