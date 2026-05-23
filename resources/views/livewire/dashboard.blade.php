<style>
/* ═══════════════════════════════════════════════════════════════
   DASHBOARD – Variables de tema (light / dark)
   ═══════════════════════════════════════════════════════════════ */
:root {
    --db-bg:          #f8fafc;
    --db-card:        #ffffff;
    --db-card-border: #e2e8f0;
    --db-text:        #1e293b;
    --db-muted:       #64748b;
    --db-accent:      #3b82f6;
    --db-input-bg:    #ffffff;
    --db-input-border:#cbd5e1;
    --db-row-hover:   #f1f5f9;
    --db-badge-pend:  #fef3c7;
    --db-badge-prog:  #dbeafe;
    --db-badge-done:  #dcfce7;
    --db-badge-pend-t:#92400e;
    --db-badge-prog-t:#1e40af;
    --db-badge-done-t:#166534;
    --chart-grid:     rgba(0,0,0,0.05);
    --chart-text:     #64748b;

    /* Heatmap */
    --hm-0: #eef2f7;
    --hm-1: #bfdbfe;
    --hm-2: #60a5fa;
    --hm-3: #3b82f6;
    --hm-4: #1d4ed8;
}

.dark-mode-active {
    --db-bg:          #0f172a;
    --db-card:        #1e293b;
    --db-card-border: #334155;
    --db-text:        #f1f5f9;
    --db-muted:       #94a3b8;
    --db-accent:      #60a5fa;
    --db-input-bg:    #0f172a;
    --db-input-border:#334155;
    --db-row-hover:   #263347;
    --db-badge-pend:  #451a03;
    --db-badge-prog:  #1e3a5f;
    --db-badge-done:  #14532d;
    --db-badge-pend-t:#fbbf24;
    --db-badge-prog-t:#93c5fd;
    --db-badge-done-t:#86efac;
    --chart-grid:     rgba(255,255,255,0.07);
    --chart-text:     #94a3b8;

    --hm-0: #1e293b;
    --hm-1: #1e3a5f;
    --hm-2: #1d4ed8;
    --hm-3: #3b82f6;
    --hm-4: #93c5fd;
}

/* ── Layout ────────────────────────────────────────────────── */
.db-wrapper {
    background: var(--db-bg);
    min-height: 100vh;
    padding: 28px 32px 48px;
    color: var(--db-text);
    transition: background .3s, color .3s;
}

/* ── Card base ─────────────────────────────────────────────── */
.db-card {
    background: var(--db-card);
    border: 1px solid var(--db-card-border);
    border-radius: 14px;
    padding: 20px 24px;
    transition: background .3s, border-color .3s;
}

/* ── Filtros ───────────────────────────────────────────────── */
.db-filters {
    background: var(--db-card);
    border: 1px solid var(--db-card-border);
    border-radius: 14px;
    padding: 16px 20px;
    margin-bottom: 24px;
}

.db-filters .form-label {
    color: var(--db-muted);
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    margin-bottom: 5px;
}

.db-filters .form-select,
.db-filters .form-control {
    background: var(--db-input-bg);
    border-color: var(--db-input-border);
    color: var(--db-text);
    border-radius: 8px;
    font-size: 0.875rem;
}

.db-filters .form-select:focus {
    border-color: var(--db-accent);
    box-shadow: 0 0 0 3px rgba(59,130,246,.15);
}

/* ── KPI Cards ─────────────────────────────────────────────── */
.kpi-card {
    background: var(--db-card);
    border: 1px solid var(--db-card-border);
    border-radius: 14px;
    padding: 20px 22px;
    display: flex;
    align-items: center;
    gap: 16px;
    transition: transform .15s, box-shadow .15s, background .3s;
}
.kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,.08);
}

