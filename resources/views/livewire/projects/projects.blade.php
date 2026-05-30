<div class="projects-root">

    {{-- ══════════════════════════════════════════════════
     ESTILOS
══════════════════════════════════════════════════ --}}
    <style>
        /* ── Variables ─────────────────────────────────── */
        :root {
            --pr-bg: #f0f4f9;
            --pr-card: #ffffff;
            --pr-border: #e4e9f0;
            --pr-text: #0f172a;
            --pr-muted: #64748b;
            --pr-accent: #3b6ef6;
            --pr-accent2: #6366f1;
            --pr-success: #22c55e;
            --pr-warn: #f59e0b;
            --pr-danger: #ef4444;
            --pr-radius: 16px;
            --pr-shadow: 0 2px 8px rgba(0, 0, 0, .06), 0 8px 24px rgba(0, 0, 0, .04);
            --pr-shadow-h: 0 4px 16px rgba(59, 110, 246, .14), 0 12px 32px rgba(0, 0, 0, .08);
        }

        .dark-mode-active {
            --pr-bg: #0c1220;
            --pr-card: #151f31;
            --pr-border: #1e2d47;
            --pr-text: #e8edf5;
            --pr-muted: #7a8fa6;
            --pr-accent: #4f7eff;
        }

        /* ── Wrapper de página ─────────────────────────── */
        .projects-root {
            background: var(--pr-bg);
            min-height: 100vh;
            padding: 28px 28px 60px;
            font-family: 'Segoe UI', system-ui, sans-serif;
            color: var(--pr-text);
            transition: background .3s, color .3s;
        }

        /* ── Header de página ──────────────────────────── */
        .pr-page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 24px;
        }

        .pr-page-title {
            font-size: 1.65rem;
            font-weight: 800;
            letter-spacing: -.03em;
            color: var(--pr-text);
            margin: 0;
        }

        .pr-page-title span {
            color: var(--pr-accent);
        }

        .pr-header-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        /* ── Botones ───────────────────────────────────── */
        .pr-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: 10px;
            border: none;
            font-size: .85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .18s ease;
            text-decoration: none;
            white-space: nowrap;
        }

        .pr-btn-primary {
            background: var(--pr-accent);
            color: #fff;
            box-shadow: 0 2px 8px rgba(59, 110, 246, .3);
        }

        .pr-btn-primary:hover {
            background: #2952d9;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(59, 110, 246, .4);
        }

        .pr-btn-ghost {
            background: var(--pr-card);
            color: var(--pr-muted);
            border: 1.5px solid var(--pr-border);
        }

        .pr-btn-ghost:hover {
            border-color: var(--pr-accent);
            color: var(--pr-accent);
            transform: translateY(-1px);
        }

        .pr-btn-danger-sm {
            background: rgba(239, 68, 68, .1);
            color: var(--pr-danger);
            border: 1.5px solid rgba(239, 68, 68, .2);
            padding: 5px 10px;
            font-size: .78rem;
            border-radius: 7px;
        }

        .pr-btn-danger-sm:hover {
            background: rgba(239, 68, 68, .18);
        }

        .pr-btn-success-sm {
            background: rgba(34, 197, 94, .1);
            color: var(--pr-success);
            border: 1.5px solid rgba(34, 197, 94, .2);
            padding: 5px 10px;
            font-size: .78rem;
            border-radius: 7px;
        }

        .pr-btn-success-sm:hover {
            background: rgba(34, 197, 94, .18);
        }

        .pr-btn-edit-sm {
            background: rgba(59, 110, 246, .1);
            color: var(--pr-accent);
            border: 1.5px solid rgba(59, 110, 246, .2);
            padding: 5px 10px;
            font-size: .78rem;
            border-radius: 7px;
        }

        .pr-btn-edit-sm:hover {
            background: rgba(59, 110, 246, .18);
        }

        .pr-btn-detail-sm {
            background: rgba(100, 116, 139, .1);
            color: var(--pr-muted);
            border: 1.5px solid rgba(100, 116, 139, .2);
            padding: 5px 10px;
            font-size: .78rem;
            border-radius: 7px;
        }

        .pr-btn-detail-sm:hover {
            background: rgba(100, 116, 139, .18);
        }

        /* ── Barra de búsqueda / filtros ───────────────── */
        .pr-toolbar {
            background: var(--pr-card);
            border: 1px solid var(--pr-border);
            border-radius: var(--pr-radius);
            padding: 14px 18px;
            margin-bottom: 22px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: space-between;
        }

        .pr-toolbar-left {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }

        .pr-toolbar-right {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }

        .pr-search-input {
            padding: 8px 14px;
            border: 1.5px solid var(--pr-border);
            border-radius: 9px;
            background: var(--pr-bg);
            color: var(--pr-text);
            font-size: .85rem;
            outline: none;
            transition: border-color .15s;
        }

        .pr-search-input:focus {
            border-color: var(--pr-accent);
        }

        .pr-search-input-sm {
            max-width: 280px;
        }

        .pr-search-input-id {
            width: 80px;
        }

        .pr-select {
            padding: 8px 12px;
            border: 1.5px solid var(--pr-border);
            border-radius: 9px;
            background: var(--pr-bg);
            color: var(--pr-text);
            font-size: .85rem;
            outline: none;
            cursor: pointer;
            transition: border-color .15s;
        }

        .pr-select:focus {
            border-color: var(--pr-accent);
        }

        .pr-check-label {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: .82rem;
            color: var(--pr-muted);
            cursor: pointer;
            user-select: none;
            white-space: nowrap;
        }

        .pr-check-label input {
            accent-color: var(--pr-accent);
            width: 15px;
            height: 15px;
            cursor: pointer;
        }

        /* ── Formulario inline de nuevo proyecto ───────── */
        .pr-new-project-panel {
            background: var(--pr-card);
            border: 1.5px solid var(--pr-border);
            border-radius: var(--pr-radius);
            padding: 22px 24px;
            margin-bottom: 22px;
            box-shadow: var(--pr-shadow);
        }

        .pr-new-project-panel h4 {
            margin: 0 0 16px;
            font-size: 1rem;
            font-weight: 700;
            color: var(--pr-text);
        }

        /* ── Grid de tarjetas ──────────────────────────── */
        .pr-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 18px;
        }

        /* ── Tarjeta individual ────────────────────────── */
        .pr-card {
            background: var(--pr-card);
            border: 1.5px solid var(--pr-border);
            border-radius: var(--pr-radius);
            box-shadow: var(--pr-shadow);
            display: flex;
            flex-direction: column;
            transition: box-shadow .2s ease, border-color .2s ease, transform .15s ease;
            overflow: hidden;
            position: relative;
        }

        .pr-card:hover {
            box-shadow: var(--pr-shadow-h);
            border-color: rgba(59, 110, 246, .25);
            transform: translateY(-2px);
        }

        .pr-card.is-deleted {
            opacity: .7;
            border-color: rgba(239, 68, 68, .25);
            background: rgba(239, 68, 68, .03);
        }

        .pr-card.is-editing {
            border-color: var(--pr-accent);
            box-shadow: 0 0 0 3px rgba(59, 110, 246, .1);
        }

        /* Franja superior de color por estado */
        .pr-card-stripe {
            height: 4px;
            width: 100%;
        }

        .stripe-active {
            background: linear-gradient(90deg, var(--pr-accent), var(--pr-accent2));
        }

        .stripe-archived {
            background: linear-gradient(90deg, #94a3b8, #64748b);
        }

        .stripe-deleted {
            background: linear-gradient(90deg, var(--pr-danger), #f97316);
        }

        /* Cuerpo de la tarjeta */
        .pr-card-body {
            padding: 18px 20px 14px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* Cabecera tarjeta: ID + badge + menú */
        .pr-card-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 8px;
        }

        .pr-card-id {
            font-size: .72rem;
            font-weight: 700;
            color: var(--pr-muted);
            letter-spacing: .04em;
        }

        .pr-card-badges {
            display: flex;
            gap: 5px;
            align-items: center;
            flex-wrap: wrap;
        }

        /* Badge de estado */
        .pr-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 9px;
            border-radius: 20px;
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .03em;
        }

        .pr-badge-active {
            background: rgba(34, 197, 94, .12);
            color: #16a34a;
            border: 1px solid rgba(34, 197, 94, .2);
        }

        .pr-badge-archived {
            background: rgba(100, 116, 139, .1);
            color: #475569;
            border: 1px solid rgba(100, 116, 139, .2);
        }

        .pr-badge-deleted {
            background: rgba(239, 68, 68, .1);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, .2);
        }

        .pr-badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }

        .pr-badge-active .pr-badge-dot {
            background: #16a34a;
        }

        .pr-badge-archived .pr-badge-dot {
            background: #475569;
        }

        .pr-badge-deleted .pr-badge-dot {
            background: #dc2626;
        }

        /* Nombre del proyecto */
        .pr-card-name {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--pr-text);
            text-decoration: none;
            line-height: 1.3;
            display: flex;
            align-items: flex-start;
            gap: 6px;
            transition: color .15s;
        }

        .pr-card-name:hover {
            color: var(--pr-accent);
        }

        .pr-card-name-arrow {
            opacity: 0;
            transform: translateX(-4px);
            transition: all .15s;
            font-size: .9rem;
            margin-top: 1px;
            flex-shrink: 0;
        }

        .pr-card:hover .pr-card-name-arrow {
            opacity: 1;
            transform: translateX(0);
        }

        /* Descripción */
        .pr-card-desc {
            font-size: .82rem;
            color: var(--pr-muted);
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin: 0;
        }

        /* Progreso de tareas */
        .pr-progress-wrap {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .pr-progress-bar-track {
            height: 6px;
            background: var(--pr-border);
            border-radius: 99px;
            overflow: hidden;
            display: flex;
        }

        .pr-progress-seg {
            height: 100%;
            transition: width .4s ease;
        }

        .pr-progress-seg-done {
            background: var(--pr-success);
        }

        .pr-progress-seg-progress {
            background: var(--pr-accent);
        }

        .pr-progress-seg-pending {
            background: var(--pr-warn);
        }

        .pr-progress-labels {
            display: flex;
            gap: 12px;
            font-size: .7rem;
            color: var(--pr-muted);
            flex-wrap: wrap;
        }

        .pr-progress-labels span {
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .pr-progress-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            display: inline-block;
        }

        /* Avatares de usuarios ─────────────────────────── */
        .pr-users-row {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .pr-users-label {
            font-size: .72rem;
            font-weight: 600;
            color: var(--pr-muted);
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-right: 2px;
        }

        .pr-avatars {
            display: flex;
            align-items: center;
        }

        .pr-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 2px solid var(--pr-card);
            object-fit: cover;
            margin-left: -8px;
            font-size: .68rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            text-transform: uppercase;
            position: relative;
            cursor: default;
            transition: transform .15s;
        }

        .pr-avatar:first-child {
            margin-left: 0;
        }

        .pr-avatar:hover {
            transform: translateY(-2px) scale(1.1);
            z-index: 5;
        }

        .pr-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .pr-avatar-more {
            background: var(--pr-bg);
            color: var(--pr-muted);
            border: 2px solid var(--pr-border);
            font-size: .65rem;
        }

        /* Pie de la tarjeta: acciones ──────────────────── */
        .pr-card-footer {
            border-top: 1px solid var(--pr-border);
            padding: 12px 20px;
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        /* Fecha expiración (soft deleted) */
        .pr-expires-tag {
            font-size: .72rem;
            color: var(--pr-danger);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Panel de gestión de usuarios (dentro de la tarjeta) */
        .pr-users-manager-panel {
            border-top: 1px solid var(--pr-border);
            padding: 12px 20px;
            background: rgba(59, 110, 246, .03);
        }

        .pr-users-manager-title {
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--pr-muted);
            margin-bottom: 8px;
        }

        /* Panel de edición inline ──────────────────────── */
        .pr-edit-panel {
            padding: 16px 20px;
            border-top: 1px solid var(--pr-border);
            background: rgba(59, 110, 246, .03);
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .pr-edit-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .pr-edit-col {
            flex: 1;
            min-width: 120px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .pr-edit-label {
            font-size: .72rem;
            font-weight: 600;
            color: var(--pr-muted);
        }

        .pr-edit-input,
        .pr-edit-select,
        .pr-edit-textarea {
            padding: 7px 10px;
            border: 1.5px solid var(--pr-border);
            border-radius: 8px;
            background: var(--pr-card);
            color: var(--pr-text);
            font-size: .85rem;
            outline: none;
            transition: border-color .15s;
            width: 100%;
            box-sizing: border-box;
        }

        .pr-edit-input:focus,
        .pr-edit-select:focus,
        .pr-edit-textarea:focus {
            border-color: var(--pr-accent);
        }

        .pr-edit-textarea {
            resize: none;
            min-height: 60px;
        }

        .pr-edit-actions {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }

        /* Estado vacío */
        .pr-empty {
            grid-column: 1/-1;
            text-align: center;
            padding: 60px 20px;
            color: var(--pr-muted);
        }

        .pr-empty-icon {
            font-size: 2.5rem;
            margin-bottom: 12px;
            opacity: .4;
        }

        .pr-empty-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .pr-empty-sub {
            font-size: .85rem;
        }

        /* Dropdown de etiquetas */
        .pr-label-dropdown .dropdown-menu {
            border-radius: 10px;
            border: 1.5px solid var(--pr-border);
            box-shadow: var(--pr-shadow-h);
            padding: 8px;
        }

        /* Paginador */
        .pr-pagination {
            margin-top: 24px;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .projects-root {
                padding: 16px 12px 40px;
            }

            .pr-grid {
                grid-template-columns: 1fr;
            }

            .pr-page-title {
                font-size: 1.3rem;
            }
        }
    </style>

    {{-- ══════════════════════════════════════════════════
     CABECERA
══════════════════════════════════════════════════ --}}
    <div class="pr-page-header">
        <h1 class="pr-page-title">Mis <span>Proyectos</span></h1>

        <div class="pr-header-actions">
            {{-- Botón etiquetas --}}
            <button type="button" class="pr-btn pr-btn-ghost" wire:click="$dispatch('open-label-modal')">
                <i class="fas fa-tag"></i>
                <span class="d-none d-sm-inline">Etiquetas</span>
            </button>

            {{-- Botón nuevo proyecto --}}
            @if (!$showForm)
                <button type="button" class="pr-btn pr-btn-primary" wire:click="openForm" wire:loading.attr="disabled"
                    wire:target="openForm">
                    <span wire:loading.remove wire:target="openForm"><i class="fas fa-plus"></i> Nuevo proyecto</span>
                    <span wire:loading wire:target="openForm"><i class="fas fa-spinner fa-spin"></i> Abriendo…</span>
                </button>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
     FORMULARIO NUEVO PROYECTO
══════════════════════════════════════════════════ --}}
    @if ($showForm)
        <div class="pr-new-project-panel">
            <h4><i class="fas fa-folder-plus me-2" style="color:var(--pr-accent);"></i>Nuevo Proyecto</h4>
            <livewire:project-form />
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════
     BARRA DE FILTROS / BÚSQUEDA
══════════════════════════════════════════════════ --}}
    <div class="pr-toolbar">
        <div class="pr-toolbar-left">
            <input type="text" class="pr-search-input pr-search-input-sm" placeholder="🔍 Buscar proyecto..."
                wire:model.live.debounce.400ms="searchText">
            <input type="text" class="pr-search-input pr-search-input-id" placeholder="ID"
                wire:model.live="searchId">

            {{-- Dropdown filtro etiquetas --}}
            <div class="dropdown pr-label-dropdown">
                <button class="pr-btn pr-btn-ghost btn" type="button" id="filterLabelsDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside"
                    style="padding:8px 14px; font-size:.82rem;">
                    <i class="fas fa-filter"></i>
                    Filtros @if (count($searchLabels) > 0)
                        ({{ count($searchLabels) }})
                    @endif
                </button>

                <ul class="dropdown-menu p-2 shadow" aria-labelledby="filterLabelsDropdown"
                    style="max-height: 250px; overflow-y: auto; min-width: 200px;">

                    @forelse($labels ?? [] as $label)
                        <li>
                            <div class="form-check m-1">
                                <input class="form-check-input" type="checkbox" value="{{ $label->id }}"
                                    id="filter-label-{{ $label->id }}" wire:model.live="searchLabels">
                                <label class="form-check-label" for="filter-label-{{ $label->id }}">
                                    {{ $label->name }}
                                </label>
                            </div>
                        </li>
                    @empty
                        <li><span class="dropdown-item-text text-muted small">No hay etiquetas disponibles.</span></li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="pr-toolbar-right">
            <select class="pr-select" wire:model.live="orderBy">
                <option value="status">Ordenar por estado</option>
                <option value="id_desc">Más recientes</option>
                <option value="id_asc">Más antiguos</option>
            </select>

            <select class="pr-select" wire:model.live="perPage" style="width:75px;">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>

            <label class="pr-check-label">
                <input type="checkbox" wire:model="showDeleted" wire:change="$refresh">
                Eliminados
            </label>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
     GRID DE TARJETAS
