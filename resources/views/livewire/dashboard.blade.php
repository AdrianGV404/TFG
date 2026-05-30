{{-- ============================================================
     Dashboard — ProMaTi
     Bloques:
       1. Filtros (usuario + proyecto)
       2. Fila superior: Lista tareas+tiempo | Piechart estados
       3. Heatmap anual (historico_horas_dia)
       4. Últimas 15 tareas modificadas
     ============================================================ --}}

<style>
/* ── Variables ──────────────────────────────────────────── */
:root {
    --db-bg:        #f1f5f9;
    --db-card:      #ffffff;
    --db-border:    #e2e8f0;
    --db-text:      #0f172a;
    --db-muted:     #64748b;
    --db-accent:    #3b82f6;
    --db-radius:    14px;
    --db-shadow:    0 1px 4px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.04);

    /* heatmap */
    --hm-0: #f8fafc;
    --hm-1: #fef08a;   /* amarillo  0–3 h */
    --hm-2: #fb923c;   /* naranja   3–6 h */
    --hm-3: #ef4444;   /* rojo      >6 h  */
}

.dark-mode-active {
    --db-bg:     #0f172a;
    --db-card:   #1e293b;
    --db-border: #334155;
    --db-text:   #f1f5f9;
    --db-muted:  #94a3b8;
    --db-accent: #60a5fa;
    --db-shadow: 0 1px 4px rgba(0,0,0,.3), 0 4px 16px rgba(0,0,0,.2);

    --hm-0: #1e293b;
    --hm-1: #854d0e;
    --hm-2: #c2410c;
    --hm-3: #991b1b;
}

/* ── Wrapper ────────────────────────────────────────────── */
.db-wrap {
    background: var(--db-bg);
    min-height: 100vh;
    padding: 28px 32px 60px;
    color: var(--db-text);
    transition: background .3s, color .3s;
    font-family: 'Segoe UI', system-ui, sans-serif;
}

/* ── Card ────────────────────────────────────────────────── */
.db-card {
    background: var(--db-card);
    border: 1px solid var(--db-border);
    border-radius: var(--db-radius);
    box-shadow: var(--db-shadow);
    transition: background .3s, border-color .3s;
}

/* ── Sección title ──────────────────────────────────────── */
.db-section-title {
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: var(--db-muted);
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 7px;
}
.db-section-title i { color: var(--db-accent); }

/* ── Filtros ────────────────────────────────────────────── */
.db-filters {
    background: var(--db-card);
    border: 1px solid var(--db-border);
    border-radius: var(--db-radius);
    padding: 16px 20px;
    margin-bottom: 24px;
}
.db-filters .form-label {
    font-size: .7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: var(--db-muted);
    margin-bottom: 5px;
}
.db-filters .form-select,
.db-filters .form-control {
    background: var(--db-bg);
    border-color: var(--db-border);
    color: var(--db-text);
    border-radius: 8px;
    font-size: .875rem;
    transition: border-color .15s;
}
.db-filters .form-select:focus {
    border-color: var(--db-accent);
    box-shadow: 0 0 0 3px rgba(59,130,246,.12);
}

/* ── KPI badge ──────────────────────────────────────────── */
.kpi-badge {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--db-accent), #6366f1);
    color: #fff;
    border-radius: 12px;
    padding: 14px 22px;
    min-width: 140px;
}
.kpi-badge .kpi-val {
    font-size: 2rem;
    font-weight: 800;
    line-height: 1;
}
.kpi-badge .kpi-lbl {
    font-size: .7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .07em;
    opacity: .85;
    margin-top: 4px;
}

/* ── Task time list ─────────────────────────────────────── */
.task-time-list {
    max-height: 320px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: var(--db-border) transparent;
}
.task-time-list::-webkit-scrollbar { width: 5px; }
.task-time-list::-webkit-scrollbar-thumb {
    background: var(--db-border);
    border-radius: 4px;
}