.kpi-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}
.kpi-icon.blue   { background: rgba(59,130,246,.12); color: #3b82f6; }
.kpi-icon.green  { background: rgba(22,163,74,.12);  color: #16a34a; }
.kpi-icon.amber  { background: rgba(245,158,11,.12); color: #f59e0b; }
.kpi-icon.violet { background: rgba(139,92,246,.12); color: #8b5cf6; }

.kpi-label {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: var(--db-muted);
    margin-bottom: 2px;
}

.kpi-value {
    font-size: 1.65rem;
    font-weight: 800;
    color: var(--db-text);
    line-height: 1;
}

.kpi-sub {
    font-size: 0.72rem;
    color: var(--db-muted);
    margin-top: 2px;
}

/* ── Títulos de sección ────────────────────────────────────── */
.db-section-title {
    font-size: 0.78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: var(--db-muted);
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 7px;
}
.db-section-title i { color: var(--db-accent); }

/* ── Heatmap ───────────────────────────────────────────────── */
.heatmap-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.hm-cell {
    width: 26px; height: 26px;
    border-radius: 5px;
    cursor: default;
    transition: transform .1s;
    flex-shrink: 0;
}
.hm-cell:hover { transform: scale(1.25); }

.hm-0 { background: var(--hm-0); }
.hm-1 { background: var(--hm-1); }
.hm-2 { background: var(--hm-2); }
.hm-3 { background: var(--hm-3); }
.hm-4 { background: var(--hm-4); }

.heatmap-legend {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 12px;
    font-size: 0.72rem;
    color: var(--db-muted);
}
.heatmap-legend .hm-cell { width: 16px; height: 16px; }

/* ── Tareas recientes ──────────────────────────────────────── */
.task-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 11px 0;
    border-bottom: 1px solid var(--db-card-border);
    gap: 12px;
    transition: background .1s;
}
.task-row:last-child { border-bottom: none; }

.task-row-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--db-text);
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 260px;
}
.task-row-meta {
    font-size: 0.72rem;
    color: var(--db-muted);
}

.status-pill {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 700;
    white-space: nowrap;
    flex-shrink: 0;
}

.pill-pending   { background: var(--db-badge-pend); color: var(--db-badge-pend-t); }
.pill-progress  { background: var(--db-badge-prog); color: var(--db-badge-prog-t); }
.pill-done      { background: var(--db-badge-done); color: var(--db-badge-done-t); }

/* ── User breakdown ────────────────────────────────────────── */
.user-bar-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 13px;
}
.user-bar-row:last-child { margin-bottom: 0; }

.user-avatar {
    width: 32px; height: 32px;
    border-radius: 50%;
    background: var(--db-accent);
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.72rem;
    font-weight: 700;
    flex-shrink: 0;
}

.user-bar-track {
    flex: 1;
    height: 7px;
    background: var(--hm-0);
    border-radius: 4px;
    overflow: hidden;
}
.user-bar-fill {
    height: 100%;
    background: var(--db-accent);
    border-radius: 4px;
    transition: width .6s cubic-bezier(.4,0,.2,1);
}

.user-bar-hours {
    font-size: 0.78rem;
    font-weight: 600;
    color: var(--db-text);
    min-width: 40px;
    text-align: right;
}

/* ── Estado vacío ──────────────────────────────────────────── */
.db-empty {
    text-align: center;
    padding: 32px 16px;
    color: var(--db-muted);
}
.db-empty i { font-size: 2rem; margin-bottom: 8px; display: block; }
</style>

