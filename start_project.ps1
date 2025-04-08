param([switch]$Elevated)

function Test-Admin {
    $currentUser = New-Object Security.Principal.WindowsPrincipal([Security.Principal.WindowsIdentity]::GetCurrent())
    return $currentUser.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
}

# Configuración de encoding
$OutputEncoding = [System.Text.Encoding]::UTF8
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8
chcp 65001 > $null

# Verificación temprana del servicio PostgreSQL
$pgService = "postgresql-x64-17"
$service = Get-Service $pgService -ErrorAction SilentlyContinue

# Lógica de elevación optimizada
if ($service -and $service.Status -ne "Running" -and !(Test-Admin) -and !$Elevated) {
    Write-Host "`n[PERMISOS ADMINISTRATIVOS]" -ForegroundColor Yellow
    Write-Host "Se requieren privilegios de administrador para iniciar PostgreSQL" -ForegroundColor Yellow
    
    try {
        Start-Process powershell.exe -Verb RunAs -ArgumentList (
            "-NoProfile -ExecutionPolicy Bypass -File `"$PSCommandPath`" -Elevated"
        )
        exit
    } catch {
        Write-Host "`n[ERROR] No se pudo elevar los privilegios" -ForegroundColor Red
        Write-Host "Iniciando sin permisos de administrador..." -ForegroundColor Yellow
    }
}

Write-Host "`nIniciando el entorno de desarrollo...`n" -ForegroundColor Green

# 1. Manejo del servicio PostgreSQL en PowerShell elevado
$postgresSuccess = $true
if ($null -eq $service) {
    Write-Host "[POSTGRESQL] Servicio no encontrado" -ForegroundColor Red
    $postgresSuccess = $false
} else {
    if ($service.Status -ne "Running") {
        Write-Host "[POSTGRESQL] Iniciando servicio..." -ForegroundColor Yellow
        
        try {
            if (Test-Admin) {
                # Iniciar PostgreSQL y luego cerrar PowerShell
                Start-Service -Name $pgService -ErrorAction Stop
                Start-Sleep -Seconds 3
                Write-Host "[POSTGRESQL] Servicio iniciado correctamente" -ForegroundColor Green
            } else {
                Write-Host "[POSTGRESQL] No se pudo iniciar (requiere admin)" -ForegroundColor Red
                $postgresSuccess = $false
            }
        } catch {
            Write-Host "[POSTGRESQL] Error al iniciar: $($_.Exception.Message)" -ForegroundColor Red
            $postgresSuccess = $false
        }
    } else {
        Write-Host "[POSTGRESQL] Ya está en ejecución" -ForegroundColor Green
    }
}

# 2. Iniciar backend Django en el terminal actual de VSCode
$djangoSuccess = $true
Write-Host "`n[BACKEND] Iniciando servidor Django..." -ForegroundColor Yellow
try {
    # Ejecutar Django en el mismo terminal de VSCode
    Start-Process python -ArgumentList "manage.py runserver" -WorkingDirectory "$PSScriptRoot\backend" -PassThru -NoNewWindow
    Start-Sleep -Seconds 2
    Write-Host "[DJANGO] Servidor iniciado en http://localhost:8000" -ForegroundColor Green
} catch {
    Write-Host "[DJANGO] Error al iniciar: $($_.Exception.Message)" -ForegroundColor Red
    $djangoSuccess = $false
}

# 3. Iniciar frontend React en un nuevo PowerShell
$reactSuccess = $true
Write-Host "`n[FRONTEND] Iniciando servidor React..." -ForegroundColor Yellow
try {
    $reactStartCommand = "cd '$PSScriptRoot\frontend'; npm run dev"
    Start-Process powershell -ArgumentList @("-NoExit", "-Command", $reactStartCommand) -PassThru
    Start-Sleep -Seconds 2
    Write-Host "[REACT] Servidor iniciado en http://localhost:5173" -ForegroundColor Green
} catch {
    Write-Host "[REACT] Error al iniciar: $($_.Exception.Message)" -ForegroundColor Red
    $reactSuccess = $false
}

# Abrir navegadores
Write-Host "`nAbriendo aplicaciones en el navegador..." -ForegroundColor Cyan
Start-Process "http://localhost:8000"
Start-Process "http://localhost:5173"

# Mensaje final mejorado (compatible con PowerShell 5.1)
$overallSuccess = $postgresSuccess -and $djangoSuccess -and $reactSuccess

if ($overallSuccess) {
    Write-Host @"
`n===============================================
 ¡Todo ha funcionado correctamente!            
 Servicios activos:                           
  - PostgreSQL: $($service.Status)
  - Django: http://localhost:8000
  - React: http://localhost:5173
===============================================
"@ -ForegroundColor Green
} else {
    # Convertir booleanos a texto (alternativa al operador ternario)
    $postgresStatus = if ($postgresSuccess) { 'OK' } else { 'ERROR' }
    $djangoStatus = if ($djangoSuccess) { 'OK' } else { 'ERROR' }
    $reactStatus = if ($reactSuccess) { 'OK' } else { 'ERROR' }

    Write-Host @"
`n===============================================
 ¡Atención! Fallos detectados:                 
  - PostgreSQL: $postgresStatus
  - Django: $djangoStatus
  - React: $reactStatus
===============================================
"@ -ForegroundColor Red
}