.ttl-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 0;
    border-bottom: 1px solid var(--db-border);
    font-size: .83rem;
}
.ttl-row:last-child { border-bottom: none; }
.ttl-id {
    font-size: .7rem;
    color: var(--db-muted);
    min-width: 32px;
}
.ttl-title {
    flex: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-weight: 500;
}
.ttl-hours {
    font-size: .78rem;
    font-weight: 700;
    color: var(--db-accent);
    white-space: nowrap;
}

/* ── Piechart ───────────────────────────────────────────── */
.pie-legend-row {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 7px 0;
    border-bottom: 1px solid var(--db-border);
    font-size: .82rem;
}
.pie-legend-row:last-child { border-bottom: none; }
.pie-dot {
    width: 11px; height: 11px;
    border-radius: 50%;
    flex-shrink: 0;
}
.pie-legend-label { flex: 1; }
.pie-legend-count {
    font-weight: 700;
    color: var(--db-text);
    min-width: 26px;
    text-align: right;
}
.pie-legend-pct {
    color: var(--db-muted);
    font-size: .72rem;
    min-width: 36px;
    text-align: right;
}

/* ── Heatmap ────────────────────────────────────────────── */
.hm-grid {
    display: flex;
    gap: 3px;
    overflow-x: auto;
    padding-bottom: 6px;
    scrollbar-width: thin;
    scrollbar-color: var(--db-border) transparent;
}
.hm-grid::-webkit-scrollbar { height: 4px; }
.hm-grid::-webkit-scrollbar-thumb {
    background: var(--db-border);
    border-radius: 2px;
}

.hm-col {
    display: flex;
    flex-direction: column;
    gap: 3px;
    flex-shrink: 0;
}
.hm-col-label {
    font-size: .58rem;
    color: var(--db-muted);
    text-align: center;
    height: 14px;
    line-height: 14px;
    white-space: nowrap;
}

.hm-cell {
    width: 13px;
    height: 13px;
    border-radius: 2px;
    cursor: pointer;
    position: relative;
    flex-shrink: 0;
    transition: transform .1s;
}
.hm-cell:hover { transform: scale(1.5); z-index: 10; }

.hm-0 { background: var(--hm-0); border: 1px solid var(--db-border); }
.hm-1 { background: var(--hm-1); }
.hm-2 { background: var(--hm-2); }
.hm-3 { background: var(--hm-3); }

/* Tooltip del heatmap — global, position:fixed para no quedar clippeado
   por ningún contenedor con overflow */
#hm-float-tooltip {
    display: none;
    position: fixed;
    background: var(--db-card);
    border: 1px solid var(--db-border);
    border-radius: 8px;
    box-shadow: 0 8px 24px rgba(0,0,0,.18);
    padding: 8px 11px;
    min-width: 180px;
    max-width: 260px;
    z-index: 9999;
    pointer-events: none;
    font-size: .73rem;
    color: var(--db-text);
    white-space: normal;
}

.hm-tooltip-date {
    font-weight: 700;
    margin-bottom: 5px;
    color: var(--db-accent);
    font-size: .75rem;
}
.hm-tooltip-row {
    display: flex;
    justify-content: space-between;
    gap: 8px;
    padding: 2px 0;
    border-bottom: 1px solid var(--db-border);
}
.hm-tooltip-row:last-child { border-bottom: none; }
.hm-tooltip-task {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 130px;
}
.hm-tooltip-hours {
    font-weight: 700;
    color: var(--db-accent);
    white-space: nowrap;
}

/* Leyenda heatmap */
.hm-legend {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 10px;
    font-size: .7rem;
    color: var(--db-muted);
}
.hm-legend-cell {
    width: 12px; height: 12px;
    border-radius: 2px;
}

/* ── Recent tasks ───────────────────────────────────────── */
.recent-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 14px;
    border-radius: 8px;
    cursor: pointer;
    transition: background .12s;
    text-decoration: none;
    color: inherit;
}
.recent-row:hover { background: rgba(59,130,246,.06); }

