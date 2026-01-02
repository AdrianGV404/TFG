<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Gestión de proyectos')</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    />

    @livewireStyles
</head>

<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('notify', ({ message, type }) => {

            // Elimina notificaciones anteriores
            document.querySelectorAll('.lw-notification').forEach(n => n.remove());

            const alert = document.createElement('div');

            alert.className = `lw-notification alert alert-${type}`;
            alert.innerText = message;

            Object.assign(alert.style, {
                position: 'fixed',
                top: '20px',
                right: '20px',
                zIndex: 9999,
                minWidth: '320px',
                maxWidth: '420px',
                padding: '16px 20px',
                fontSize: '1rem',
                fontWeight: '500',
                boxShadow: '0 10px 25px rgba(0,0,0,.15)',
                borderRadius: '10px',
                opacity: '0',
                transform: 'translateY(-10px)',
                transition: 'all .25s ease'
            });

            document.body.appendChild(alert);

            // Animación de entrada
            requestAnimationFrame(() => {
                alert.style.opacity = '1';
                alert.style.transform = 'translateY(0)';
            });

            // Auto-cierre (5 segundos)
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        });
    });
</script>

<!-- MODAL DE CONFIRMACIÓN (CENTRADO) -->
<div
    x-data="{
        show: false,
        title: '',
        message: '',
        deleteEvent: null,
        deleteId: null,
    }"
    x-on:confirm-delete.window="
        title = $event.detail.title;
        message = $event.detail.message;
        deleteEvent = $event.detail.action;
        deleteId = $event.detail.id;
        show = true;
    "
    x-show="show"
    x-transition.opacity
    x-cloak
    style="
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.5);
        z-index: 9998;
    "
>
    <div
        class="bg-white rounded shadow p-4"
        style="
            width: 420px;
            max-width: 90%;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        "
        @click.outside="show = false"
    >
        <h5 class="mb-3" x-text="title"></h5>

        <p class="text-muted mb-4" x-text="message"></p>

        <div class="d-flex justify-content-end gap-2">
            <button
                class="btn btn-secondary"
                @click="show = false"
            >
                Cancelar
            </button>

            <button
                class="btn btn-danger"
                @click="
                    show = false;
                    Livewire.dispatch(deleteEvent, { id: deleteId });
                "
            >
                Sí, eliminar
            </button>
        </div>
    </div>
</div>

<body>

    @yield('content')

    @livewireScripts
</body>
</html>
