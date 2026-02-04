<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'ProMaTi')</title>

    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

    @livewireStyles

    <style>
        /* Ajusta el body para que el contenido quede debajo del navbar fijo */
        body {
            padding-top: 56px;
        }
    </style>
</head>

<body>
    @include('livewire.partials.navbar')

    <div class="main-content">
        @yield('content')
    </div>

    @livewireScripts

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('notify', ({
                message,
                type
            }) => {
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
                requestAnimationFrame(() => {
                    alert.style.opacity = '1';
                    alert.style.transform = 'translateY(0)';
                });
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });
        });
    </script>

    <!-- MODAL DE CONFIRMACIÓN DE BORRADO -->
    <div x-data="{
        show: false,
        title: '',
        message: '',
        deleteEvent: null,
        deleteId: null,
        isPermanent: false,
        countdown: 10
    }"
        x-on:confirm-delete.window="
            title = $event.detail.title;
            message = $event.detail.message;
            deleteEvent = $event.detail.action;
            deleteId = $event.detail.id;
            isPermanent = $event.detail.isPermanent || false;
            countdown = isPermanent ? 10 : 0;
            show = true;

            if(isPermanent){
                const interval = setInterval(() => {
                    countdown--;
                    if(countdown <= 0) clearInterval(interval);
                }, 1000);
            }
        "
        x-show="show" x-transition.opacity x-cloak
        style="position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 9998;">
        <div class="bg-white rounded shadow p-4"
            style="width: 420px; max-width: 90%; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%)"
            @click.outside="show = false">
            <h5 class="mb-3" x-text="title"></h5>

            <p class="text-muted mb-4" x-html="message"></p>

            <div class="d-flex justify-content-end gap-2">
                <button class="btn btn-secondary" @click="show = false">
                    Cancelar
                </button>

                <button class="btn btn-danger" x-bind:disabled="countdown > 0"
                    x-text="isPermanent && countdown > 0 ? 'Eliminar en ' + countdown + 's' : 'Sí, eliminar'"
                    @click="
                        show = false;
                        Livewire.dispatch(deleteEvent, { id: deleteId });
                    "></button>
            </div>
        </div>
    </div>

    <!-- MODAL DE CONFIRMACIÓN DE RESTAURAR -->
    <div x-data="{
        showRestore: false,
        restoreId: null,
        title: '',
        message: '',
        action: null
    }"
        x-on:confirm-restore.window="
        restoreId = $event.detail.id;
        title = $event.detail.title;
        message = $event.detail.message;
        action = $event.detail.action;
        showRestore = true;
    "
        x-show="showRestore" x-transition.opacity x-cloak
        style="position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 9998;">
        <div class="bg-white rounded shadow p-4"
            style="width: 420px; max-width: 90%; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%)"
            @click.outside="showRestore = false">

            <h5 class="mb-3" x-text="title"></h5>
            <p class="text-muted mb-4" x-html="message"></p>

            <div class="d-flex justify-content-end gap-2">
                <button class="btn btn-secondary" @click="showRestore = false">
                    Cancelar
                </button>

                <button class="btn btn-success"
                    @click="
                    Livewire.dispatch(action, { id: restoreId });
                    showRestore = false;
                ">
                    Restaurar
                </button>
            </div>
        </div>
    </div>
</body>

</html>