.recent-status-dot {
    width: 9px; height: 9px;
    border-radius: 50%;
    flex-shrink: 0;
}
.recent-title {
    flex: 1;
    font-size: .85rem;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.recent-meta {
    font-size: .72rem;
    color: var(--db-muted);
    white-space: nowrap;
}
.recent-project {
    font-size: .7rem;
    color: var(--db-muted);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 130px;
}

/* ── Loading overlay ────────────────────────────────────── */
.db-loading {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: .8rem;
    color: var(--db-muted);
}
</style>

<div class="db-wrap {{ auth()->user()->settings?->theme === 'dark' ? 'dark-mode-active' : '' }}">

    {{-- ── Contenedor de datos JSON para JS (Livewire lo actualiza en cada render) ──
         Las gráficas leen de aquí en cada rebuild para tener datos frescos tras
         cambiar filtros. Debe estar FUERA de cualquier wire:ignore.           --}}
    <div id="db-json-data"
         data-status='@json($taskStatusData)'
         data-heatmap='@json($heatmapData)'
         data-tooltips='@json($heatmapTooltips)'
         style="display:none;">
    </div>

    {{-- ══ CABECERA ══════════════════════════════════════════════════════════ --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="m-0 fw-bold" style="font-size:1.4rem; color:var(--db-text);">
                <i class="fas fa-chart-line me-2" style="color:var(--db-accent);"></i>
                Dashboard
            </h2>
            <p class="mb-0 mt-1" style="font-size:.78rem; color:var(--db-muted);">
                Métricas del tenant · {{ auth()->user()->tenant->name ?? '' }}
            </p>
        </div>
        <div wire:loading class="db-loading">
            <div class="spinner-border spinner-border-sm" role="status"></div>
            Actualizando…
        </div>
    </div>

    {{-- ══ FILTROS ════════════════════════════════════════════════════════════ --}}

    {{-- <div class="db-filters mb-4">
    <div class="row g-3 align-items-end">

        @if($isAdmin)
        <div class="col-md-4">
            <label class="form-label"><i class="fas fa-user me-1"></i> Miembro del equipo</label>
            <select wire:model="userId" class="form-select form-select-sm">
                <option value="">Todos los miembros</option>
                @foreach($users as $u)
                    <option value="{{ $u['id'] }}">{{ $u['name'] }}</option>
                @endforeach
            </select>
        </div>
        @else
        <div class="col-md-4">
            <label class="form-label"><i class="fas fa-user me-1"></i> Mostrando datos de</label>
            <input type="text" class="form-control form-control-sm" value="{{ auth()->user()->name }}" disabled readonly>
        </div>
        @endif

        <div class="col-md-4">
            <label class="form-label"><i class="fas fa-folder me-1"></i> Proyecto</label>
            <select wire:model="projectId" class="form-select form-select-sm">
                <option value="">Todos los proyectos</option>
                @foreach($projects as $p)
                    <option value="{{ $p['id'] }}">{{ $p['name'] }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <button type="button" wire:click="applyFilters" class="btn btn-primary btn-sm w-100" style="background: var(--db-accent); border: none;">
                <i class="fas fa-sync-alt me-1"></i> Aplicar filtros
            </button>
        </div>

        <div class="col-md-2 d-flex justify-content-center">
            <div class="kpi-badge">
                <div class="kpi-val">{{ $avgHoursPerTask }}</div>
                <div class="kpi-lbl">h / tarea</div>
            </div>
        </div>

    </div>
</div> --}}

    {{-- ══ FILA PRINCIPAL: lista tareas + piechart ════════════════════════════ --}}
    <div class="row g-3 mb-4">

        {{-- Lista tareas con tiempo ──────────────────────────────────────── --}}
        <div class="col-md-7">
            <div class="db-card p-4 h-100">
                <div class="db-section-title">
                    <i class="fas fa-stopwatch"></i> Tiempo por tarea
                </div>

                @if(count($taskTimeList) > 0)
                    <div class="task-time-list">
                        @foreach($taskTimeList as $t)
                        <div class="ttl-row">
                            <span class="ttl-id">#{{ $t['id'] }}</span>
                            <span class="ttl-title" title="{{ $t['title'] }}">{{ $t['title'] }}</span>
                            <span class="ttl-hours">{{ $t['horas'] }}h</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4" style="color:var(--db-muted); font-size:.85rem;">
                        <i class="fas fa-inbox d-block mb-2" style="font-size:1.6rem;"></i>
                        No hay tiempo registrado con los filtros actuales.
                    </div>
                @endif
            </div>
        </div>

        {{-- Piechart estados ─────────────────────────────────────────────── --}}
        <div class="col-md-5">
            <div class="db-card p-4 h-100">
                <div class="db-section-title">
                    <i class="fas fa-chart-pie"></i> Estado de tareas
                </div>

                @php $totalTasks = collect($taskStatusData)->sum('count'); @endphp

                @if($totalTasks > 0)
                    {{-- Canvas del donut --}}
                    <div style="height:180px; position:relative; margin-bottom:16px;" wire:ignore>
                        <canvas id="statusPieChart"></canvas>
                    </div>

                    {{-- Leyenda --}}
                    @foreach($taskStatusData as $s)
                        @if($s['count'] > 0)
                        <div class="pie-legend-row">
                            <div class="pie-dot" style="background:{{ $s['color'] }};"></div>
                            <span class="pie-legend-label">{{ $s['label'] }}</span>
                            <span class="pie-legend-count">{{ $s['count'] }}</span>
                            <span class="pie-legend-pct">{{ $s['percent'] }}%</span>
                        </div>
                        @endif
                    @endforeach
                @else
                    <div class="text-center py-4" style="color:var(--db-muted); font-size:.85rem;">
                        <i class="fas fa-inbox d-block mb-2" style="font-size:1.6rem;"></i>
                        Sin tareas.
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ══ HEATMAP ANUAL ══════════════════════════════════════════════════════ --}}
    <div class="db-card p-4 mb-4">
        <div class="db-section-title">
            <i class="fas fa-fire"></i> Actividad anual · horas imputadas
            @if($projectId)
                <span style="font-weight:400; color:var(--db-muted);">
                    — {{ collect($projects)->firstWhere('id', $projectId)['name'] ?? '' }}
                </span>
            @endif
        </div>

        @if(count($heatmapData) > 0)
        <div wire:ignore>
            <div class="hm-grid" id="hm-grid"></div>
        </div>

        <div class="hm-legend">
            <span>Menos</span>
            <div class="hm-legend-cell hm-0" style="border:1px solid var(--db-border);"></div>
            <div class="hm-legend-cell hm-1"></div>
            <div class="hm-legend-cell hm-2"></div>
            <div class="hm-legend-cell hm-3"></div>
            <span>Más</span>
            <span class="ms-3">
                <span style="background:var(--hm-1); display:inline-block; width:10px; height:10px; border-radius:2px;"></span> &lt;3h
                &nbsp;
                <span style="background:var(--hm-2); display:inline-block; width:10px; height:10px; border-radius:2px;"></span> 3–6h
                &nbsp;
                <span style="background:var(--hm-3); display:inline-block; width:10px; height:10px; border-radius:2px;"></span> &gt;6h
            </span>
        </div>
        @else
            <p style="color:var(--db-muted); font-size:.85rem;">
                No hay datos de actividad en el último año.
            </p>
        @endif
    </div>

    {{-- ══ ÚLTIMAS TAREAS MODIFICADAS ════════════════════════════════════════ --}}
    <div class="db-card p-4">
        <div class="db-section-title">
            <i class="fas fa-history"></i> Últimas tareas modificadas
        </div>

        @forelse($recentTasks as $task)
            @php
                $statusColors = [
                    'pending'     => '#f59e0b',
                    'in_progress' => '#3b82f6',
                    'on_hold'     => '#6b7280',
                    'testing'     => '#8b5cf6',
                    'done'        => '#22c55e',
                ];
                $statusLabels = [
                    'pending'     => 'Pendiente',
                    'in_progress' => 'En progreso',
                    'on_hold'     => 'En pausa',
                    'testing'     => 'En pruebas',
                    'done'        => 'Hecha',
                ];
                $color = $statusColors[$task->status] ?? '#64748b';
                $label = $statusLabels[$task->status] ?? $task->status;
            @endphp

            <a href="{{ route('tasks.show', $task) }}" class="recent-row">
                <div class="recent-status-dot" style="background:{{ $color }};"
                     title="{{ $label }}"></div>
                <div style="flex:1; min-width:0;">
                    <div class="recent-title">#{{ $task->id }} · {{ $task->title }}</div>
                    <div class="recent-project">{{ $task->project->name ?? '—' }}</div>
                </div>
                <div class="recent-meta">
                    {{ $task->updated_at->diffForHumans() }}
                </div>
                <i class="fas fa-chevron-right"
                   style="font-size:.65rem; color:var(--db-muted);"></i>
            </a>
        @empty
            <p style="color:var(--db-muted); font-size:.85rem; padding:8px 14px;">
                No hay tareas recientes.
            </p>
        @endforelse
    </div>

    {{-- Tooltip global del heatmap --}}
    <div id="hm-float-tooltip"></div>

</div>

{{-- ══ SCRIPTS ═════════════════════════════════════════════════════════════ --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    let pieChart = null;

    function getData() {
        const el = document.getElementById('db-json-data');
        if (!el) return { statusData: [], heatmapData: [], tooltipData: {} };
        try {
            return {
                statusData:  JSON.parse(el.getAttribute('data-status')  || '[]'),
                heatmapData: JSON.parse(el.getAttribute('data-heatmap') || '[]'),
                tooltipData: JSON.parse(el.getAttribute('data-tooltips')|| '{}'),
            };
        } catch (e) {
            console.error('[Dashboard] Error parseando JSON:', e);
            return { statusData: [], heatmapData: [], tooltipData: {} };
        }
    }

    function isDark() {
        return document.body.classList.contains('dark-mode-active');
    }

    function escHtml(str) {
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    const floatTip = document.getElementById('hm-float-tooltip');
    function tipShow(html) { floatTip.innerHTML = html; floatTip.style.display = 'block'; }
    function tipMove(e) {
        if (floatTip.style.display === 'none') return;
        const margin = 14, vw = window.innerWidth, vh = window.innerHeight;
        let x = e.clientX + margin, y = e.clientY - floatTip.offsetHeight - margin;
        if (x + floatTip.offsetWidth > vw - margin) x = e.clientX - floatTip.offsetWidth - margin;
        if (y < margin) y = e.clientY + margin;
        x = Math.max(margin, Math.min(x, vw - floatTip.offsetWidth - margin));
        y = Math.max(margin, Math.min(y, vh - floatTip.offsetHeight - margin));
        floatTip.style.left = x + 'px';
        floatTip.style.top = y + 'px';
    }
    function tipHide() { floatTip.style.display = 'none'; }

    function buildPie() {
        const canvas = document.getElementById('statusPieChart');
        if (!canvas) return;
        if (pieChart) { pieChart.destroy(); pieChart = null; }

        const { statusData } = getData();
        const active = statusData.filter(s => s.count > 0);
        if (active.length === 0) return;

        pieChart = new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: active.map(s => s.label),
                datasets: [{
                    data: active.map(s => s.count),
                    backgroundColor: active.map(s => s.color),
                    borderWidth: 0,
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: isDark() ? '#1e293b' : '#ffffff',
                        titleColor: isDark() ? '#f1f5f9' : '#0f172a',
                        bodyColor: isDark() ? '#94a3b8' : '#64748b',
                        borderColor: isDark() ? '#334155' : '#e2e8f0',
                        borderWidth: 1,
                        callbacks: { label: ctx => ` ${ctx.label}: ${ctx.raw} tareas` }
                    }
                }
            }
        });
    }

    function buildHeatmap() {
        const grid = document.getElementById('hm-grid');
        if (!grid) return;
        const { heatmapData, tooltipData } = getData();
        if (!heatmapData.length) return;
        grid.innerHTML = '';

        const firstDate = new Date(heatmapData[0].date + 'T00:00:00');
        const dayOfWeek = (firstDate.getDay() + 6) % 7;
        const weeks = [];
        let week = Array(dayOfWeek).fill(null);
        for (const cell of heatmapData) {
            week.push(cell);
            if (week.length === 7) { weeks.push(week); week = []; }
        }
        if (week.length) { while (week.length < 7) week.push(null); weeks.push(week); }

        const dayLabels = ['L','M','X','J','V','S','D'];
        const labelCol = document.createElement('div');
        labelCol.className = 'hm-col';
        labelCol.innerHTML = '<div class="hm-col-label"></div>';
        dayLabels.forEach(d => {
            const span = document.createElement('div');
            span.className = 'hm-col-label';
            span.style.height = '16px';
            span.textContent = d;
            labelCol.appendChild(span);
        });
        grid.appendChild(labelCol);

        let lastMonth = -1;
        weeks.forEach(weekCells => {
            const col = document.createElement('div');
            col.className = 'hm-col';
            const firstReal = weekCells.find(c => c !== null);
            let monthLabel = '';
            if (firstReal) {
                const m = new Date(firstReal.date + 'T00:00:00').getMonth();
                if (m !== lastMonth) {
                    lastMonth = m;
                    monthLabel = new Date(firstReal.date + 'T00:00:00').toLocaleString('es', { month: 'short' });
                }
            }
            const mlDiv = document.createElement('div');
            mlDiv.className = 'hm-col-label';
            mlDiv.textContent = monthLabel;
            col.appendChild(mlDiv);

            weekCells.forEach(cell => {
                const div = document.createElement('div');
                if (cell === null) {
                    div.style.width = '13px';
                    div.style.height = '13px';
                    div.style.flexShrink = '0';
                } else {
                    div.className = `hm-cell hm-${cell.level}`;
                    const dateObj = new Date(cell.date + 'T00:00:00');
                    const fmt = dateObj.toLocaleDateString('es', { day: 'numeric', month: 'short', year: 'numeric' });
                    let tipHtml = `<div class="hm-tooltip-date">${escHtml(fmt)} · ${cell.horas}h</div>`;
                    const entries = tooltipData[cell.date];
                    if (entries?.length) {
                        entries.forEach(e => {
                            tipHtml += `<div class="hm-tooltip-row"><span class="hm-tooltip-task" title="${escHtml(e.title)}">#${e.task_id} ${escHtml(e.title)}</span><span class="hm-tooltip-hours">${e.horas}h</span></div>`;
                        });
                    } else {
                        tipHtml += `<div style="color:var(--db-muted); font-size:.7rem;">Sin detalle</div>`;
                    }
                    div.addEventListener('mouseenter', ev => tipShow(tipHtml));
                    div.addEventListener('mousemove', tipMove);
                    div.addEventListener('mouseleave', tipHide);
                }
                col.appendChild(div);
            });
            grid.appendChild(col);
        });
        grid.scrollLeft = grid.scrollWidth;
    }

    function init() {
        buildPie();
        buildHeatmap();
    }

    // Escuchar el evento emitido desde el servidor al pulsar el botón
    document.addEventListener('filters-applied', () => {
        console.log('filters-applied recibido, redibujando...');
        setTimeout(init, 100);
    });

    // Fallback: si Livewire actualiza el DOM por cualquier motivo (Livewire 3)
    if (typeof Livewire !== 'undefined') {
        Livewire.hook('commit', ({ succeed }) => {
            succeed(() => setTimeout(init, 100));
        });
    } else {
        // Para Livewire 2 (legacy)
        document.addEventListener('livewire:load', () => {
            Livewire.hook('element.updated', () => setTimeout(init, 100));
        });
    }

    // Primera carga
    document.addEventListener('DOMContentLoaded', init);

    // Observer para modo oscuro
    const observer = new MutationObserver(() => setTimeout(buildPie, 80));
    observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });
})();
</script>