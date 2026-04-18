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
        :root {
            --sidebar-width: 280px;
            --navbar-height: 56px;
        }

        body {
            padding-top: var(--navbar-height);
            overflow-x: hidden;
            margin: 0;
            /* Quitamos el transition del body para que no haya saltos raros */
        }

        /* SIDEBAR */
        #sidebar-panel {
            position: fixed;
            top: 0;
            left: calc(var(--sidebar-width) * -1);
            width: var(--sidebar-width);
            height: 100vh;
            background-color: #212529;
            z-index: 1060; /* Por encima del navbar si es necesario */
            transition: left 0.3s ease;
            border-right: 1px solid #343a40;
        }

        /* CONTENEDORES DINÁMICOS (Adaptación de tamaño) */
        /* El navbar (.fixed-top) y el envoltorio principal (.main-wrapper) */
        .main-wrapper, 
        .fixed-top {
            transition: margin-left 0.3s ease, width 0.3s ease;
            width: 100% !important;
            margin-left: 0;
        }

        /* ESTADO ABIERTO: Aquí ocurre la magia del reajuste */
        .sidebar-open #sidebar-panel { 
            left: 0; 
        }

        .sidebar-open .main-wrapper, 
        .sidebar-open .fixed-top { 
            /* En lugar de transform, usamos margen para reducir el espacio disponible */
            margin-left: var(--sidebar-width) !important; 
            /* Restamos el ancho del sidebar al total para que el contenido NO se salga */
            width: calc(100% - var(--sidebar-width)) !important; 
        }

        /* SIDEBAR LINKS */
        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #adb5bd;
            text-decoration: none;
            transition: 0.2s;
            border-radius: 8px;
            margin: 4px 10px;
        }
        .sidebar-link:hover { background: rgba(255,255,255,0.1); color: #fff; }
        .sidebar-link i { width: 25px; }

        [x-cloak] { display: none !important; }
    </style>
</head>

<body x-data="{ sidebarOpen: false }" :class="sidebarOpen ? 'sidebar-open' : ''">

    {{-- Mostrar Sidebar solo si NO es login y el usuario está autenticado --}}
    @if (Route::currentRouteName() !== 'login' && auth()->check())
    <aside id="sidebar-panel" class="shadow">
        <div class="p-3 border-bottom border-secondary d-flex justify-content-between align-items-center">
            <span class="text-white fw-bold fs-5"><i class="fas fa-th-large me-2"></i>Menú</span>
            <button class="btn btn-sm text-white" @click="sidebarOpen = false">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="mt-3">
            <a href="{{ route('projects') }}" class="sidebar-link">
                <i class="fas fa-home"></i> Inicio / Proyectos
            </a>
            
            @if (auth()->user()->role === 'admin')
                <div class="px-4 mt-4 mb-2 text-uppercase small text-muted fw-bold" style="font-size: 0.7rem;">Administración</div>
                <a href="{{ route('users.index') }}" class="sidebar-link">
                    <i class="fas fa-users-cog"></i> Gestión Usuarios
                </a>
                <a href="{{ route('users.create') }}" class="sidebar-link">
                    <i class="fas fa-user-plus"></i> Nuevo Usuario
                </a>
            @endif

            <div class="px-4 mt-4 mb-2 text-uppercase small text-muted fw-bold" style="font-size: 0.7rem;">Cuenta</div>
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="sidebar-link border-0 bg-transparent w-100 text-start">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </button>
            </form>
        </div>
    </aside>
    @endif

    @include('livewire.partials.navbar')
    <div class="main-wrapper">
        <div class="main-content">
            @yield('content')
        </div>
    </div>

    @livewireScripts

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('notify', ({ message, type }) => {
                document.querySelectorAll('.lw-notification').forEach(n => n.remove());
                const alert = document.createElement('div');
                alert.className = `lw-notification alert alert-${type}`;
                alert.innerText = message;
                Object.assign(alert.style, {
                    position: 'fixed', top: '20px', right: '20px', zIndex: 9999,
                    minWidth: '320px', maxWidth: '420px', padding: '16px 20px',
                    boxShadow: '0 10px 25px rgba(0,0,0,.15)', borderRadius: '10px',
                    transition: 'all .25s ease'
                });
                document.body.appendChild(alert);
                setTimeout(() => alert.remove(), 5000);
            });
        });
    </script>

    {{-- MODAL BORRADO --}}
    <div x-data="{ show: false, title: '', message: '', deleteEvent: null, deleteId: null, isPermanent: false, countdown: 10 }"
        x-on:confirm-delete.window="title = $event.detail.title; message = $event.detail.message; deleteEvent = $event.detail.action; deleteId = $event.detail.id; isPermanent = $event.detail.isPermanent || false; countdown = isPermanent ? 10 : 0; show = true; if(isPermanent){ const interval = setInterval(() => { countdown--; if(countdown <= 0) clearInterval(interval); }, 1000); }"
        x-show="show" x-transition.opacity x-cloak style="position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 9998;">
        <div class="bg-white rounded shadow p-4" style="width: 420px; max-width: 90%; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%)" @click.outside="show = false">
            <h5 class="mb-3" x-text="title"></h5>
            <p class="text-muted mb-4" x-html="message"></p>
            <div class="d-flex justify-content-end gap-2">
                <button class="btn btn-secondary" @click="show = false">Cancelar</button>
                <button class="btn btn-danger" x-bind:disabled="countdown > 0" x-text="isPermanent && countdown > 0 ? 'Eliminar en ' + countdown + 's' : 'Sí, eliminar'" @click="show = false; Livewire.dispatch(deleteEvent, { id: deleteId });"></button>
            </div>
        </div>
    </div>

    {{-- MODAL RESTAURAR --}}
    <div x-data="{ showRestore: false, restoreId: null, title: '', message: '', action: null }"
        x-on:confirm-restore.window="restoreId = $event.detail.id; title = $event.detail.title; message = $event.detail.message; action = $event.detail.action; showRestore = true;"
        x-show="showRestore" x-transition.opacity x-cloak style="position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 9998;">
        <div class="bg-white rounded shadow p-4" style="width: 420px; max-width: 90%; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%)" @click.outside="showRestore = false">
            <h5 class="mb-3" x-text="title"></h5>
            <p class="text-muted mb-4" x-html="message"></p>
            <div class="d-flex justify-content-end gap-2">
                <button class="btn btn-secondary" @click="showRestore = false">Cancelar</button>
                <button class="btn btn-success" @click="Livewire.dispatch(action, { id: restoreId }); showRestore = false;">Restaurar</button>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>