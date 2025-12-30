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
<body>

    @yield('content')

    @livewireScripts
</body>
</html>
