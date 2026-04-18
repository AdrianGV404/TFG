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
            padding-top: {{ auth()->check() ? 'var(--navbar-height)' : '0' }};
            overflow-x: hidden;
            margin: 0;
        }

        #sidebar-panel {
            position: fixed;
            top: 0;
            left: calc(var(--sidebar-width) * -1);
            width: var(--sidebar-width);
            height: 100vh;
            background-color: #212529;
            z-index: 1060;
            transition: left 0.3s ease;
            border-right: 1px solid #343a40;
        }

        .main-wrapper,
        .fixed-top {
            transition: margin-left 0.3s ease, width 0.3s ease;
            width: 100% !important;
            margin-left: 0;
        }

        .sidebar-open #sidebar-panel {
            left: 0;
        }

        .sidebar-open .main-wrapper,
        .sidebar-open .fixed-top {
            margin-left: var(--sidebar-width) !important;
            width: calc(100% - var(--sidebar-width)) !important;
        }

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

        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .sidebar-link i {
            width: 25px;
        }

        [x-cloak] {
            display: none !important;
        }

        .dark-mode { background-color: #121212; color: #eee; }
        .dark-mode .card { background-color: #1e1e1e; color: white; border-color: #333; }
    </style>
</head>

<body 
    x-data="{ 
        sidebarOpen: {{ auth()->check() ? "localStorage.getItem('sidebar-status') === 'true'" : 'false' }},
        theme: '{{ auth()->user()->settings['theme'] ?? 'light' }}',
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
            localStorage.setItem('sidebar-status', this.sidebarOpen);
        }
    }" 
    x-init="
        if (!{{ auth()->check() ? 'true' : 'false' }}) {
            localStorage.removeItem('sidebar-status');
        }
    "
    :class="{ 'sidebar-open': sidebarOpen, 'dark-mode': theme === 'dark' }"
    @theme-updated.window="theme = $event.detail.theme"
>

    {{-- SIDEBAR --}}
    @auth
        <aside id="sidebar-panel" class="shadow">
            <div class="p-3 border-bottom border-secondary d-flex justify-content-between align-items-center">
                <span class="text-white fw-bold fs-5"><i class="fas fa-th-large me-2"></i>Menú</span>
                <button class="btn btn-sm text-white" @click="toggleSidebar()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="mt-3">
                <a href="{{ route('profile') }}" class="sidebar-link {{ Route::is('profile') ? 'active' : '' }}">
                    <i class="fas fa-user-circle"></i> Mi Perfil
                </a>
                <a href="{{ route('projects') }}" class="sidebar-link">
                    <i class="fas fa-home"></i> Inicio / Proyectos
                </a>
                
                @if (auth()->user()->isAdmin())
                    <div class="px-4 mt-4 mb-2 text-uppercase small text-muted fw-bold" style="font-size: 0.7rem;">
                        Administración</div>
                    <a href="{{ route('users.index') }}" class="sidebar-link">
                        <i class="fas fa-users-cog"></i> Gestión Usuarios
                    </a>
                    <a href="{{ route('users.create') }}" class="sidebar-link">
                        <i class="fas fa-user-plus"></i> Nuevo Usuario
                    </a>
                @endif

                <div class="px-4 mt-4 mb-2 text-uppercase small text-muted fw-bold" style="font-size: 0.7rem;">Cuenta</div>
                
                <form method="POST" action="{{ route('logout') }}" class="m-0"
                      @submit="localStorage.removeItem('sidebar-status')">
                    @csrf
                    <button type="submit" class="sidebar-link border-0 bg-transparent w-100 text-start">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </button>
                </form>
            </div>
        </aside>

        @include('livewire.partials.navbar')
    @endauth

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

    @auth
        <!-- Modales igual que antes -->
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>