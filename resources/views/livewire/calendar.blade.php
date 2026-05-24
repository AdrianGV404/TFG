<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="m-0">📅 Calendario de Trabajo</h2>
        <div wire:loading class="spinner-border text-primary spinner-border-sm" role="status"></div>
    </div>

    {{-- FILTROS --}}
    <div class="card p-3 bg-light mb-4 shadow-sm">
        <div class="row g-3">
            {{-- Filtro Proyecto --}}
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Proyecto</label>
                <select wire:model.live="projectId" class="form-select">
                    <option value="">Todos los proyectos</option>
                    @foreach($projects as $project)
                        <option value="{{ $project['id'] }}">{{ $project['name'] }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filtro Estado --}}
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Estado</label>
                <select wire:model.live="status" class="form-select">
                    <option value="">Todos los estados</option>
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filtro Usuario (Solo Responsables/Admin) --}}
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Usuario Asignado</label>
                @if(in_array(auth()->user()->role, ['admin', 'responsable']))
                    <select wire:model.live="userId" class="form-select">
                        <option value="">Todo el equipo</option>
                        @foreach($users as $user)
                            <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                        @endforeach
                    </select>
                @else
                    <input type="text" class="form-control" value="{{ auth()->user()->name }}" disabled readonly>
                @endif
            </div>
        </div>
    </div>

    {{-- LEYENDA HEATMAP --}}
    <div class="d-flex align-items-center small text-muted mb-3 justify-content-end">
        <span class="me-2">Carga de trabajo (Heatmap):</span>
        Baja <div class="mx-1 rounded" style="width: 15px; height: 15px; background: rgba(220, 53, 69, 0.1);"></div>
        <div class="mx-1 rounded" style="width: 15px; height: 15px; background: rgba(220, 53, 69, 0.4);"></div>
        <div class="mx-1 rounded" style="width: 15px; height: 15px; background: rgba(220, 53, 69, 0.8);"></div> Alta
    </div>

    {{-- CALENDARIO (wire:ignore asegura que Livewire no rompa FullCalendar al actualizar los filtros) --}}
    <div class="card p-3 shadow-sm" wire:ignore>
        <div id="calendar"></div>
    </div>
</div>

{{-- Dependencias CDN --}}
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('livewire:init', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar;
        var currentHeatmap = {};

        // Inicializar FullCalendar
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek'
            },
            locale: 'es',
            firstDay: 1, // Lunes
            editable: true, // Permite Drag & Drop
            droppable: true,
            
            // 1. Mostrar texto si no hay tareas (Alternativa 1)
            noEventsContent: "No hay tareas que coincidan con los criterios.",

            // 2. Colorear los días según el Heatmap
            dayCellDidMount: function(info) {
                let dateStr = info.date.toLocaleDateString('en-CA'); // Formato YYYY-MM-DD local
                let taskCount = currentHeatmap[dateStr] || 0;
                
                if(taskCount > 0) {
                    // Calculamos intensidad del rojo según carga (max 5 tareas para 100% color)
                    let intensity = Math.min(1, taskCount / 5);
                    info.el.style.backgroundColor = `rgba(220, 53, 69, ${intensity})`;
                } else {
                    info.el.style.backgroundColor = ''; // Resetear color
                }
            },

            // 3. Evento: Al soltar una tarea en otro día (Drag & Drop)
            eventDrop: function(info) {
                let newDate = info.event.startStr.split('T')[0];
                Livewire.dispatch('update-task-date', { taskId: info.event.id, newDate: newDate });
            },

            // 4. Evento: Clic para edición rápida de estado
            eventClick: async function(info) {
                const { value: newStatus } = await Swal.fire({
                    title: 'Editar Tarea',
                    html: `<b>${info.event.title}</b><br><small>${info.event.extendedProps.projectName}</small>`,
                    input: 'select',
                    inputOptions: {
                        'pending': 'Pendiente',
                        'in_progress': 'En Progreso',
                        'testing': 'En Pruebas',
                        'done': 'Completada',
                        'blocked': 'Bloqueado'
                    },
                    inputValue: info.event.extendedProps.status,
                    showCancelButton: true,
                    confirmButtonText: 'Guardar Estado',
                    cancelButtonText: 'Cancelar'
                });

                if (newStatus && newStatus !== info.event.extendedProps.status) {
                    Livewire.dispatch('update-task-status', { taskId: info.event.id, newStatus: newStatus });
                }
            }
        });
        
        calendar.render();

        // Solicitar las tareas al cargar la vista
        Livewire.dispatch('fetch-tasks');

        // Escuchar cuando el componente Livewire pida recargar
        Livewire.on('refresh-calendar', () => {
            Livewire.dispatch('fetch-tasks');
        });

        // Escuchar cuando el servidor envía los eventos procesados
        Livewire.on('load-calendar-data', (data) => {
            // Actualizar variables y calendario
            currentHeatmap = data[0].heatmap;
            calendar.removeAllEvents();
            calendar.addEventSource(data[0].events);
        });

        // Alertas flotantes bonitas
        Livewire.on('notify', (data) => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: data[0].type,
                title: data[0].message,
                showConfirmButton: false,
                timer: 3000
            });
        });
    });
</script>