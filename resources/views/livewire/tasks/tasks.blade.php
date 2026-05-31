<div class="tk-root" x-data="{ openModal: false }">

    {{-- ══════════════════════════════════════════════════
     ESTILOS
══════════════════════════════════════════════════ --}}
    <style>
        /* ── Variables ─────────────────────────────────── */
        :root {
            --tk-bg:        #f0f4f9;
            --tk-card:      #ffffff;
            --tk-border:    #e4e9f0;
            --tk-text:      #0f172a;
            --tk-muted:     #64748b;
            --tk-accent:    #3b6ef6;
            --tk-accent2:   #6366f1;
            --tk-success:   #22c55e;
            --tk-warn:      #f59e0b;
            --tk-danger:    #ef4444;
            --tk-purple:    #8b5cf6;
            --tk-gray:      #94a3b8;
            --tk-blue:      #3b82f6;
            --tk-radius:    14px;
            --tk-shadow:    0 2px 8px rgba(0,0,0,.06), 0 8px 24px rgba(0,0,0,.04);
            --tk-shadow-h:  0 4px 16px rgba(59,110,246,.14), 0 12px 32px rgba(0,0,0,.08);
        }

        .dark-mode-active {
            --tk-bg:     #0c1220;
            --tk-card:   #151f31;
            --tk-border: #1e2d47;
            --tk-text:   #e8edf5;
            --tk-muted:  #7a8fa6;
            --tk-accent: #4f7eff;
        }

        /* ── Wrapper ───────────────────────────────────── */
        .tk-root {
            background: var(--tk-bg);
            min-height: 100vh;
            padding: 24px 24px 60px;
            font-family: 'Segoe UI', system-ui, sans-serif;
            color: var(--tk-text);
            transition: background .3s, color .3s;
        }

        /* ── Cabecera ──────────────────────────────────── */
        .tk-page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 20px;
        }

        .tk-page-title {
            font-size: 1.45rem;
            font-weight: 800;
            letter-spacing: -.03em;
            color: var(--tk-text);
            margin: 0;
        }

        .tk-page-title span { color: var(--tk-accent); }

        .tk-header-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        /* ── Botones ───────────────────────────────────── */
        .tk-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 9px;
            border: none;
            font-size: .83rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .18s ease;
            text-decoration: none;
            white-space: nowrap;
        }

        .tk-btn-primary {
            background: var(--tk-accent);
            color: #fff;
            box-shadow: 0 2px 8px rgba(59,110,246,.28);
        }
        .tk-btn-primary:hover {
            background: #2952d9;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(59,110,246,.38);
        }

        .tk-btn-ghost {
            background: var(--tk-card);
            color: var(--tk-muted);
            border: 1.5px solid var(--tk-border);
        }
        .tk-btn-ghost:hover {
            border-color: var(--tk-accent);
            color: var(--tk-accent);
            transform: translateY(-1px);
        }

        .tk-btn-danger-sm {
            background: rgba(239,68,68,.1);
            color: var(--tk-danger);
            border: 1.5px solid rgba(239,68,68,.2);
            padding: 5px 10px;
            font-size: .77rem;
            border-radius: 7px;
        }
        .tk-btn-danger-sm:hover  { background: rgba(239,68,68,.18); }

        .tk-btn-success-sm {
            background: rgba(34,197,94,.1);
            color: #16a34a;
            border: 1.5px solid rgba(34,197,94,.2);
            padding: 5px 10px;
            font-size: .77rem;
            border-radius: 7px;
        }
        .tk-btn-success-sm:hover { background: rgba(34,197,94,.18); }

        .tk-btn-edit-sm {
            background: rgba(59,110,246,.1);
            color: var(--tk-accent);
            border: 1.5px solid rgba(59,110,246,.2);
            padding: 5px 10px;
            font-size: .77rem;
            border-radius: 7px;
        }
        .tk-btn-edit-sm:hover { background: rgba(59,110,246,.18); }

        .tk-btn-detail-sm {
            background: rgba(100,116,139,.1);
            color: var(--tk-muted);
            border: 1.5px solid rgba(100,116,139,.2);
            padding: 5px 10px;
            font-size: .77rem;
            border-radius: 7px;
        }
        .tk-btn-detail-sm:hover { background: rgba(100,116,139,.18); }

        /* ── Toolbar / barra de búsqueda ───────────────── */
        .tk-toolbar {
            background: var(--tk-card);
            border: 1px solid var(--tk-border);
            border-radius: var(--tk-radius);
            padding: 12px 16px;
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: calc(var(--navbar-height, 64px) + 12px);
            z-index: 40;
            box-shadow: var(--tk-shadow);
        }

        .tk-toolbar-left  { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
        .tk-toolbar-right { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }

        .tk-search-input {
            padding: 7px 12px;
            border: 1.5px solid var(--tk-border);
            border-radius: 8px;
            background: var(--tk-bg);
            color: var(--tk-text);
            font-size: .83rem;
            outline: none;
            transition: border-color .15s;
            width: 220px;
        }
        .tk-search-input:focus { border-color: var(--tk-accent); }
        .tk-search-id { width: 70px; }

        .tk-select {
            padding: 7px 10px;
            border: 1.5px solid var(--tk-border);
            border-radius: 8px;
            background: var(--tk-bg);
            color: var(--tk-text);
            font-size: .83rem;
            outline: none;
            cursor: pointer;
            transition: border-color .15s;
        }
        .tk-select:focus { border-color: var(--tk-accent); }

        .tk-check-label {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: .8rem;
            color: var(--tk-muted);
            cursor: pointer;
            user-select: none;
            white-space: nowrap;
        }
        .tk-check-label input {
            accent-color: var(--tk-accent);
            width: 14px;
            height: 14px;
            cursor: pointer;
        }

        /* ── Panel nuevo-tarea ─────────────────────────── */
        .tk-new-task-panel {
            background: var(--tk-card);
            border: 1.5px solid var(--tk-border);
            border-radius: var(--tk-radius);
            padding: 20px 22px;
            margin-bottom: 20px;
            box-shadow: var(--tk-shadow);
        }
        .tk-new-task-panel h4 {
            margin: 0 0 14px;
            font-size: .95rem;
            font-weight: 700;
            color: var(--tk-text);
        }

        /* ── Lista de filas ────────────────────────────── */
        .tk-grid {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        /* ── Tarjeta individual (fila) ─────────────────── */
        .tk-card {
            background: var(--tk-card);
            border: 1.5px solid var(--tk-border);
            border-radius: var(--tk-radius);
            box-shadow: var(--tk-shadow);
            display: flex;
            flex-direction: row;
            align-items: stretch;
            transition: box-shadow .2s ease, border-color .2s ease, transform .15s ease;
            overflow: hidden;
            position: relative;
        }
        .tk-card:hover {
            box-shadow: var(--tk-shadow-h);
            border-color: rgba(59,110,246,.22);
            transform: translateY(-1px);
        }
        .tk-card.is-deleted {
            opacity: .72;
            border-color: rgba(239,68,68,.25);
            background: rgba(239,68,68,.03);
        }

        /* Franja de color por estado (vertical, lado izquierdo) */
        .tk-card-stripe {
            width: 5px;
            flex-shrink: 0;
            align-self: stretch;
        }
        .stripe-pending     { background: linear-gradient(180deg, #f59e0b, #fbbf24); }
        .stripe-in_progress { background: linear-gradient(180deg, #3b82f6, #60a5fa); }
        .stripe-on_hold     { background: linear-gradient(180deg, #94a3b8, #cbd5e1); }
        .stripe-testing     { background: linear-gradient(180deg, #8b5cf6, #a78bfa); }
        .stripe-done        { background: linear-gradient(180deg, #22c55e, #4ade80); }
        .stripe-deleted     { background: linear-gradient(180deg, #ef4444, #f97316); }

        /* Cuerpo de tarjeta (fila) */
        .tk-card-body {
            padding: 12px 16px;
            flex: 1;
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 14px;
            min-width: 0;
            flex-wrap: wrap;
        }

        /* Cabecera: ID + badges */
        .tk-card-head {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .tk-card-id {
            font-size: .7rem;
            font-weight: 700;
            color: var(--tk-muted);
            letter-spacing: .05em;
        }

        .tk-card-badges {
            display: flex;
            gap: 5px;
            align-items: center;
            flex-wrap: wrap;
        }

        /* Badges de estado */
        .tk-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 8px;
            border-radius: 20px;
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .03em;
        }
        .tk-badge-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            display: inline-block;
        }

        /* Status badge colors */
        .tk-badge-pending    { background: rgba(245,158,11,.12); color: #92400e; border: 1px solid rgba(245,158,11,.25); }
        .tk-badge-pending    .tk-badge-dot { background: #f59e0b; }
        .tk-badge-in_progress { background: rgba(59,130,246,.12); color: #1e40af; border: 1px solid rgba(59,130,246,.25); }
        .tk-badge-in_progress .tk-badge-dot { background: #3b82f6; }
        .tk-badge-on_hold    { background: rgba(148,163,184,.15); color: #475569; border: 1px solid rgba(148,163,184,.3); }
        .tk-badge-on_hold    .tk-badge-dot { background: #94a3b8; }
        .tk-badge-testing    { background: rgba(139,92,246,.12); color: #6d28d9; border: 1px solid rgba(139,92,246,.25); }
        .tk-badge-testing    .tk-badge-dot { background: #8b5cf6; }
        .tk-badge-done       { background: rgba(34,197,94,.12); color: #15803d; border: 1px solid rgba(34,197,94,.25); }
        .tk-badge-done       .tk-badge-dot { background: #22c55e; }
        .tk-badge-deleted    { background: rgba(239,68,68,.1); color: #dc2626; border: 1px solid rgba(239,68,68,.2); }
        .tk-badge-deleted    .tk-badge-dot { background: #ef4444; }

        /* Título de la tarea */
        .tk-card-name {
            font-size: .95rem;
            font-weight: 700;
            color: var(--tk-text);
            text-decoration: none;
            line-height: 1.3;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: color .15s;
            flex: 1;
            min-width: 180px;
        }
        .tk-card-name:hover { color: var(--tk-accent); }
        .tk-card-name-arrow {
            opacity: 0;
            transform: translateX(-4px);
            transition: all .15s;
            font-size: .88rem;
            flex-shrink: 0;
        }
        .tk-card:hover .tk-card-name-arrow {
            opacity: 1;
            transform: translateX(0);
        }

        /* Descripción */
        .tk-card-desc {
            font-size: .78rem;
            color: var(--tk-muted);
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin: 0;
            max-width: 300px;
        }

        /* Etiquetas inline */
        .tk-card-labels {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            flex-shrink: 0;
        }
        .tk-label-chip {
            display: inline-block;
            padding: 2px 7px;
            font-size: .67rem;
            font-weight: 600;
            border-radius: 12px;
            background: rgba(99,102,241,.12);
            color: #4338ca;
            border: 1px solid rgba(99,102,241,.2);
        }
        .dark-mode-active .tk-label-chip {
            background: rgba(99,102,241,.2);
            color: #a5b4fc;
        }

        /* Due date */
        .tk-due-date {
            font-size: .75rem;
            color: var(--tk-muted);
            display: flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .tk-due-date.overdue { color: var(--tk-danger); font-weight: 600; }

        /* Inline status select in row */
        .tk-status-inline {
            padding: 4px 8px;
            border-radius: 20px;
            border: 1.5px solid transparent;
            font-size: .72rem;
            font-weight: 700;
            cursor: pointer;
            outline: none;
            transition: all .15s;
            flex-shrink: 0;
        }
        .tk-status-inline.pending     { background: rgba(245,158,11,.12); color: #92400e; border-color: rgba(245,158,11,.3); }
        .tk-status-inline.in_progress { background: rgba(59,130,246,.12); color: #1e40af; border-color: rgba(59,130,246,.3); }
        .tk-status-inline.on_hold     { background: rgba(148,163,184,.15); color: #475569; border-color: rgba(148,163,184,.35); }
        .tk-status-inline.testing     { background: rgba(139,92,246,.12); color: #6d28d9; border-color: rgba(139,92,246,.3); }
        .tk-status-inline.done        { background: rgba(34,197,94,.12); color: #15803d; border-color: rgba(34,197,94,.3); }
        /* Options get their own colors via JS workaround: color the option text */
        .tk-status-inline option.opt-pending     { background: #fffbeb; color: #92400e; }
        .tk-status-inline option.opt-in_progress { background: #eff6ff; color: #1e40af; }
        .tk-status-inline option.opt-on_hold     { background: #f8fafc; color: #475569; }
        .tk-status-inline option.opt-testing     { background: #f5f3ff; color: #5b21b6; }
        .tk-status-inline option.opt-done        { background: #f0fdf4; color: #15803d; }

        /* Expiración (eliminado) */
        .tk-expires-tag {
            font-size: .71rem;
            color: var(--tk-danger);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
            flex-shrink: 0;
        }

        /* Avatares ────────────────────────────────────── */
        .tk-users-row {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }
        .tk-users-label {
            font-size: .68rem;
            font-weight: 700;
            color: var(--tk-muted);
            text-transform: uppercase;
            letter-spacing: .05em;
        }
        .tk-avatars { display: flex; align-items: center; }
        .tk-avatar {
            width: 26px; height: 26px;
            border-radius: 50%;
            border: 2px solid var(--tk-card);
            object-fit: cover;
            margin-left: -7px;
            font-size: .63rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            text-transform: uppercase;
            position: relative;
            cursor: default;
            transition: transform .15s;
            overflow: hidden;
        }
        .tk-avatar:first-child { margin-left: 0; }
        .tk-avatar:hover { transform: translateY(-2px) scale(1.12); z-index: 5; }
        .tk-avatar img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
        .tk-avatar-more {
            background: var(--tk-bg);
            color: var(--tk-muted);
            border: 2px solid var(--tk-border);
            font-size: .6rem;
        }

        /* Prioridad mini-badge */
        .tk-priority-badge {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            padding: 2px 7px;
            border-radius: 10px;
            font-size: .67rem;
            font-weight: 700;
        }
        .tk-prio-critical { background: rgba(239,68,68,.12);  color: #b91c1c; border: 1px solid rgba(239,68,68,.25); }
        .tk-prio-high     { background: rgba(249,115,22,.12); color: #c2410c; border: 1px solid rgba(249,115,22,.25); }
        .tk-prio-mid      { background: rgba(234,179,8,.13);  color: #854d0e; border: 1px solid rgba(234,179,8,.28); }
        .tk-prio-low      { background: rgba(59,130,246,.11); color: #1d4ed8; border: 1px solid rgba(59,130,246,.24); }
        .tk-prio-none     { background: rgba(100,116,139,.1); color: #475569; border: 1px solid rgba(100,116,139,.2); }

        /* Pie de tarjeta (acciones en fila) */
        .tk-card-footer {
            border-left: 1px solid var(--tk-border);
            padding: 10px 14px;
            display: flex;
            gap: 7px;
            align-items: center;
            flex-direction: column;
            justify-content: center;
            flex-shrink: 0;
        }

        /* Estado vacío */
        .tk-empty {
            text-align: center;
            padding: 60px 20px;
            color: var(--tk-muted);
        }
        .tk-empty-icon { font-size: 2.4rem; margin-bottom: 10px; opacity: .4; }
        .tk-empty-title { font-size: 1rem; font-weight: 600; margin-bottom: 5px; }
        .tk-empty-sub { font-size: .83rem; }

        /* Paginador */
        .tk-pagination { margin-top: 22px; }

        /* Label dropdown */
        .tk-label-dropdown .dropdown-menu {
            border-radius: 10px;
            border: 1.5px solid var(--tk-border);
            box-shadow: var(--tk-shadow-h);
            padding: 8px;
        }

        /* Time-tracking */
        .tk-time-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: .7rem;
            color: var(--tk-muted);
            font-family: monospace;
            background: var(--tk-bg);
            border: 1px solid var(--tk-border);
            border-radius: 6px;
            padding: 2px 6px;
        }
        .tk-time-badge.running {
            color: var(--tk-danger);
            border-color: rgba(239,68,68,.25);
            background: rgba(239,68,68,.06);
            animation: tk-pulse 1.5s infinite;
        }

        @keyframes tk-pulse {
            0%, 100% { opacity: 1; }
            50%       { opacity: .55; }
        }

        /* Responsive */
        @media (max-width: 600px) {
            .tk-root { padding: 14px 10px 40px; }
            .tk-card-body { flex-wrap: wrap; gap: 8px; }
            .tk-card-footer { border-left: none; border-top: 1px solid var(--tk-border); flex-direction: row; width: 100%; }
            .tk-page-title { font-size: 1.2rem; }
        }

        /* ── Miembros del proyecto en cabecera ─────────── */
        .tk-project-members {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 8px;
        }
        .tk-project-members-label {
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--tk-muted);
            white-space: nowrap;
        }
        .tk-member-chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 3px 10px 3px 3px;
            background: var(--tk-card);
            border: 1.5px solid var(--tk-border);
            border-radius: 20px;
            font-size: .78rem;
            font-weight: 600;
            color: var(--tk-text);
            transition: border-color .15s, box-shadow .15s;
        }
        .tk-member-chip:hover {
            border-color: var(--tk-accent);
            box-shadow: 0 2px 8px rgba(59,110,246,.12);
        }
        .tk-member-chip-avatar {
            width: 22px; height: 22px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: .62rem;
            font-weight: 800;
            color: #fff;
            overflow: hidden;
            flex-shrink: 0;
            text-transform: uppercase;
        }
        .tk-member-chip-avatar img {
            width: 100%; height: 100%;
            border-radius: 50%; object-fit: cover;
        }
    </style>

    {{-- ══════════════════════════════════════════════════
     CABECERA
══════════════════════════════════════════════════ --}}
    <div class="tk-page-header">
        <div style="flex:1; min-width:0;">
            <h2 class="tk-page-title">Tareas del <span>proyecto</span></h2>

            {{-- Miembros del proyecto --}}
            @if (isset($project) && $project->users && $project->users->isNotEmpty())
                @php $memberColors = ['#3b6ef6','#6366f1','#22c55e','#f59e0b','#ef4444','#06b6d4','#8b5cf6','#ec4899']; @endphp
                <div class="tk-project-members">
                    <span class="tk-project-members-label">
                        <i class="fas fa-users" style="margin-right:3px;"></i>Equipo
                    </span>
                    @foreach ($project->users as $member)
                        @php
                            $mBg = $memberColors[$member->id % count($memberColors)];
                            $mInitials = collect(explode(' ', $member->name))
                                ->map(fn($p) => strtoupper(substr($p, 0, 1)))
                                ->take(2)->implode('');
                        @endphp
                        <div class="tk-member-chip" title="{{ $member->email }}">
                            <div class="tk-member-chip-avatar" style="background:{{ $mBg }};">
                                @if ($member->profile_photo_path)
                                    <img src="{{ asset('storage/' . $member->profile_photo_path) }}" alt="{{ $member->name }}">
                                @elseif (method_exists($member, 'profile_photo_url'))
                                    <img src="{{ $member->profile_photo_url }}" alt="{{ $member->name }}"
                                        onerror="this.style.display='none'; this.parentNode.innerText='{{ $mInitials }}'">
                                @else
                                    {{ $mInitials }}
                                @endif
                            </div>
                            {{ $member->name }}
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="tk-header-actions">
            @if (!$showForm)
                <button type="button" class="tk-btn tk-btn-primary"
                    wire:click="openForm" wire:loading.attr="disabled" wire:target="openForm">
                    <span wire:loading.remove wire:target="openForm">
                        <i class="fas fa-plus"></i> Nueva tarea
                    </span>
                    <span wire:loading wire:target="openForm">
                        <i class="fas fa-spinner fa-spin"></i> Abriendo…
                    </span>
                </button>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
     FORMULARIO NUEVA TAREA
══════════════════════════════════════════════════ --}}
    @if ($showForm)
        <div class="tk-new-task-panel">
            <h4><i class="fas fa-clipboard-list me-2" style="color:var(--tk-accent);"></i>Nueva Tarea</h4>
            <livewire:task-form :project="$project" wire:key="task-form-{{ $taskFormKey }}" />
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════
     TOOLBAR (filtros pegajosa)
══════════════════════════════════════════════════ --}}
    <div class="tk-toolbar">
        <div class="tk-toolbar-left">
            <input type="text" class="tk-search-input" placeholder="🔍 Buscar por título…"
                wire:model.live.debounce.400ms="searchText">

            <input type="text" class="tk-search-input tk-search-id" placeholder="ID"
                wire:model.live="searchId">

            {{-- Dropdown etiquetas --}}
            <div class="dropdown tk-label-dropdown">
                <button class="tk-btn tk-btn-ghost btn" type="button" id="tkFilterLabels"
                    data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside"
                    style="padding:7px 12px; font-size:.8rem;">
                    <i class="fas fa-filter"></i>
                    Etiquetas
                    @if (count($searchLabels ?? []) > 0)
                        <span style="background:var(--tk-accent);color:#fff;border-radius:99px;padding:0 5px;font-size:.65rem;">
                            {{ count($searchLabels) }}
                        </span>
                    @endif
                </button>
                <ul class="dropdown-menu p-2" aria-labelledby="tkFilterLabels"
                    style="max-height:240px; overflow-y:auto; min-width:190px;">
                    @forelse($labels as $label)
                        <li>
                            <div class="form-check m-1">
                                <input class="form-check-input" type="checkbox"
                                    value="{{ $label->id }}"
                                    id="tklbl-{{ $label->id }}"
                                    wire:model.live="searchLabels">
                                <label class="form-check-label" for="tklbl-{{ $label->id }}">
                                    {{ $label->name }}
                                </label>
                            </div>
                        </li>
                    @empty
                        <li><span class="dropdown-item-text text-muted small">No hay etiquetas.</span></li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="tk-toolbar-right">
            <select class="tk-select" wire:model.live="orderBy" style="min-width:170px;">
                <option value="priority">Prioridad</option>
                <option value="status">Estado</option>
                <option value="id_desc">Más recientes</option>
                <option value="id_asc">Más antiguos</option>
            </select>

            <select class="tk-select" wire:model.live="perPage" style="width:70px;">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>

            <label class="tk-check-label">
                <input type="checkbox" wire:model="showDeleted" wire:change="$refresh">
                Eliminadas
            </label>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
     GRID DE TARJETAS
══════════════════════════════════════════════════ --}}
    <div class="tk-grid">
        @forelse ($tasks as $task)
            @php
                $status    = $task->status;
                $priority  = $task->priority;
                $isDeleted = $task->trashed();
                $deletedAt = $isDeleted ? $task->deleted_at : null;
                $daysToKeep = config('prune.days_to_keep_deleted.' . \App\Models\Task::class, 60);
                $expiresAt  = $isDeleted ? $deletedAt->copy()->addDays($daysToKeep) : null;
                $daysLeft   = $isDeleted ? now()->diffInDays($expiresAt, false) : null;

                // Avatar helpers
                $avatarColors = ['#3b6ef6','#6366f1','#22c55e','#f59e0b','#ef4444','#06b6d4','#8b5cf6','#ec4899'];

                // Priority class helper
                $prioClass = match(true) {
                    $priority === 0        => 'tk-prio-critical',
                    $priority <= 3         => 'tk-prio-high',
                    $priority <= 6         => 'tk-prio-mid',
                    $priority <= 8         => 'tk-prio-low',
                    default                => 'tk-prio-none',
                };

                // Running time entry
                $runningEntry = ($task->timeEntries ?? collect())
                    ->where('user_id', auth()->id())
                    ->where('is_running', true)
                    ->first();

                // Status label map
                $statusLabels = [
                    'pending'     => 'Pendiente',
                    'in_progress' => 'En progreso',
                    'on_hold'     => 'En pausa',
                    'testing'     => 'En pruebas',
                    'done'        => 'Hecha',
                ];

                // Users assigned to the task (creator + assignees if relation exists)
                $taskUsers = collect();
                if (isset($task->creator) && $task->creator) {
                    $taskUsers->push($task->creator);
                }
                if (method_exists($task, 'assignees') && $task->relationLoaded('assignees')) {
                    foreach ($task->assignees as $u) {
                        if (!$taskUsers->contains('id', $u->id)) $taskUsers->push($u);
                    }
                }
            @endphp

            <div class="tk-card {{ $isDeleted ? 'is-deleted' : '' }}"
                wire:key="task-{{ $task->id }}"
                x-data="{ currentStatus: '{{ $status }}' }">

                {{-- Franja de color vertical --}}
                <div class="tk-card-stripe"
                    :class="'stripe-' + currentStatus + '{{ $isDeleted ? ' stripe-deleted' : '' }}'"></div>

                {{-- Cuerpo fila --}}
                <div class="tk-card-body">

                    {{-- ID + badge de estado (select inline) --}}
                    <div class="tk-card-head">
                        <span class="tk-card-id">#{{ $task->id }}</span>
                        @if ($isDeleted)
                            <span class="tk-badge tk-badge-deleted">
                                <span class="tk-badge-dot"></span> Eliminada
                            </span>
                        @else
                            <span class="tk-badge tk-badge-{{ $status }}">
                                <span class="tk-badge-dot"></span>
                                {{ $statusLabels[$status] ?? $status }}
                            </span>
                        @endif
                    </div>

                    {{-- Título --}}
                    @if (!$isDeleted)
                        <a href="{{ route('tasks.show', $task) }}" class="tk-card-name">
                            {{ $task->title }}
                            <span class="tk-card-name-arrow">→</span>
                        </a>
                    @else
                        <span class="tk-card-name" style="cursor:default;">{{ $task->title }}</span>
                    @endif

                    {{-- Descripción --}}
                    @if ($task->description)
                        <p class="tk-card-desc">{{ $task->description }}</p>
                    @endif

                    {{-- Etiquetas --}}
                    @if ($task->labels && $task->labels->isNotEmpty())
                        <div class="tk-card-labels">
                            @foreach ($task->labels as $lbl)
                                <span class="tk-label-chip">{{ $lbl->name }}</span>
                            @endforeach
                        </div>
                    @endif

                    {{-- Prioridad + fecha --}}
                    <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap; flex-shrink:0; margin-left:auto;">
                        <span class="tk-priority-badge {{ $prioClass }}">
                            ↑ {{ \App\Models\Task::PRIORITY_LABELS[$priority] ?? 'P'.$priority }}
                        </span>

                        @if ($task->due_date)
                            @php $isOverdue = $task->due_date->isPast() && !$task->isDone(); @endphp
                            <span class="tk-due-date {{ $isOverdue ? 'overdue' : '' }}">
                                <i class="fas fa-calendar-alt" style="font-size:.65rem;"></i>
                                {{ $task->due_date->format('d/m/Y') }}
                                @if ($isOverdue) ⚠️ @endif
                            </span>
                        @endif

                        @if ($runningEntry)
                            <span class="tk-time-badge running">● En curso</span>
                        @endif
                    </div>

                    {{-- Expiración si eliminada --}}
                    @if ($isDeleted && $daysLeft !== null)
                        <div class="tk-expires-tag">
                            <i class="fas fa-clock"></i>
                            Expira en {{ max(0, (int) $daysLeft) }} días ({{ $expiresAt->format('d/m/Y') }})
                        </div>
                    @endif

                    {{-- Avatares ────────────────────────────── --}}
                    @if ($taskUsers->isNotEmpty())
                        <div class="tk-users-row" style="flex-shrink:0;">
                            <div class="tk-avatars">
                                @foreach ($taskUsers->take(5) as $tUser)
                                    @php
                                        $bg = $avatarColors[$tUser->id % count($avatarColors)];
                                        $initials = collect(explode(' ', $tUser->name))
                                            ->map(fn($p) => strtoupper(substr($p, 0, 1)))
                                            ->take(2)->implode('');
                                    @endphp
                                    <div class="tk-avatar"
                                        title="{{ $tUser->name }}"
                                        style="background:{{ $bg }}; color:#fff; z-index:{{ 10 - $loop->index }};">
                                        @if ($tUser->profile_photo_path)
                                            <img src="{{ asset('storage/' . $tUser->profile_photo_path) }}"
                                                alt="{{ $tUser->name }}">
                                        @elseif (method_exists($tUser, 'profile_photo_url'))
                                            <img src="{{ $tUser->profile_photo_url }}" alt="{{ $tUser->name }}"
                                                onerror="this.style.display='none'">
                                        @else
                                            {{ $initials }}
                                        @endif
                                    </div>
                                @endforeach
                                @php $extra = max(0, $taskUsers->count() - 5); @endphp
                                @if ($extra > 0)
                                    <div class="tk-avatar tk-avatar-more" title="{{ $extra }} más">
                                        +{{ $extra }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                </div>{{-- /tk-card-body --}}

                {{-- Pie de acciones --}}
                <div class="tk-card-footer">
                    @if ($isDeleted)
                        {{-- Restaurar --}}
                        <button type="button" class="tk-btn tk-btn-success-sm" x-data
                            data-id="{{ $task->id }}"
                            data-title="Restaurar {{ $task->title }}"
                            data-message="Vas a restaurar la tarea <i>{{ $task->title }}</i>."
                            data-action="restore-task"
                            x-on:click="$dispatch('confirm-restore', {
                                id: $el.dataset.id,
                                title: $el.dataset.title,
                                message: $el.dataset.message,
                                action: $el.dataset.action
                            })">
                            <i class="fas fa-rotate-left"></i> Restaurar
                        </button>

                        {{-- Eliminar permanente --}}
                        <button type="button" class="tk-btn tk-btn-danger-sm" x-data
                            data-id="{{ $task->id }}"
                            data-title="Eliminar {{ $task->title }}"
                            data-message="Se borrará <b>permanentemente</b>. Esta acción no se puede deshacer."
                            data-action="delete-task"
                            x-on:click="$dispatch('confirm-delete', {
                                id: $el.dataset.id,
                                title: $el.dataset.title,
                                message: $el.dataset.message,
                                action: $el.dataset.action
                            })">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    @else
                        {{-- Ver detalle --}}
                        <a href="{{ route('tasks.show', $task) }}" class="tk-btn tk-btn-detail-sm">
                            <i class="fas fa-eye"></i> Ver
                        </a>

                        {{-- Grabar / detener tiempo --}}
                        @if ($runningEntry)
                            <button wire:click="toggleTimeTracking({{ $task->id }})"
                                class="tk-btn tk-btn-danger-sm">
                                <i class="fas fa-stop"></i> Detener
                            </button>
                        @else
                            <button wire:click="toggleTimeTracking({{ $task->id }})"
                                class="tk-btn tk-btn-success-sm">
                                <i class="fas fa-play"></i> Grabar
                            </button>
                        @endif

                        {{-- Tiempo manual --}}
                        <button type="button"
                            x-on:click="openModal = true; $wire.openManualTimeModal({{ $task->id }})"
                            class="tk-btn tk-btn-edit-sm" title="Añadir tiempo manual">
                            <i class="fas fa-clock"></i>
                        </button>

                        {{-- Eliminar soft --}}
                        <button type="button" class="tk-btn tk-btn-danger-sm" x-data
                            data-id="{{ $task->id }}"
                            data-title="Eliminar {{ $task->title }}"
                            data-message="¿Seguro que quieres eliminar la tarea <i>{{ $task->title }}</i>? Solo el administrador podrá deshacerlo."
                            data-action="delete-task"
                            x-on:click="$dispatch('confirm-delete', {
                                id: $el.dataset.id,
                                title: $el.dataset.title,
                                message: $el.dataset.message,
                                action: $el.dataset.action
                            })">
                            <i class="fas fa-trash"></i>
                        </button>
                    @endif
                </div>

            </div>{{-- /tk-card --}}

        @empty
            <div class="tk-empty">
                <div class="tk-empty-icon"><i class="fas fa-clipboard"></i></div>
                <div class="tk-empty-title">No hay tareas que coincidan</div>
                <div class="tk-empty-sub">Prueba a ajustar los filtros o crea la primera tarea.</div>
            </div>
        @endforelse
    </div>

    {{-- Paginador --}}
    <div class="tk-pagination">
        {{ $tasks->links() }}
    </div>

</div>