══════════════════════════════════════════════════ --}}
    <div class="pr-grid">
        @forelse ($projects as $project)
            @php
                $isDeleted = $project->trashed();
                $daysToKeep = config('prune.days_to_keep_deleted.' . \App\Models\Project::class, 60);
                $expiresAt = $isDeleted ? $project->deleted_at->copy()->addDays($daysToKeep) : null;
                $daysLeft = $isDeleted ? now()->diffInDays($expiresAt, false) : null;
                $isEditing = $editingProjectId == $project->id;
                $total = $project->total_tasks ?? 0;
                $donePct = $total > 0 ? round(($project->done_tasks * 100) / $total) : 0;
                $progPct = $total > 0 ? round(($project->in_progress_tasks * 100) / $total) : 0;
                $pendPct = $total > 0 ? round(($project->pending_tasks * 100) / $total) : 0;
            @endphp

            <div class="pr-card {{ $isDeleted ? 'is-deleted' : '' }} {{ $isEditing ? 'is-editing' : '' }}"
                wire:key="project-{{ $project->id }}">

                {{-- Franja de color --}}
                <div
                    class="pr-card-stripe {{ $isDeleted ? 'stripe-deleted' : ($project->status === 'archived' ? 'stripe-archived' : 'stripe-active') }}">
                </div>

                {{-- Cuerpo --}}
                <div class="pr-card-body">

                    {{-- ID + badge + acceso rápido --}}
                    <div class="pr-card-head">
                        <span class="pr-card-id">#{{ $project->id }}</span>
                        <div class="pr-card-badges">
                            @if ($isDeleted)
                                <span class="pr-badge pr-badge-deleted">
                                    <span class="pr-badge-dot"></span> Eliminado
                                </span>
                            @elseif ($project->status === 'archived')
                                <span class="pr-badge pr-badge-archived">
                                    <span class="pr-badge-dot"></span> Archivado
                                </span>
                            @else
                                <span class="pr-badge pr-badge-active">
                                    <span class="pr-badge-dot"></span> Activo
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Nombre (enlace o texto según modo) --}}
                    @if ($isEditing)
                        <input type="text" class="pr-edit-input" wire:model.defer="editingName"
                            placeholder="Nombre del proyecto">
                    @else
                        <a href="{{ route('projects.show', $project) }}" class="pr-card-name">
                            {{ $project->name }}
                            <span class="pr-card-name-arrow">→</span>
                        </a>
                    @endif

                    {{-- Descripción --}}
                    @if ($isEditing)
                        <textarea class="pr-edit-textarea" wire:model.defer="editingDescription" placeholder="Descripción (opcional)" x-data
                            x-init="$el.style.height = $el.scrollHeight + 'px'" x-on:input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"></textarea>
                    @elseif ($project->description)
                        <p class="pr-card-desc">{{ $project->description }}</p>
                    @endif

                    {{-- Estado en edición --}}
                    @if ($isEditing)
                        <div style="display:flex; gap:8px; align-items:center;">
                            <label class="pr-edit-label" style="white-space:nowrap;">Estado:</label>
                            <select class="pr-edit-select" wire:model.defer="editingStatus" style="max-width:160px;">
                                <option value="active">Activo</option>
                                <option value="archived">Archivado</option>
                            </select>
                        </div>
                    @endif

                    {{-- Progreso de tareas --}}
                    @if ($total > 0 && !$isEditing)
                        <div class="pr-progress-wrap">
                            <div class="pr-progress-bar-track">
                                <div class="pr-progress-seg pr-progress-seg-done"
                                    style="width:{{ $donePct }}%;"></div>
                                <div class="pr-progress-seg pr-progress-seg-progress"
                                    style="width:{{ $progPct }}%;"></div>
                                <div class="pr-progress-seg pr-progress-seg-pending"
                                    style="width:{{ $pendPct }}%;"></div>
                            </div>
                            <div class="pr-progress-labels">
                                <span><span class="pr-progress-dot" style="background:#22c55e;"></span>
                                    {{ $project->done_tasks }} hechas</span>
                                <span><span class="pr-progress-dot" style="background:#3b6ef6;"></span>
                                    {{ $project->in_progress_tasks }} en curso</span>
                                <span><span class="pr-progress-dot" style="background:#f59e0b;"></span>
                                    {{ $project->pending_tasks }} pendientes</span>
                            </div>
                        </div>
                    @endif

                    {{-- Fecha de expiración (solo si eliminado) --}}
                    @if ($isDeleted && $daysLeft !== null)
                        <div class="pr-expires-tag">
                            <i class="fas fa-clock"></i>
                            Expira en {{ max(0, (int) $daysLeft) }} días ({{ $expiresAt->format('d/m/Y') }})
                        </div>
                    @endif

                    {{-- Avatares de usuarios ─────────────────────── --}}
                    @if (!$isEditing && $project->users->count() > 0)
                        @php
                            $visibleUsers = $project->users->take(5);
                            $extraCount = max(0, $project->users->count() - 5);
                        @endphp
                        <div class="pr-users-row">
                            <span class="pr-users-label">Equipo</span>
                            <div class="pr-avatars">
                                @foreach ($visibleUsers as $pUser)
                                    @php
                                        // Genera color de fondo determinista basado en el ID
                                        $colors = [
                                            '#3b6ef6',
                                            '#6366f1',
                                            '#22c55e',
                                            '#f59e0b',
                                            '#ef4444',
                                            '#06b6d4',
                                            '#8b5cf6',
                                            '#ec4899',
                                        ];
                                        $bg = $colors[$pUser->id % count($colors)];
                                        $initials = collect(explode(' ', $pUser->name))
                                            ->map(fn($p) => strtoupper(substr($p, 0, 1)))
                                            ->take(2)
                                            ->implode('');
                                    @endphp
                                    <div class="pr-avatar" title="{{ $pUser->name }}"
                                        style="background:{{ $bg }}; color:#fff; z-index:{{ 10 - $loop->index }};">
                                        @if ($pUser->profile_photo_path)
                                            <img src="{{ asset('storage/' . $pUser->profile_photo_path) }}"
                                                alt="{{ $pUser->name }}">
                                        @else
                                            {{ $initials }}
                                        @endif
                                    </div>
                                @endforeach
                                @if ($extraCount > 0)
                                    <div class="pr-avatar pr-avatar-more" title="{{ $extraCount }} más">
                                        +{{ $extraCount }}</div>
                                @endif
                            </div>
                        </div>
                    @endif

                </div>{{-- /pr-card-body --}}

                {{-- ── Gestión de usuarios (solo admin/creador, no en edición) ── --}}
                @if (!$isEditing && !$isDeleted && (auth()->user()->isAdmin() || $project->created_by === auth()->id()))
                    <div class="pr-users-manager-panel">
                        <div class="pr-users-manager-title"><i class="fas fa-users me-1"></i> Gestión del equipo</div>
                        <livewire:project-users-manager :project="$project" :key="'users-' . $project->id" />
                    </div>
                @endif

                {{-- ── Pie de acciones ── --}}
                <div class="pr-card-footer">
                    @if ($isEditing)
                        {{-- Acciones de edición --}}
                        <button type="button" class="pr-btn pr-btn-primary"
                            style="padding:7px 14px; font-size:.82rem;" wire:click="saveEdit"
                            wire:loading.attr="disabled" wire:target="saveEdit">
                            <span wire:loading.remove wire:target="saveEdit"><i class="fas fa-check"></i>
                                Guardar</span>
                            <span wire:loading wire:target="saveEdit"><i class="fas fa-spinner fa-spin"></i>
                                Guardando…</span>
                        </button>
                        <button type="button" class="pr-btn pr-btn-ghost"
                            style="padding:7px 14px; font-size:.82rem;" wire:click="cancelEdit">
                            Cancelar
                        </button>
                    @elseif ($isDeleted)
                        {{-- Acciones de eliminado --}}
                        <button type="button" class="pr-btn pr-btn-success-sm" x-data data-id="{{ $project->id }}"
                            data-title="Restaurar {{ $project->name }}"
                            data-message="Vas a restaurar el proyecto <i>{{ $project->name }}</i>."
                            data-action="restore-project"
                            x-on:click="$dispatch('confirm-restore', {id:$el.dataset.id, title:$el.dataset.title, message:$el.dataset.message, action:$el.dataset.action})">
                            <i class="fas fa-rotate-left"></i> Restaurar
                        </button>
                        <button type="button" class="pr-btn pr-btn-danger-sm" x-data data-id="{{ $project->id }}"
                            data-title="Eliminar {{ $project->name }}"
                            data-message="Se borrará <b>permanentemente</b>. Esta acción no se puede deshacer."
                            data-action="delete-project"
                            x-on:click="$dispatch('confirm-delete', {id:$el.dataset.id, title:$el.dataset.title, message:$el.dataset.message, action:$el.dataset.action})">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    @else
                        {{-- Acciones normales --}}
                        <a href="{{ route('projects.show', $project) }}" class="pr-btn pr-btn-detail-sm">
                            <i class="fas fa-eye"></i> Ver tareas
                        </a>
                        <button type="button" class="pr-btn pr-btn-edit-sm"
                            wire:click="startEdit({{ $project->id }})">
                            <i class="fas fa-pen"></i> Editar
                        </button>
                        <button type="button" class="pr-btn pr-btn-danger-sm" x-data data-id="{{ $project->id }}"
                            data-title="Eliminar {{ $project->name }}"
                            data-message="¿Seguro que quieres eliminar el proyecto <i>{{ $project->name }}</i>?"
                            data-action="delete-project"
                            x-on:click="$dispatch('confirm-delete', {id:$el.dataset.id, title:$el.dataset.title, message:$el.dataset.message, action:$el.dataset.action})">
                            <i class="fas fa-trash"></i>
                        </button>
                    @endif
                </div>

            </div>{{-- /pr-card --}}
        @empty
            <div class="pr-empty">
                <div class="pr-empty-icon"><i class="fas fa-folder-open"></i></div>
                <div class="pr-empty-title">No hay proyectos</div>
                <div class="pr-empty-sub">Crea tu primer proyecto con el botón de arriba.</div>
            </div>
        @endforelse
    </div>

    {{-- Paginador --}}
    <div class="pr-pagination">
        {{ $projects->links() }}
    </div>

    {{-- Modal de etiquetas --}}
    <livewire:label-create-modal />

</div>