<div class="db-wrapper" wire:key="dashboard-root">

    {{-- ══════════════ HEADER ══════════════ --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="m-0 fw-bold" style="color:var(--db-text); font-size:1.35rem;">
                <i class="fas fa-chart-line me-2" style="color:var(--db-accent);"></i>
                Dashboard de Rendimiento
            </h2>
            <p class="mb-0 mt-1" style="font-size:0.8rem; color:var(--db-muted);">
                Última actualización: {{ now()->format('d/m/Y H:i') }}
            </p>
        </div>
        <div wire:loading class="d-flex align-items-center gap-2" style="color:var(--db-muted); font-size:.82rem;">
            <div class="spinner-border spinner-border-sm" role="status"></div>
            Actualizando…
        </div>
    </div>

    {{-- ══════════════ FILTROS ══════════════ --}}
    <div class="db-filters mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">
                    <i class="fas fa-calendar-alt me-1"></i> Rango temporal
                </label>
                <select wire:model.live="range" class="form-select form-select-sm">
                    <option value="week">Últimos 7 días</option>
                    <option value="month">Últimos 30 días</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">
                    <i class="fas fa-user me-1"></i> Miembro del equipo
                </label>
                @if(in_array(auth()->user()->role, ['admin', 'responsable']))
                    <select wire:model.live="userId" class="form-select form-select-sm">
                        <option value="">Todos los miembros</option>
                        @foreach($users as $user)
                            <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                        @endforeach
                    </select>
                @else
                    <input type="text" class="form-control form-control-sm"
                           value="{{ auth()->user()->name }}" disabled readonly>
                @endif
            </div>

            <div class="col-md-4">
                <label class="form-label">
                    <i class="fas fa-folder me-1"></i> Proyecto
                </label>
                <select wire:model.live="projectId" class="form-select form-select-sm">
                    <option value="">Todos los proyectos</option>
                    @foreach($projects as $project)
                        <option value="{{ $project['id'] }}">{{ $project['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- ══════════════ KPI CARDS ══════════════ --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon blue">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <div class="kpi-label">Horas totales</div>
                    <div class="kpi-value">{{ number_format($totalHours, 1) }}</div>
                    <div class="kpi-sub">horas registradas</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon green">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <div class="kpi-label">Completadas</div>
                    <div class="kpi-value">{{ $completedTasks }}</div>
                    <div class="kpi-sub">tareas finalizadas</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon amber">
                    <i class="fas fa-spinner"></i>
                </div>
                <div>
                    <div class="kpi-label">En progreso</div>
                    <div class="kpi-value">{{ $inProgressTasks }}</div>
                    <div class="kpi-sub">{{ $pendingTasks }} pendientes</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon violet">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div>
                    <div class="kpi-label">Media diaria</div>
                    <div class="kpi-value">{{ number_format($avgDailyHours, 1) }}</div>
                    <div class="kpi-sub">horas / día</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════ GRÁFICAS ══════════════ --}}
    <div class="row g-3 mb-4">
        {{-- Línea de tiempo --}}
        <div class="col-md-8">
            <div class="db-card h-100">
                <div class="db-section-title">
                    <i class="fas fa-wave-square"></i> Tiempo dedicado por jornada
                </div>
                <div style="height: 260px; position: relative;">
                    <canvas id="timeChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Donut estados --}}
        <div class="col-md-4">
            <div class="db-card h-100">
                <div class="db-section-title">
                    <i class="fas fa-tasks"></i> Estado de tareas
                </div>
                @php
                    $total = $completedTasks + $inProgressTasks + $pendingTasks;
                @endphp
                @if($total > 0)
                    <div style="height: 200px; position: relative;">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-1" style="font-size:.75rem; color:var(--db-muted);">
                            <span>✓ Completadas</span>
                            <span class="fw-bold" style="color:var(--db-text);">{{ $completedTasks }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1" style="font-size:.75rem; color:var(--db-muted);">
                            <span>⚡ En progreso</span>
                            <span class="fw-bold" style="color:var(--db-text);">{{ $inProgressTasks }}</span>
                        </div>
                        <div class="d-flex justify-content-between" style="font-size:.75rem; color:var(--db-muted);">
                            <span>○ Pendientes</span>
                            <span class="fw-bold" style="color:var(--db-text);">{{ $pendingTasks }}</span>
                        </div>
                    </div>
                @else
                    <div class="db-empty">
                        <i class="fas fa-inbox"></i>
                        Sin tareas en este período
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ══════════════ HEATMAP + ACTIVIDAD ══════════════ --}}
    <div class="row g-3 mb-4">
        {{-- Heatmap --}}
        <div class="col-md-6">
            <div class="db-card h-100">
                <div class="db-section-title">
                    <i class="fas fa-fire"></i> Mapa de actividad
                </div>

                @if(count($heatmapData) > 0)
                    <div class="heatmap-grid">
                        @foreach($heatmapData as $cell)
                            <div class="hm-cell hm-{{ $cell['level'] }}"
                                 data-bs-toggle="tooltip"
                                 title="{{ $cell['date'] }}: {{ $cell['hours'] }}h"></div>
                        @endforeach
                    </div>
                    <div class="heatmap-legend">
                        <span>Menos</span>
                        <div class="hm-cell hm-0"></div>
                        <div class="hm-cell hm-1"></div>
                        <div class="hm-cell hm-2"></div>
                        <div class="hm-cell hm-3"></div>
                        <div class="hm-cell hm-4"></div>
                        <span>Más</span>
                    </div>
                @else
                    <div class="db-empty">
                        <i class="fas fa-calendar-times"></i>
                        No hay registros de tiempo
                    </div>
                @endif
            </div>
        </div>

        {{-- Tareas recientes --}}
        <div class="col-md-6">
            <div class="db-card h-100">
                <div class="db-section-title">
                    <i class="fas fa-history"></i> Actividad reciente
                </div>

                @forelse($recentTasks as $task)
                    @php
                        $pillClass = match($task->status) {
                            'done'        => 'pill-done',
                            'in_progress' => 'pill-progress',
                            default       => 'pill-pending',
                        };
                        $pillLabel = match($task->status) {
                            'done'        => 'Hecha',
                            'in_progress' => 'En progreso',
                            default       => 'Pendiente',
                        };
                    @endphp
                    <div class="task-row">
                        <div style="min-width:0;">
                            <div class="task-row-title">{{ $task->title }}</div>
                            <div class="task-row-meta">
                                <i class="fas fa-folder-open me-1"></i>
                                {{ $task->project->name ?? '—' }}
                                &nbsp;·&nbsp;
                                {{ $task->updated_at->diffForHumans() }}
                            </div>
                        </div>
                        <span class="status-pill {{ $pillClass }}">{{ $pillLabel }}</span>
                    </div>
                @empty
                    <div class="db-empty">
                        <i class="fas fa-inbox"></i>
                        Sin actividad reciente
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ══════════════ DESGLOSE POR USUARIO (solo admin) ══════════════ --}}
    @if(in_array(auth()->user()->role, ['admin', 'responsable']) && count($userBreakdown) > 0)
    <div class="row g-3">
        <div class="col-12">
            <div class="db-card">
                <div class="db-section-title">
                    <i class="fas fa-users"></i> Horas por miembro del equipo
                </div>
                @foreach($userBreakdown as $member)
                    <div class="user-bar-row">
                        <div class="user-avatar">
                            {{ strtoupper(substr($member['name'], 0, 2)) }}
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div style="font-size:.8rem; font-weight:600; color:var(--db-text); margin-bottom:4px;">
                                {{ $member['name'] }}
                            </div>
                            <div class="user-bar-track">
                                <div class="user-bar-fill" style="width: {{ $member['percent'] }}%;"></div>
                            </div>
                        </div>
                        <div class="user-bar-hours">{{ $member['hours'] }}h</div>
                        <div style="font-size:.72rem; color:var(--db-muted); min-width:32px; text-align:right;">
                            {{ $member['percent'] }}%
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

