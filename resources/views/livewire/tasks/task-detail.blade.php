<div class="td-root">

    {{-- ══════════════════════════════════════════════════
     ESTILOS
══════════════════════════════════════════════════ --}}
    <style>
        /* ── Variables ─────────────────────────────────── */
        :root {
            --td-bg:        #f0f4f9;
            --td-card:      #ffffff;
            --td-border:    #e4e9f0;
            --td-text:      #0f172a;
            --td-muted:     #64748b;
            --td-accent:    #3b6ef6;
            --td-success:   #22c55e;
            --td-warn:      #f59e0b;
            --td-danger:    #ef4444;
            --td-purple:    #8b5cf6;
            --td-radius:    14px;
            --td-shadow:    0 2px 8px rgba(0,0,0,.06), 0 8px 24px rgba(0,0,0,.04);
        }

        .dark-mode-active {
            --td-bg:     #0c1220;
            --td-card:   #151f31;
            --td-border: #1e2d47;
            --td-text:   #e8edf5;
            --td-muted:  #7a8fa6;
            --td-accent: #4f7eff;
        }

        .td-root {
            background: var(--td-bg);
            min-height: 100vh;
            padding: 28px 24px 60px;
            font-family: 'Segoe UI', system-ui, sans-serif;
            color: var(--td-text);
            transition: background .3s, color .3s;
        }

        .td-inner {
            max-width: 900px;
            margin: 0 auto;
        }

        /* Breadcrumb */
        .td-breadcrumb {
            font-size: .8rem;
            color: var(--td-muted);
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 5px;
            flex-wrap: wrap;
        }
        .td-breadcrumb a { color: var(--td-muted); text-decoration: none; transition: color .15s; }
        .td-breadcrumb a:hover { color: var(--td-accent); }
        .td-breadcrumb .sep { opacity: .4; }

        /* Status stripe */
        .td-status-stripe {
            height: 4px;
            border-radius: 4px 4px 0 0;
            margin-bottom: 0;
        }
        .stripe-pending     { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
        .stripe-in_progress { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
        .stripe-on_hold     { background: linear-gradient(90deg, #94a3b8, #cbd5e1); }
        .stripe-testing     { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }
        .stripe-done        { background: linear-gradient(90deg, #22c55e, #4ade80); }
        .stripe-deleted     { background: linear-gradient(90deg, #ef4444, #f97316); }

        /* Page header */
        .td-page-header {
            background: var(--td-card);
            border: 1.5px solid var(--td-border);
            border-radius: var(--td-radius);
            box-shadow: var(--td-shadow);
            overflow: hidden;
            margin-bottom: 22px;
        }
        .td-page-header-body {
            padding: 20px 24px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .td-title {
            font-size: 1.55rem;
            font-weight: 800;
            letter-spacing: -.03em;
            margin: 0;
            line-height: 1.3;
            color: var(--td-text);
            flex: 1;
            min-width: 0;
        }
        .td-title-input {
            font-size: 1.4rem;
            font-weight: 700;
            padding: 8px 12px;
            border: 1.5px solid var(--td-accent);
            border-radius: 9px;
            background: var(--td-bg);
            color: var(--td-text);
            width: 100%;
            outline: none;
        }

        .td-header-actions { display: flex; gap: 8px; flex-wrap: wrap; flex-shrink: 0; align-items: flex-start; }

        /* Botones */
        .td-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 15px;
            border-radius: 9px;
            border: none;
            font-size: .83rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .18s ease;
            text-decoration: none;
            white-space: nowrap;
        }
        .td-btn-primary { background: var(--td-accent); color: #fff; box-shadow: 0 2px 8px rgba(59,110,246,.28); }
        .td-btn-primary:hover { background: #2952d9; transform: translateY(-1px); }
        .td-btn-ghost {
            background: var(--td-card);
            color: var(--td-muted);
            border: 1.5px solid var(--td-border);
        }
        .td-btn-ghost:hover { border-color: var(--td-accent); color: var(--td-accent); }
        .td-btn-danger-sm {
            background: rgba(239,68,68,.1);
            color: var(--td-danger);
            border: 1.5px solid rgba(239,68,68,.2);
            padding: 7px 12px;
            font-size: .8rem;
            border-radius: 8px;
        }
        .td-btn-danger-sm:hover { background: rgba(239,68,68,.18); }

        /* Layout 2 columnas */
        .td-layout {
            display: grid;
            grid-template-columns: 1fr 280px;
            gap: 20px;
            align-items: start;
        }
        @media (max-width: 720px) {
            .td-layout { grid-template-columns: 1fr; }
        }

        /* Tarjeta genérica */
        .td-card {
            background: var(--td-card);
            border: 1.5px solid var(--td-border);
            border-radius: var(--td-radius);
            box-shadow: var(--td-shadow);
            overflow: hidden;
        }
        .td-card + .td-card { margin-top: 16px; }

        .td-card-header {
            padding: 13px 18px 0;
            font-size: .7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: var(--td-muted);
        }
        .td-card-body { padding: 12px 18px 16px; }

        /* Textarea */
        .td-textarea {
            width: 100%;
            min-height: 100px;
            padding: 10px 12px;
            border: 1.5px solid var(--td-border);
            border-radius: 9px;
            background: var(--td-bg);
            color: var(--td-text);
            font-size: .9rem;
            resize: none;
            overflow: hidden;
            outline: none;
            transition: border-color .15s;
            box-sizing: border-box;
        }
        .td-textarea:focus { border-color: var(--td-accent); }

        /* ── LABEL SELECTOR MEJORADO ───────────────────── */
        .td-label-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            padding: 6px 0;
        }
        .td-label-toggle {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 11px;
            border-radius: 20px;
            font-size: .78rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .15s;
            border: 1.5px solid var(--td-border);
            background: var(--td-bg);
            color: var(--td-muted);
            user-select: none;
        }
        .td-label-toggle:hover {
            border-color: var(--td-accent);
            color: var(--td-accent);
        }
        .td-label-toggle.selected {
            background: rgba(59,110,246,.12);
            color: var(--td-accent);
            border-color: rgba(59,110,246,.35);
        }
        .td-label-toggle.selected .td-label-check { opacity: 1; }
        .td-label-check { opacity: 0; transition: opacity .12s; font-size: .75rem; }

        /* View-mode labels */
        .td-labels-view {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            padding: 4px 0;
        }
        .td-label-chip {
            display: inline-block;
            padding: 4px 10px;
            font-size: .75rem;
            font-weight: 600;
            border-radius: 14px;
            background: rgba(99,102,241,.12);
            color: #4338ca;
            border: 1px solid rgba(99,102,241,.22);
        }
        .dark-mode-active .td-label-chip {
            background: rgba(99,102,241,.22);
            color: #a5b4fc;
        }

        /* Select meta */
        .td-select {
            width: 100%;
            padding: 9px 11px;
            border: 1.5px solid var(--td-border);
            border-radius: 9px;
            background: var(--td-bg);
            color: var(--td-text);
            font-size: .85rem;
            outline: none;
            cursor: pointer;
            transition: border-color .15s;
            box-sizing: border-box;
        }
        .td-select:focus { border-color: var(--td-accent); }

        /* Status colors on select */
        .td-select.pending     { background: #fffbeb; color: #92400e; border-color: #fde68a; }
        .td-select.in_progress { background: #eff6ff; color: #1e40af; border-color: #bfdbfe; }
        .td-select.on_hold     { background: #f8fafc; color: #475569; border-color: #cbd5e1; }
        .td-select.testing     { background: #f5f3ff; color: #5b21b6; border-color: #ddd6fe; }
        .td-select.done        { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }

        /* Dark mode status */
        .dark-mode-active .td-select.pending     { background: rgba(245,158,11,.1); color: #fcd34d; border-color: rgba(245,158,11,.25); }
        .dark-mode-active .td-select.in_progress { background: rgba(59,130,246,.1); color: #93c5fd; border-color: rgba(59,130,246,.25); }
        .dark-mode-active .td-select.on_hold     { background: rgba(148,163,184,.08); color: #94a3b8; border-color: rgba(148,163,184,.25); }
        .dark-mode-active .td-select.testing     { background: rgba(139,92,246,.1); color: #c4b5fd; border-color: rgba(139,92,246,.25); }
        .dark-mode-active .td-select.done        { background: rgba(34,197,94,.1); color: #86efac; border-color: rgba(34,197,94,.25); }

        /* Colored options in status select */
        .td-select option.opt-pending     { background: #fffbeb; color: #92400e; }
        .td-select option.opt-in_progress { background: #eff6ff; color: #1e40af; }
        .td-select option.opt-on_hold     { background: #f8fafc; color: #475569; }
        .td-select option.opt-testing     { background: #f5f3ff; color: #5b21b6; }
        .td-select option.opt-done        { background: #f0fdf4; color: #15803d; }

        /* Meta label */
        .td-meta-label {
            font-size: .68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: var(--td-muted);
            margin-bottom: 6px;
            display: block;
        }

        /* Avatar creator */
        .td-creator {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .td-creator-avatar {
            width: 38px; height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--td-border);
            flex-shrink: 0;
        }
        .td-creator-name { font-weight: 700; font-size: .9rem; }
        .td-creator-email { font-size: .76rem; color: var(--td-muted); }

        /* Time section */
        .td-time-total {
            font-size: 1.4rem;
            font-family: 'JetBrains Mono', 'Fira Mono', monospace;
            font-weight: 700;
            color: var(--td-text);
            letter-spacing: .06em;
        }
        .td-time-running {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: .72rem;
            font-weight: 600;
            color: var(--td-danger);
            background: rgba(239,68,68,.08);
            border: 1px solid rgba(239,68,68,.22);
            border-radius: 20px;
            padding: 3px 9px;
            animation: td-pulse 1.5s infinite;
        }

        @keyframes td-pulse { 0%,100%{opacity:1} 50%{opacity:.5} }

        .td-time-controls { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 14px; align-items: center; }

        /* Big track button */
        .td-track-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 22px;
            border-radius: 12px;
            border: none;
            font-size: .92rem;
            font-weight: 700;
            cursor: pointer;
            transition: all .18s ease;
            text-decoration: none;
            white-space: nowrap;
            box-shadow: 0 3px 12px rgba(0,0,0,.1);
        }
        .td-track-btn-start {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: #fff;
            box-shadow: 0 4px 16px rgba(34,197,94,.35);
        }
        .td-track-btn-start:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(34,197,94,.45);
        }
        .td-track-btn-stop {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: #fff;
            box-shadow: 0 4px 16px rgba(239,68,68,.35);
            animation: td-track-glow 2s infinite;
        }
        .td-track-btn-stop:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239,68,68,.5);
        }
        @keyframes td-track-glow {
            0%,100% { box-shadow: 0 4px 16px rgba(239,68,68,.35); }
            50%      { box-shadow: 0 4px 24px rgba(239,68,68,.6); }
        }
        .td-track-btn-icon {
            width: 28px; height: 28px;
            background: rgba(255,255,255,.25);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: .9rem;
            flex-shrink: 0;
        }
        .td-track-btn-manual {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 10px 16px;
            border-radius: 10px;
            background: var(--td-card);
            color: var(--td-muted);
            border: 1.5px solid var(--td-border);
            font-size: .82rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .15s;
        }
        .td-track-btn-manual:hover {
            border-color: var(--td-accent);
            color: var(--td-accent);
            background: rgba(59,110,246,.05);
        }

        .td-time-table { width: 100%; border-collapse: collapse; font-size: .82rem; margin-top: 12px; }
        .td-time-table th {
            text-align: left;
            padding: 6px 8px;
            font-size: .68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--td-muted);
            border-bottom: 1px solid var(--td-border);
        }
        .td-time-table td {
            padding: 7px 8px;
            border-bottom: 1px solid var(--td-border);
            color: var(--td-text);
            vertical-align: middle;
        }
        .td-time-table tr:last-child td { border-bottom: none; }
        .td-time-avatar {
            width: 22px; height: 22px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 5px;
            vertical-align: middle;
        }

        /* Date input */
        .td-date-input {
            width: 100%;
            padding: 8px 11px;
            border: 1.5px solid var(--td-border);
            border-radius: 9px;
            background: var(--td-bg);
            color: var(--td-text);
            font-size: .85rem;
            outline: none;
            transition: border-color .15s;
            box-sizing: border-box;
        }
        .td-date-input:focus { border-color: var(--td-accent); }

        /* Due date display */
        .td-due-display { font-size: .9rem; font-weight: 600; }
        .td-due-display.overdue { color: var(--td-danger); }

        /* Divider */
        .td-divider { height: 1px; background: var(--td-border); margin: 10px 0; }

        /* Badge eliminada */
        .td-deleted-banner {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 13px;
            background: rgba(239,68,68,.1);
            color: #dc2626;
            border: 1px solid rgba(239,68,68,.25);
            border-radius: 20px;
            font-size: .8rem;
            font-weight: 700;
        }

        /* Timestamps */
        .td-ts { font-size: .8rem; color: var(--td-muted); }
        .td-ts strong { color: var(--td-text); }

        /* Modal */
        .td-modal-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,.5);
            z-index: 1000;
            display: flex; align-items: center; justify-content: center;
        }
        .td-modal {
            background: var(--td-card);
            border: 1.5px solid var(--td-border);
            border-radius: var(--td-radius);
            box-shadow: 0 20px 60px rgba(0,0,0,.2);
            padding: 26px 28px;
            width: 100%;
            max-width: 400px;
            color: var(--td-text);
        }
        .td-modal-title { font-size: 1.05rem; font-weight: 700; margin: 0 0 16px; }
        .td-modal-row { display: flex; gap: 14px; }
        .td-modal-col { flex: 1; }
        .td-modal-col label { font-size: .78rem; font-weight: 600; color: var(--td-muted); display: block; margin-bottom: 5px; }
        .td-modal-input {
            width: 100%;
            padding: 8px 10px;
            border: 1.5px solid var(--td-border);
            border-radius: 8px;
            background: var(--td-bg);
            color: var(--td-text);
            font-size: .9rem;
            outline: none;
            box-sizing: border-box;
        }
        .td-modal-input:focus { border-color: var(--td-accent); }
        .td-modal-footer { display: flex; gap: 8px; justify-content: flex-end; margin-top: 18px; }
    </style>

    <div class="td-inner">

        {{-- BREADCRUMB --}}
        <nav class="td-breadcrumb">
            <a href="{{ route('projects') }}">Proyectos</a>
            <span class="sep">/</span>
            <a href="{{ route('projects.show', $task->project) }}">{{ $task->project->name }}</a>
            <span class="sep">/</span>
            <span style="color:var(--td-text); font-weight:600;">{{ Str::limit($task->title, 55) }}</span>
        </nav>

        {{-- CABECERA con franja de color --}}
        <div class="td-page-header">
            <div class="td-status-stripe {{ $task->trashed() ? 'stripe-deleted' : 'stripe-' . $status }}"></div>
            <div class="td-page-header-body">
                <div style="flex:1; min-width:0;">
                    @if ($isEditing)
                        <input type="text" wire:model.defer="title"
                            class="td-title-input" placeholder="Título de la tarea">
                        @error('title')
                            <small style="color:var(--td-danger); font-size:.78rem;">{{ $message }}</small>
                        @enderror
                    @else
                        <h1 class="td-title">{{ $task->title }}</h1>
                    @endif
                </div>

                <div class="td-header-actions">
                    @if (!$task->trashed())
                        @if ($isEditing)
                            <button wire:click="save" class="td-btn td-btn-primary">
                                <i class="fas fa-check"></i> Guardar
                            </button>
                            <button wire:click="cancelEdit" class="td-btn td-btn-ghost">
                                Cancelar
                            </button>
                        @else
                            <button wire:click="startEdit" class="td-btn td-btn-ghost">
                                <i class="fas fa-pen"></i> Editar
                            </button>
                        @endif
                        <button type="button" class="td-btn td-btn-danger-sm" x-data
                            data-id="{{ $task->id }}"
                            data-title="Eliminar {{ $task->title }}"
                            data-message="¿Seguro que quieres eliminar la tarea <i>{{ $task->title }}</i>? Podrá recuperarse más tarde."
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
                        <span class="td-deleted-banner">
                            <i class="fas fa-trash-alt"></i> Tarea eliminada
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- LAYOUT PRINCIPAL --}}
        <div class="td-layout">

            {{-- ── COLUMNA IZQUIERDA ─────────────────────── --}}
            <div>

                {{-- DESCRIPCIÓN --}}
                <div class="td-card">
                    <div class="td-card-header">Descripción</div>
                    <div class="td-card-body">
                        @if ($isEditing)
                            <textarea wire:model.defer="description"
                                class="td-textarea"
                                x-data
                                x-init="$el.style.height = $el.scrollHeight + 'px'"
                                x-on:input="$el.style.height='auto'; $el.style.height=$el.scrollHeight+'px'"
                                placeholder="Describe la tarea…"></textarea>
                        @else
                            @if ($task->description)
                                <p style="margin:0; white-space:pre-line; font-size:.9rem; line-height:1.65; color:var(--td-text);">
                                    {!! nl2br(e($task->description)) !!}
                                </p>
                            @else
                                <p style="margin:0; font-style:italic; color:var(--td-muted); font-size:.88rem;">
                                    Sin descripción.
                                </p>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- ETIQUETAS --}}
                <div class="td-card" style="margin-top:16px;">
                    <div class="td-card-header">Etiquetas</div>
                    <div class="td-card-body">
                        @if ($isEditing)
                            {{-- Toggle chips: mucho más usable que un <select multiple> --}}
                            @if ($labels->isNotEmpty())
                                <div class="td-label-grid">
                                    @foreach ($labels as $label)
                                        @php $isSelected = in_array($label->id, $selected_labels ?? []); @endphp
                                        <label class="td-label-toggle {{ $isSelected ? 'selected' : '' }}">
                                            <input type="checkbox"
                                                value="{{ $label->id }}"
                                                wire:model.defer="selected_labels"
                                                style="display:none;">
                                            <span class="td-label-check">✓</span>
                                            {{ $label->name }}
                                        </label>
                                    @endforeach
                                </div>
                                @error('selected_labels')
                                    <small style="color:var(--td-danger); font-size:.78rem; display:block; margin-top:5px;">
                                        {{ $message }}
                                    </small>
                                @enderror
                                @error('selected_labels.*')
                                    <small style="color:var(--td-danger); font-size:.78rem; display:block; margin-top:5px;">
                                        Label duplicado o inválido.
                                    </small>
                                @enderror
                            @else
                                <p style="margin:0; color:var(--td-muted); font-size:.83rem; font-style:italic;">
                                    No hay etiquetas disponibles en este tenant.
                                </p>
                            @endif
                        @else
                            @if ($task->labels && $task->labels->isNotEmpty())
                                <div class="td-labels-view">
                                    @foreach ($task->labels as $lbl)
                                        <span class="td-label-chip">{{ $lbl->name }}</span>
                                    @endforeach
                                </div>
                            @else
                                <p style="margin:0; font-style:italic; color:var(--td-muted); font-size:.85rem;">
                                    Sin etiquetas.
                                </p>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- TIEMPO REGISTRADO --}}
                <div class="td-card" style="margin-top:16px;">
                    <div class="td-card-header">Tiempo registrado</div>
                    <div class="td-card-body">
                        @php
                            $tH = floor($totalSeconds / 3600);
                            $tM = floor(($totalSeconds % 3600) / 60);
                            $tS = $totalSeconds % 60;
                        @endphp

                        <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
                            <span class="td-time-total">
                                {{ str_pad($tH,2,'0',STR_PAD_LEFT) }}:{{ str_pad($tM,2,'0',STR_PAD_LEFT) }}:{{ str_pad($tS,2,'0',STR_PAD_LEFT) }}
                            </span>
                            @if ($runningEntry)
                                <span class="td-time-running">
                                    <i class="fas fa-circle"></i> En curso
                                </span>
                            @endif
                        </div>

                        <div class="td-time-controls">
                            @if ($runningEntry)
                                <button wire:click="toggleTimeTracking"
                                    class="td-track-btn td-track-btn-stop">
                                    <span class="td-track-btn-icon"><i class="fas fa-stop"></i></span>
                                    Detener grabación
                                </button>
                            @else
                                <button wire:click="toggleTimeTracking"
                                    class="td-track-btn td-track-btn-start">
                                    <span class="td-track-btn-icon"><i class="fas fa-play"></i></span>
                                    Iniciar grabación
                                </button>
                            @endif
                            <button wire:click="openManualTimeModal" class="td-track-btn-manual">
                                <i class="fas fa-clock"></i> Añadir manual
                            </button>
                        </div>

                        @if ($task->timeEntries->isNotEmpty())
                            <table class="td-time-table">
                                <thead>
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Inicio</th>
                                        <th>Duración</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($task->timeEntries->sortByDesc('started_at') as $entry)
                                        @php
                                            $dH = floor(($entry->duration_seconds ?? 0) / 3600);
                                            $dM = floor((($entry->duration_seconds ?? 0) % 3600) / 60);
                                            $dS = ($entry->duration_seconds ?? 0) % 60;
                                        @endphp
                                        <tr>
                                            <td>
                                                <img src="{{ $entry->user?->profile_photo_url }}"
                                                    alt="{{ $entry->user?->name }}"
                                                    class="td-time-avatar">
                                                {{ $entry->user?->name ?? 'Desconocido' }}
                                            </td>
                                            <td>{{ $entry->started_at->format('d/m/Y H:i') }}</td>
                                            <td style="font-family:monospace;">
                                                @if ($entry->is_running)
                                                    <span style="color:var(--td-danger);">En curso…</span>
                                                @else
                                                    {{ str_pad($dH,2,'0',STR_PAD_LEFT) }}:{{ str_pad($dM,2,'0',STR_PAD_LEFT) }}:{{ str_pad($dS,2,'0',STR_PAD_LEFT) }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p style="margin:10px 0 0; font-style:italic; color:var(--td-muted); font-size:.83rem;">
                                Aún no hay entradas de tiempo.
                            </p>
                        @endif
                    </div>
                </div>

            </div>

            {{-- ── COLUMNA DERECHA (metadata) ─────────────── --}}
            <div>

                {{-- ESTADO --}}
                <div class="td-card">
                    <div class="td-card-header">Estado</div>
                    <div class="td-card-body" style="padding-top:8px;">
                        <select wire:model{{ $isEditing ? '.defer' : '' }}="status"
                            class="td-select {{ $status }}"
                            @if (!$isEditing) disabled @endif>
                            <option value="pending"     class="opt-pending">⏳ Pendiente</option>
                            <option value="in_progress" class="opt-in_progress">▶ En progreso</option>
                            <option value="on_hold"     class="opt-on_hold">⏸ En pausa</option>
                            <option value="testing"     class="opt-testing">🧪 En pruebas</option>
                            <option value="done"        class="opt-done">✅ Hecha</option>
                        </select>
                    </div>
                </div>

                {{-- PRIORIDAD --}}
                <div class="td-card" style="margin-top:14px;">
                    <div class="td-card-header">Prioridad</div>
                    <div class="td-card-body" style="padding-top:8px;">
                        <select wire:model{{ $isEditing ? '.defer' : '' }}="priority"
                            class="td-select task-priority-select"
                            @if (!$isEditing) disabled @endif>
                            @for ($i = 0; $i <= 10; $i++)
                                @php $cls = \App\Models\Task::PRIORITY_CLASSES[$i] ?? 'unknown'; @endphp
                                <option value="{{ $i }}" class="priority-{{ $cls }}">
                                    {{ \App\Models\Task::PRIORITY_LABELS[$i] ?? 'Desconocida' }} ({{ $i }})
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>

                {{-- FECHA LÍMITE --}}
                <div class="td-card" style="margin-top:14px;">
                    <div class="td-card-header">Fecha límite</div>
                    <div class="td-card-body" style="padding-top:8px;">
                        @if ($isEditing)
                            <input type="date" wire:model.defer="due_date" class="td-date-input">
                            @error('due_date')
                                <small style="color:var(--td-danger); font-size:.78rem;">{{ $message }}</small>
                            @enderror
                        @else
                            @if ($task->due_date)
                                @php $isOverdue = $task->due_date->isPast() && !$task->isDone(); @endphp
                                <span class="td-due-display {{ $isOverdue ? 'overdue' : '' }}">
                                    <i class="fas fa-calendar-alt" style="font-size:.8rem; margin-right:4px;"></i>
                                    {{ $task->due_date->format('d/m/Y') }}
                                    @if ($isOverdue) ⚠️ @endif
                                </span>
                            @else
                                <span style="font-style:italic; color:var(--td-muted); font-size:.85rem;">
                                    Sin fecha límite
                                </span>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- CREADO POR --}}
                <div class="td-card" style="margin-top:14px;">
                    <div class="td-card-header">Creado por</div>
                    <div class="td-card-body" style="padding-top:8px;">
                        @if ($task->creator)
                            <div class="td-creator">
                                <img src="{{ $task->creator->profile_photo_url }}"
                                    alt="{{ $task->creator->name }}"
                                    class="td-creator-avatar">
                                <div>
                                    <div class="td-creator-name">{{ $task->creator->name }}</div>
                                    <div class="td-creator-email">{{ $task->creator->email }}</div>
                                </div>
                            </div>
                        @else
                            <span style="font-style:italic; color:var(--td-muted); font-size:.85rem;">
                                Desconocido
                            </span>
                        @endif
                    </div>
                </div>

                {{-- FECHAS --}}
                <div class="td-card" style="margin-top:14px;">
                    <div class="td-card-header">Fechas</div>
                    <div class="td-card-body" style="padding-top:8px;">
                        <div class="td-ts">
                            <strong>Creada:</strong>
                            {{ $task->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div class="td-ts" style="margin-top:4px;">
                            <strong>Actualizada:</strong>
                            {{ $task->updated_at->format('d/m/Y H:i') }}
                        </div>
                        @if ($task->trashed())
                            <div class="td-divider"></div>
                            <div class="td-ts" style="color:var(--td-danger);">
                                <strong>Eliminada:</strong>
                                {{ $task->deleted_at->format('d/m/Y H:i') }}
                            </div>
                        @endif
                    </div>
                </div>

            </div>{{-- /columna derecha --}}

        </div>{{-- /td-layout --}}

    </div>{{-- /td-inner --}}

    {{-- MODAL TIEMPO MANUAL --}}
    @if ($showManualTimeModal)
        <div class="td-modal-overlay">
            <div class="td-modal">
                <h5 class="td-modal-title"><i class="fas fa-clock me-2" style="color:var(--td-accent);"></i>Añadir tiempo manual</h5>
                <div class="td-modal-row">
                    <div class="td-modal-col">
                        <label>Horas</label>
                        <input type="number" min="0" class="td-modal-input" wire:model="manualHours">
                        @error('manualHours')
                            <small style="color:var(--td-danger); font-size:.75rem;">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="td-modal-col">
                        <label>Minutos</label>
                        <input type="number" min="0" max="59" class="td-modal-input" wire:model="manualMinutes">
                        @error('manualMinutes')
                            <small style="color:var(--td-danger); font-size:.75rem;">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="td-modal-footer">
                    <button class="td-btn td-btn-ghost" wire:click="closeManualTimeModal">Cancelar</button>
                    <button class="td-btn td-btn-primary" wire:click="saveManualTime">
                        <i class="fas fa-check"></i> Guardar tiempo
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>