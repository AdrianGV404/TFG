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
        /* Bloquea transiciones temporalmente para evitar el salto visual al cargar */
        .no-transition, .no-transition * {
            transition: none !important;
        }

        :root {
            --sidebar-width: 280px;
            --navbar-height: 56px;
            --sidebar-bg: #212529;
            --sidebar-border: #343a40;
            --sidebar-link-color: #adb5bd;
            --sidebar-title-color: #6c757d;
            --sidebar-hover-bg: rgba(255, 255, 255, 0.1);
        }

        .dark-mode-active {
            background-color: #0f172a !important;
            color: #f1f5f9 !important;
            --sidebar-bg: #111827;
            --sidebar-border: #1f2937;
            --sidebar-link-color: #94a3b8;
            --sidebar-title-color: #4b5563;
        }

        .dark-mode-active .card {
            background-color: #1e293b !important;
            color: white;
            border-color: #334155;
        }

        body {
            padding-top: {{ auth()->check() ? 'var(--navbar-height)' : '0' }};
            overflow-x: hidden;
            margin: 0;
            transition: background-color 0.3s ease;
        }

        #sidebar-panel {
            position: fixed;
            top: 0;
            left: calc(var(--sidebar-width) * -1);
            width: var(--sidebar-width);
            height: 100vh;
            background-color: var(--sidebar-bg);
            z-index: 1060;
            transition: left 0.3s ease, background-color 0.3s ease;
            border-right: 1px solid var(--sidebar-border);
        }

        .main-wrapper, .fixed-top {
            transition: margin-left 0.3s ease, width 0.3s ease;
            width: 100% !important;
            margin-left: 0;
        }

        /* Selectores vinculados estrictamente al body */
        body.sidebar-open #sidebar-panel {
            left: 0;
        }

        body.sidebar-open .main-wrapper,
        body.sidebar-open .fixed-top {
            margin-left: var(--sidebar-width) !important;
            width: calc(100% - var(--sidebar-width)) !important;
        }

        .sidebar-section-title {
            color: var(--sidebar-title-color) !important;
            transition: color 0.3s ease;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: var(--sidebar-link-color);
            text-decoration: none;
            transition: 0.2s;
            border-radius: 8px;
            margin: 4px 10px;
        }

        .sidebar-link:hover, .sidebar-link.active {
            background: var(--sidebar-hover-bg);
            color: #fff;
        }

        .sidebar-link i { width: 25px; }
        [x-cloak] { display: none !important; }
    </style>

    @if(auth()->check())
    <script>
        (function() {
            const sidebarStatus = localStorage.getItem('sidebar-status') === 'true';

            if (sidebarStatus) {
                document.documentElement.classList.add('sidebar-open', 'no-transition');
            }
        })();
    </script>
    @endif
</head>

<body
@if(auth()->check())
    x-data="{ 
        sidebarOpen: localStorage.getItem('sidebar-status') === 'true',
        theme: '{{ auth()->user()?->settings?->theme ?? 'light' }}',

        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
            localStorage.setItem('sidebar-status', this.sidebarOpen);
            document.documentElement.classList.remove('sidebar-open');
        }
    }"

    x-init="
        if (sidebarOpen) {
            document.documentElement.classList.remove('sidebar-open');
        }

        setTimeout(() => {
            document.documentElement.classList.remove('no-transition');
            document.body.classList.remove('no-transition');
        }, 100);
    "

    @theme-updated.window="theme = $event.detail.theme"

    :class="{ 
        'sidebar-open': sidebarOpen,
        'dark-mode-active': theme === 'dark'
    }"
@endif
>

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
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ Route::is('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>

                <a href="{{ route('calendario') }}" class="sidebar-link {{ Route::is('calendario') ? 'active' : '' }}">
                    <i class="fas fa-calendar"></i> Calendario
                </a>
                @if (auth()->user()->isAdmin())
                    <div class="px-4 mt-4 mb-2 text-uppercase small fw-bold sidebar-section-title" style="font-size: 0.7rem;">
                        Administración</div>
                    <a href="{{ route('users.index') }}" class="sidebar-link">
                        <i class="fas fa-users-cog"></i> Gestión Usuarios
                    </a>
                    <a href="{{ route('users.create') }}" class="sidebar-link">
                        <i class="fas fa-user-plus"></i> Nuevo Usuario
                    </a>
                @endif

                <div class="px-4 mt-4 mb-2 text-uppercase small fw-bold sidebar-section-title" style="font-size: 0.7rem;">Cuenta</div>
                
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>