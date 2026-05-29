{{--
    Vista del Calendario — Caso de Uso 07
    Requiere:
        - FullCalendar 6 (CDN)
        - SweetAlert2 (CDN)
        - Livewire 3

    Comportamiento clave:
        - Carga inicial automática de tareas (CU07, paso 3).
        - Filtros con wire:model.live que persisten en sesión (CU07, punto 3a).
        - Heatmap de intensidad de carga por día (CU06/CU07).
        - Drag & Drop con reversión visual si no hay permisos (CU07, paso 5a).
        - Clic → popup SweetAlert2 para cambiar estado (CU07, paso 5b).
        - Polling cada 30 s para sincronización con cambios de otros usuarios (CU07, punto 3b).
        - Mensaje "Sin tareas" cuando los filtros no devuelven resultados.
--}}

<div class="container-fluid py-4">

    {{-- ── Cabecera ────────────────────────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="m-0 fw-semibold">📅 Calendario de Trabajo</h2>
        <div wire:loading class="spinner-border text-primary spinner-border-sm" role="status">
            <span class="visually-hidden">Cargando…</span>
        </div>
    </div>

    {{-- ── Filtros ──────────────────────────────────────────────────────────── --}}
    <div class="card p-3 bg-light mb-4 shadow-sm">
        <div class="row g-3 align-items-end">

            {{-- Proyecto --}}
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted mb-1">Proyecto</label>
                <select wire:model.live="projectId" class="form-select form-select-sm">
                    <option value="">Todos los proyectos</option>
                    @foreach($projects as $project)
                        <option value="{{ $project['id'] }}">{{ $project['name'] }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Estado --}}
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted mb-1">Estado</label>
                <select wire:model.live="status" class="form-select form-select-sm">
                    <option value="">Todos los estados</option>
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Usuario asignado (solo responsables / admin) — CU07, paso 2 --}}
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted mb-1">Usuario asignado</label>
                @if($canFilterByUser)
                    <select wire:model.live="userId" class="form-select form-select-sm">
                        <option value="">Todo el equipo</option>
                        @foreach($users as $user)
                            <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                        @endforeach
                    </select>
                @else
                    {{-- Empleado ve solo sus tareas; no puede cambiar este filtro --}}
                    <input
                        type="text"
                        class="form-control form-control-sm"
                        value="{{ auth()->user()->name }}"
                        disabled
                        readonly
                        title="Los empleados solo pueden ver sus propias tareas"
                    >
                @endif
            </div>

        </div>
    </div>

    {{-- ── Leyenda del Heatmap ─────────────────────────────────────────────── --}}
    <div class="d-flex align-items-center small text-muted mb-3 justify-content-end gap-1">
        <span class="me-1">Carga (tareas/día):</span>
        <div class="rounded border" style="width:14px;height:14px;background:rgba(220,53,69,.10);" title="1 tarea"></div>
        <div class="rounded border" style="width:14px;height:14px;background:rgba(220,53,69,.35);" title="2–3 tareas"></div>
        <div class="rounded border" style="width:14px;height:14px;background:rgba(220,53,69,.65);" title="4–5 tareas"></div>
        <div class="rounded border" style="width:14px;height:14px;background:rgba(220,53,69,.90);" title="6+ tareas"></div>
        <span class="ms-1">Alta</span>
    </div>

    {{-- ── Leyenda de estados ───────────────────────────────────────────────── --}}
    <div class="d-flex flex-wrap gap-3 small mb-3">
        <span><span class="badge" style="background:#ffc107;color:#000">●</span> Pendiente</span>
        <span><span class="badge" style="background:#0d6efd">●</span> En Progreso</span>
        <span><span class="badge" style="background:#0d6efd">●</span> En Pruebas</span>
        <span><span class="badge" style="background:#6c757d">●</span> En Pausa</span>
        <span><span class="badge" style="background:#198754">●</span> Completada</span>
    </div>

    {{-- ── Calendario ───────────────────────────────────────────────────────── --}}
    {{-- wire:ignore evita que Livewire destruya el DOM de FullCalendar al re-renderizar --}}
    <div class="card p-3 shadow-sm" wire:ignore>
        <div id="calendar"></div>
    </div>

    {{-- Aviso "sin tareas" (gestionado por JS, oculto por defecto) --}}
    <div id="no-tasks-alert" class="alert alert-info mt-3 d-none" role="alert">
        <i class="bi bi-info-circle me-1"></i>
        No hay tareas que coincidan con los criterios seleccionados.
    </div>

</div>