</div>

{{-- ══════════════ CHART.JS ══════════════ --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('livewire:init', () => {
    let timeChart = null;
    let statusChart = null;

    function isDark() {
        return document.body.classList.contains('dark-mode-active');
    }

    function themeColors() {
        return {
            grid   : isDark() ? 'rgba(255,255,255,0.07)' : 'rgba(0,0,0,0.05)',
            text   : isDark() ? '#94a3b8' : '#64748b',
            tooltip: isDark() ? '#1e293b' : '#ffffff',
            accent : isDark() ? '#60a5fa' : '#3b82f6',
        };
    }

    function buildCharts() {
        const labels     = @json($chartLabels   ?? []);
        const timeData   = @json($chartData     ?? []);
        const statusData = @json($taskStatusData ?? []);
        const c          = themeColors();

        // ── Destruir anteriores ──────────────────────────────────────────────
        if (timeChart)   { timeChart.destroy();   timeChart   = null; }
        if (statusChart) { statusChart.destroy(); statusChart = null; }

        const ctxTime   = document.getElementById('timeChart');
        const ctxStatus = document.getElementById('statusChart');

        // ── Gráfico de línea ─────────────────────────────────────────────────
        if (ctxTime) {
            timeChart = new Chart(ctxTime, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Horas',
                        data: timeData,
                        borderColor: c.accent,
                        backgroundColor: isDark()
                            ? 'rgba(96,165,250,0.12)'
                            : 'rgba(59,130,246,0.10)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: c.accent,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: c.tooltip,
                            titleColor: isDark() ? '#f1f5f9' : '#1e293b',
                            bodyColor:  isDark() ? '#94a3b8' : '#64748b',
                            borderColor: isDark() ? '#334155' : '#e2e8f0',
                            borderWidth: 1,
                            padding: 10,
                            callbacks: {
                                label: ctx => ` ${ctx.parsed.y}h trabajadas`
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: c.grid },
                            ticks: { color: c.text, font: { size: 11 } }
                        },
                        y: {
                            beginAtZero: true,
                            suggestedMax: 8,
                            grid: { color: c.grid },
                            ticks: {
                                color: c.text,
                                font: { size: 11 },
                                callback: v => v + 'h'
                            }
                        }
                    }
                }
            });
        }

        // ── Gráfico donut ────────────────────────────────────────────────────
        if (ctxStatus && Object.values(statusData).some(v => v > 0)) {
            const COLORS = isDark()
                ? ['#f59e0b', '#60a5fa', '#4ade80']
                : ['#f59e0b', '#3b82f6', '#22c55e'];

            statusChart = new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(statusData),
                    datasets: [{
                        data: Object.values(statusData),
                        backgroundColor: COLORS,
                        borderWidth: 0,
                        hoverOffset: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: c.tooltip,
                            titleColor: isDark() ? '#f1f5f9' : '#1e293b',
                            bodyColor:  isDark() ? '#94a3b8' : '#64748b',
                            borderColor: isDark() ? '#334155' : '#e2e8f0',
                            borderWidth: 1,
                        }
                    }
                }
            });
        }

        // ── Activar tooltips Bootstrap ───────────────────────────────────────
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            if (el._bsTooltip) el._bsTooltip.dispose();
            el._bsTooltip = new bootstrap.Tooltip(el, { trigger: 'hover' });
        });
    }

    // Carga inicial
    buildCharts();

    // Re-dibujar tras cada actualización de Livewire (filtros)
    Livewire.hook('commit', ({ succeed }) => {
        succeed(() => setTimeout(buildCharts, 60));
    });

    // Re-dibujar si cambia el tema dark/light
    const observer = new MutationObserver(() => setTimeout(buildCharts, 60));
    observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });
});
</script>