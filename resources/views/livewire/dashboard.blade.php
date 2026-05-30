<style>
    /* ── Variables ──────────────────────────────────────────── */
    :root {
        --db-bg: #f1f5f9;
        --db-card: #ffffff;
        --db-border: #e2e8f0;
        --db-text: #0f172a;
        --db-muted: #64748b;
        --db-accent: #3b82f6;
        --db-radius: 14px;
        --db-shadow: 0 1px 4px rgba(0, 0, 0, .06), 0 4px 16px rgba(0, 0, 0, .04);

        /* heatmap */
        --hm-0: #f8fafc;
        --hm-1: #fef08a;
        --hm-2: #fb923c;
        --hm-3: #ef4444;
    }

    .dark-mode-active {
        --db-bg: #0f172a;
        --db-card: #1e293b;
        --db-border: #334155;
        --db-text: #f1f5f9;
        --db-muted: #94a3b8;
        --db-accent: #60a5fa;
        --db-shadow: 0 1px 4px rgba(0, 0, 0, .3), 0 4px 16px rgba(0, 0, 0, .2);

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

    .db-section-title i {
        color: var(--db-accent);
    }

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
        box-shadow: 0 0 0 3px rgba(59, 130, 246, .12);
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

    /* ── Task time list & Listados Generales ─────────────────── */
    .task-time-list,
    .label-stats-list,
    .recent-list-container {
        max-height: 380px;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: var(--db-border) transparent;
        padding-right: 6px;
    }

    .task-time-list::-webkit-scrollbar,
    .label-stats-list::-webkit-scrollbar,
    .recent-list-container::-webkit-scrollbar {
        width: 5px;
    }

    .task-time-list::-webkit-scrollbar-thumb,
    .label-stats-list::-webkit-scrollbar-thumb,
    .recent-list-container::-webkit-scrollbar-thumb {
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

    .ttl-row:last-child {
        border-bottom: none;
    }

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

    .pie-legend-row:last-child {
        border-bottom: none;
    }

    .pie-dot {
        width: 11px;
        height: 11px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .pie-legend-label {
        flex: 1;
    }

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

    .hm-grid::-webkit-scrollbar {
        height: 4px;
    }

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

    .hm-cell:hover {
        transform: scale(1.5);
        z-index: 10;
    }

    .hm-0 {
        background: var(--hm-0);
        border: 1px solid var(--db-border);
    }

    .hm-1 {
        background: var(--hm-1);
    }

    .hm-2 {
        background: var(--hm-2);
    }

    .hm-3 {
        background: var(--hm-3);
    }

    #hm-float-tooltip {
        display: none;
        position: fixed;
        background: var(--db-card);
        border: 1px solid var(--db-border);
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .18);
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

    .hm-tooltip-row:last-child {
        border-bottom: none;
    }

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

    .hm-legend {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-top: 10px;
        font-size: .7rem;
        color: var(--db-muted);
    }

    .hm-legend-cell {
        width: 12px;
        height: 12px;
        border-radius: 2px;
    }

    /* ── Label Stats (NUEVO) ────────────────────────────────── */
    .label-stat-card {
        border: 1px solid var(--db-border);
        border-radius: 8px;
        padding: 14px 16px;
        margin-bottom: 12px;
        background: var(--db-bg);
        transition: border-color .2s;
    }

    .label-stat-card:hover {
        border-color: var(--db-accent);
    }

    .label-stat-card:last-child {
        margin-bottom: 0;
    }

    .label-stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .label-stat-name {
        font-weight: 700;
        font-size: .88rem;
        color: var(--db-text);
    }

    .label-stat-meta {
        font-size: .82rem;
        color: var(--db-accent);
        font-weight: 700;
    }

    .label-stat-statuses {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        font-size: .72rem;
        color: var(--db-muted);
        font-weight: 500;
    }

    .stat-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--db-card);
        padding: 3px 8px;
        border-radius: 12px;
        border: 1px solid var(--db-border);
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

    .recent-row:hover {
        background: rgba(59, 130, 246, .06);
    }

    .recent-status-dot {
        width: 9px;
        height: 9px;
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

    {{-- JSON DATA --}}
    <div id="db-json-data" data-status='@json($taskStatusData)' data-heatmap='@json($heatmapData)'
        data-tooltips='@json($heatmapTooltips)' style="display:none;">
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

    {{-- ══ FILA PRINCIPAL: lista tareas + piechart ════════════════════════════ --}}
    <div class="row g-3 mb-4">
        {{-- Lista tareas con tiempo --}}
        <div class="col-md-7">
            <div class="db-card p-4 h-100">
                <div class="db-section-title">
                    <i class="fas fa-stopwatch"></i> Tiempo por tarea
                </div>

                @if (count($taskTimeList) > 0)
                    <div class="task-time-list" style="max-height: 320px;">
                        @foreach ($taskTimeList as $t)
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

        {{-- Piechart estados --}}
        <div class="col-md-5">
            <div class="db-card p-4 h-100">
                <div class="db-section-title">
                    <i class="fas fa-chart-pie"></i> Estado de tareas
                </div>

                @php $totalTasks = collect($taskStatusData)->sum('count'); @endphp

                @if ($totalTasks > 0)
                    <div style="height:180px; position:relative; margin-bottom:16px;" wire:ignore>
                        <canvas id="statusPieChart"></canvas>
                    </div>
                    @foreach ($taskStatusData as $s)
                        @if ($s['count'] > 0)
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
            @if ($projectId)
                <span style="font-weight:400; color:var(--db-muted);">
                    — {{ collect($projects)->firstWhere('id', $projectId)['name'] ?? '' }}
                </span>
            @endif
        </div>

        @if (count($heatmapData) > 0)
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
                    <span
                        style="background:var(--hm-1); display:inline-block; width:10px; height:10px; border-radius:2px;"></span>
                    &lt;3h
                    &nbsp;
                    <span
                        style="background:var(--hm-2); display:inline-block; width:10px; height:10px; border-radius:2px;"></span>
                    3–6h
                    &nbsp;
                    <span
                        style="background:var(--hm-3); display:inline-block; width:10px; height:10px; border-radius:2px;"></span>
                    &gt;6h
                </span>
            </div>
        @else
            <p style="color:var(--db-muted); font-size:.85rem;">
                No hay datos de actividad en el último año.
            </p>
        @endif
    </div>

    {{-- ══ FILA INFERIOR: ESTADÍSTICAS ETIQUETAS + TAREAS RECIENTES ══════════ --}}
    <div class="row g-3">

        {{-- ESTADÍSTICAS POR ETIQUETA (NUEVO) --}}
        <div class="col-md-7">
            <div class="db-card p-4 h-100">
                <div class="db-section-title">
                    <i class="fas fa-tags"></i> Estadísticas por Etiqueta
                </div>

                @if (count($labelStats) > 0)
                    <div class="label-stats-list">
                        @foreach ($labelStats as $ls)
                            <div class="label-stat-card">
                                <div class="label-stat-header">
                                    <span class="label-stat-name">
                                        <i class="fas fa-tag me-1"
                                            style="color:var(--db-accent); font-size:.75rem;"></i>
                                        {{ $ls['name'] }}
                                    </span>
                                    <span class="label-stat-meta">
                                        {{ $ls['total_hours'] }}h <span
                                            style="font-weight:400; color:var(--db-muted);">· {{ $ls['total_tasks'] }}
                                            tareas</span>
                                    </span>
                                </div>
                                <div class="label-stat-statuses">
                                    @if (($ls['statuses']['pending'] ?? 0) > 0)
                                        <span class="stat-badge"><span style="color:#f59e0b;">●</span>
                                            {{ $ls['statuses']['pending'] }} Pendientes</span>
                                    @endif
                                    @if (($ls['statuses']['in_progress'] ?? 0) > 0)
                                        <span class="stat-badge"><span style="color:#3b82f6;">●</span>
                                            {{ $ls['statuses']['in_progress'] }} En curso</span>
                                    @endif
                                    @if (($ls['statuses']['testing'] ?? 0) > 0)
                                        <span class="stat-badge"><span style="color:#8b5cf6;">●</span>
                                            {{ $ls['statuses']['testing'] }} En pruebas</span>
                                    @endif
                                    @if (($ls['statuses']['on_hold'] ?? 0) > 0)
                                        <span class="stat-badge"><span style="color:#6b7280;">●</span>
                                            {{ $ls['statuses']['on_hold'] }} En pausa</span>
                                    @endif
                                    @if (($ls['statuses']['done'] ?? 0) > 0)
                                        <span class="stat-badge"><span style="color:#22c55e;">●</span>
                                            {{ $ls['statuses']['done'] }} Completadas</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4" style="color:var(--db-muted); font-size:.85rem;">
                        <i class="fas fa-tag d-block mb-2" style="font-size:1.6rem; opacity:0.5;"></i>
                        No hay tareas etiquetadas con los filtros actuales.
                    </div>
                @endif
            </div>
        </div>

        {{-- ÚLTIMAS TAREAS MODIFICADAS --}}
        <div class="col-md-5">
            <div class="db-card p-4 h-100">
                <div class="db-section-title">
                    <i class="fas fa-history"></i> Últimas tareas
                </div>

                <div class="recent-list-container">
                    @forelse($recentTasks as $task)
                        @php
                            $statusColors = [
                                'pending' => '#f59e0b',
                                'in_progress' => '#3b82f6',
                                'on_hold' => '#6b7280',
                                'testing' => '#8b5cf6',
                                'done' => '#22c55e',
                            ];
                            $statusLabels = [
                                'pending' => 'Pendiente',
                                'in_progress' => 'En progreso',
                                'on_hold' => 'En pausa',
                                'testing' => 'En pruebas',
                                'done' => 'Hecha',
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
                            <i class="fas fa-chevron-right" style="font-size:.65rem; color:var(--db-muted);"></i>
                        </a>
                    @empty
                        <p style="color:var(--db-muted); font-size:.85rem; padding:8px 14px;">
                            No hay tareas recientes.
                        </p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    {{-- Tooltip global del heatmap --}}
    <div id="hm-float-tooltip"></div>

</div>

{{-- ══ SCRIPTS ═════════════════════════════════════════════════════════════ --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    (function() {
        let pieChart = null;

        function getData() {
            const el = document.getElementById('db-json-data');
            if (!el) return {
                statusData: [],
                heatmapData: [],
                tooltipData: {}
            };
            try {
                return {
                    statusData: JSON.parse(el.getAttribute('data-status') || '[]'),
                    heatmapData: JSON.parse(el.getAttribute('data-heatmap') || '[]'),
                    tooltipData: JSON.parse(el.getAttribute('data-tooltips') || '{}'),
                };
            } catch (e) {
                console.error('[Dashboard] Error parseando JSON:', e);
                return {
                    statusData: [],
                    heatmapData: [],
                    tooltipData: {}
                };
            }
        }

        function isDark() {
            return document.body.classList.contains('dark-mode-active');
        }

        function escHtml(str) {
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g,
                '&quot;');
        }

        const floatTip = document.getElementById('hm-float-tooltip');

        function tipShow(html) {
            floatTip.innerHTML = html;
            floatTip.style.display = 'block';
        }

        function tipMove(e) {
            if (floatTip.style.display === 'none') return;
            const margin = 14,
                vw = window.innerWidth,
                vh = window.innerHeight;
            let x = e.clientX + margin,
                y = e.clientY - floatTip.offsetHeight - margin;
            if (x + floatTip.offsetWidth > vw - margin) x = e.clientX - floatTip.offsetWidth - margin;
            if (y < margin) y = e.clientY + margin;
            x = Math.max(margin, Math.min(x, vw - floatTip.offsetWidth - margin));
            y = Math.max(margin, Math.min(y, vh - floatTip.offsetHeight - margin));
            floatTip.style.left = x + 'px';
            floatTip.style.top = y + 'px';
        }

        function tipHide() {
            floatTip.style.display = 'none';
        }

        function buildPie() {
            const canvas = document.getElementById('statusPieChart');
            if (!canvas) return;
            if (pieChart) {
                pieChart.destroy();
                pieChart = null;
            }

            const {
                statusData
            } = getData();
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
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: isDark() ? '#1e293b' : '#ffffff',
                            titleColor: isDark() ? '#f1f5f9' : '#0f172a',
                            bodyColor: isDark() ? '#94a3b8' : '#64748b',
                            borderColor: isDark() ? '#334155' : '#e2e8f0',
                            borderWidth: 1,
                            callbacks: {
                                label: ctx => ` ${ctx.label}: ${ctx.raw} tareas`
                            }
                        }
                    }
                }
            });
        }

        function buildHeatmap() {
            const grid = document.getElementById('hm-grid');
            if (!grid) return;
            const {
                heatmapData,
                tooltipData
            } = getData();
            if (!heatmapData.length) return;
            grid.innerHTML = '';

            const firstDate = new Date(heatmapData[0].date + 'T00:00:00');
            const dayOfWeek = (firstDate.getDay() + 6) % 7;
            const weeks = [];
            let week = Array(dayOfWeek).fill(null);
            for (const cell of heatmapData) {
                week.push(cell);
                if (week.length === 7) {
                    weeks.push(week);
                    week = [];
                }
            }
            if (week.length) {
                while (week.length < 7) week.push(null);
                weeks.push(week);
            }

            const dayLabels = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];
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
                        monthLabel = new Date(firstReal.date + 'T00:00:00').toLocaleString('es', {
                            month: 'short'
                        });
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
                        const fmt = dateObj.toLocaleDateString('es', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric'
                        });
                        let tipHtml =
                            `<div class="hm-tooltip-date">${escHtml(fmt)} · ${cell.horas}h</div>`;
                        const entries = tooltipData[cell.date];
                        if (entries?.length) {
                            entries.forEach(e => {
                                tipHtml +=
                                    `<div class="hm-tooltip-row"><span class="hm-tooltip-task" title="${escHtml(e.title)}">#${e.task_id} ${escHtml(e.title)}</span><span class="hm-tooltip-hours">${e.horas}h</span></div>`;
                            });
                        } else {
                            tipHtml +=
                                `<div style="color:var(--db-muted); font-size:.7rem;">Sin detalle</div>`;
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
            setTimeout(init, 100);
        });

        if (typeof Livewire !== 'undefined') {
            Livewire.hook('commit', ({
                succeed
            }) => {
                succeed(() => setTimeout(init, 100));
            });
        } else {
            document.addEventListener('livewire:load', () => {
                Livewire.hook('element.updated', () => setTimeout(init, 100));
            });
        }

        document.addEventListener('DOMContentLoaded', init);
        const observer = new MutationObserver(() => setTimeout(buildPie, 80));
        observer.observe(document.body, {
            attributes: true,
            attributeFilter: ['class']
        });
    })();
</script>