{{-- ── Dependencias CDN ──────────────────────────────────────────────────────── --}}
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('livewire:init', function () {

    // ── Estado compartido ──────────────────────────────────────────────────────
    var calendarEl      = document.getElementById('calendar');
    var noTasksAlert    = document.getElementById('no-tasks-alert');
    var calendar        = null;
    var currentHeatmap  = {};
    var lastEventHash   = '';   // para detectar cambios en segundo plano
    var pendingRevert   = null; // callback de FullCalendar para revertir un drop

    // ── Mapa de opciones de estado para el select de SweetAlert2 ──────────────
    var statusOptions = {
        'pending':     'Pendiente',
        'in_progress': 'En Progreso',
        'on_hold':     'En Pausa',
        'testing':     'En Pruebas',
        'done':        'Completada'
    };

    // ── Inicializar FullCalendar ───────────────────────────────────────────────
    calendar = new FullCalendar.Calendar(calendarEl, {

        initialView:  'dayGridMonth',
        locale:       'es',
        firstDay:     1,        // lunes
        editable:     true,     // habilita drag & drop
        droppable:    true,

        headerToolbar: {
            left:   'prev,next today',
            center: 'title',
            right:  'dayGridMonth,timeGridWeek'
        },

        // ── Sin eventos: mostrar aviso informativo (CU07, alternativa 1) ──────
        noEventsContent: function () {
            noTasksAlert.classList.remove('d-none');
            return '';
        },

        // ── Heatmap: colorear celda de día según carga de tareas ───────────────
        dayCellDidMount: function (info) {
            var dateStr   = formatDateLocal(info.date);
            var taskCount = currentHeatmap[dateStr] || 0;

            if (taskCount > 0) {
                // Intensidad máxima a partir de 6 tareas
                var intensity = Math.min(1, taskCount / 6);
                info.el.style.backgroundColor = 'rgba(220,53,69,' + intensity + ')';
            } else {
                info.el.style.backgroundColor = '';
            }
        },

        // ── Drag & Drop: soltar tarea en otro día (CU07, paso 5a) ─────────────
        eventDrop: function (info) {
            // Guardar el callback de reversión por si el servidor deniega
            pendingRevert = info.revert;

            var newDate = info.event.startStr.split('T')[0];
            @this.dispatch('update-task-date', {
                taskId:  parseInt(info.event.id),
                newDate: newDate
            });
        },

        // ── Clic en evento: edición rápida de estado (CU07, paso 5b) ──────────
        eventClick: async function (info) {
            var props     = info.event.extendedProps;
            var canEdit   = props.canEdit;

            if (!canEdit) {
                Swal.fire({
                    icon:  'warning',
                    title: 'Sin permisos',
                    text:  'No tienes permisos para editar esta tarea.',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            var result = await Swal.fire({
                title: info.event.title,
                html:
                    '<small class="text-muted">' + escapeHtml(props.projectName) + '</small>' +
                    '<br><small>Estado actual: <b>' + escapeHtml(props.statusLabel) + '</b></small>',
                input:         'select',
                inputOptions:  statusOptions,
                inputValue:    props.status,
                showCancelButton:   true,
                confirmButtonText:  'Guardar',
                cancelButtonText:   'Cancelar',
                inputValidator: function (value) {
                    if (!value) return 'Debes seleccionar un estado.';
                }
            });

            if (result.isConfirmed && result.value && result.value !== props.status) {
                @this.dispatch('update-task-status', {
                    taskId:    parseInt(info.event.id),
                    newStatus: result.value
                });
            }
        },

        // Tooltip básico al pasar el ratón
        eventDidMount: function (info) {
            info.el.setAttribute('title',
                info.event.title + ' — ' + (info.event.extendedProps.projectName || '')
            );
        }
    });

    calendar.render();

    // ── Solicitar tareas al montar la vista ────────────────────────────────────
    @this.dispatch('fetch-tasks');

    // ── Escuchar recarga pedida por Livewire ───────────────────────────────────
    Livewire.on('refresh-calendar', function () {
        @this.dispatch('fetch-tasks');
    });

    // ── Recibir datos del servidor y actualizar el calendario ──────────────────
    Livewire.on('load-calendar-data', function (data) {
        var payload  = data[0];
        var events   = payload.events   || [];
        var heatmap  = payload.heatmap  || {};

        currentHeatmap = heatmap;

        // Actualizar heatmap: re-renderizar celdas
        calendar.render();

        // Reemplazar eventos
        calendar.removeAllEvents();
        calendar.addEventSource(events);

        // Mostrar / ocultar aviso "sin tareas"
        if (events.length === 0) {
            noTasksAlert.classList.remove('d-none');
        } else {
            noTasksAlert.classList.add('d-none');
        }

        // Registrar hash para detectar cambios en polling
        lastEventHash = JSON.stringify(events.map(function (e) {
            return e.id + ':' + e.start + ':' + e.extendedProps.status;
        }));
    });

    // ── Revertir drop denegado por el servidor ─────────────────────────────────
    Livewire.on('revert-event', function () {
        if (pendingRevert) {
            pendingRevert();
            pendingRevert = null;
        }
    });

    // ── Notificaciones toast ───────────────────────────────────────────────────
    Livewire.on('notify', function (data) {
        var item = Array.isArray(data) ? data[0] : data;
        Swal.fire({
            toast:              true,
            position:           'top-end',
            icon:               item.type   || 'info',
            title:              item.message || '',
            showConfirmButton:  false,
            timer:              3000,
            timerProgressBar:   true
        });
    });

    // ── Polling: sincronización con cambios de otros usuarios ──────────────────
    // CU07, punto 3b — cada 30 segundos re-carga eventos silenciosamente
    setInterval(function () {
        @this.dispatch('fetch-tasks');
    }, 30000);

    // ── Utilidades ────────────────────────────────────────────────────────────

    /** Formatea un Date como YYYY-MM-DD en hora local (evita desfase UTC). */
    function formatDateLocal(date) {
        var y  = date.getFullYear();
        var m  = String(date.getMonth() + 1).padStart(2, '0');
        var d  = String(date.getDate()).padStart(2, '0');
        return y + '-' + m + '-' + d;
    }

    /** Escapa caracteres HTML para evitar XSS en el popup. */
    function escapeHtml(text) {
        var map = { '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#039;' };
        return String(text).replace(/[&<>"']/g, function (m) { return map[m]; });
    }

});
</script>