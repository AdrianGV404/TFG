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


<body>

    @yield('content')
    @livewireScripts
</body>
</html>
