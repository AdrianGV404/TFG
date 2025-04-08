param([switch]$Elevated)

# Configuración de encoding (UTF-8)
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8
$OutputEncoding = [System.Text.Encoding]::UTF8
chcp 65001 > $null  # Cambiar página de códigos de la consola

function Test-Admin {
    $currentUser = New-Object Security.Principal.WindowsPrincipal([Security.Principal.WindowsIdentity]::GetCurrent())
    return $currentUser.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
}

# Verificación de PostgreSQL
$pgService = "postgresql-x64-17"
$service = Get-Service $pgService -ErrorAction SilentlyContinue

# Elevación de permisos
if ($service -and $service.Status -eq "Running" -and !(Test-Admin) -and !$Elevated) {
    Write-Host "`n[PERMISOS ADMINISTRATIVOS]" -ForegroundColor Yellow
    Write-Host "Se requieren privilegios de administrador para detener PostgreSQL" -ForegroundColor Yellow
    
    try {
        Start-Process powershell.exe -Verb RunAs -ArgumentList (
            "-NoProfile -ExecutionPolicy Bypass -File `"$PSCommandPath`" -Elevated"
        )
        exit
    } catch {
        Write-Host "`n[ERROR] No se pudo elevar los privilegios" -ForegroundColor Red
        Write-Host "Deteniendo componentes sin permisos de administrador..." -ForegroundColor Yellow
    }
}

Write-Host "`nDeteniendo el entorno de desarrollo...`n" -ForegroundColor Cyan

# 1. Detener PostgreSQL
if ($service -and $service.Status -eq "Running") {
    Write-Host "[POSTGRESQL] Deteniendo servicio..." -ForegroundColor Yellow
    try {
        if (Test-Admin) {
            Stop-Service -Name $pgService -ErrorAction Stop
            Start-Sleep -Seconds 2
            Write-Host "[POSTGRESQL] Servicio detenido correctamente" -ForegroundColor Green
        } else {
            Write-Host "[POSTGRESQL] No se pudo detener (requiere admin)" -ForegroundColor Red
        }
    } catch {
        Write-Host "[POSTGRESQL] Error al detener: $($_.Exception.Message)" -ForegroundColor Red
    }
} else {
    Write-Host "[POSTGRESQL] Servicio no está en ejecución" -ForegroundColor Green
}

# 2. Detener procesos de Vite en PowerShell (puerto 5173)
Write-Host "`n[PUERTO 5173] Buscando procesos VITE..." -ForegroundColor Yellow
try {
    # Detectar procesos de PowerShell que estén ejecutando el comando de Vite (npm run dev)
    $viteProcesses = Get-Process -Name "powershell" -ErrorAction SilentlyContinue |
                     Where-Object { $_.CommandLine -like "*npm run dev*" }

    if ($viteProcesses.Count -gt 0) {
        foreach ($process in $viteProcesses) {
            try {
                Write-Host "• Deteniendo proceso VITE '$($process.ProcessName)' (PID: $($process.Id))" -ForegroundColor Cyan
                # Cerrar la ventana de PowerShell asociada
                Stop-Process -Id $process.Id -Force -ErrorAction Stop
            } catch {
                Write-Host "  [ERROR] No se pudo detener PID $($process.Id): $($_.Exception.Message)" -ForegroundColor Red
            }
        }
    } else {
        Write-Host "[PUERTO 5173] Ningún proceso de Vite encontrado" -ForegroundColor Green
    }
} catch {
    Write-Host "[PUERTO 5173] Error: $($_.Exception.Message)" -ForegroundColor Red
}

# 3. Detener procesos de Django en PowerShell (puerto 8000)
Write-Host "`n[PUERTO 8000] Buscando procesos Django..." -ForegroundColor Yellow
try {
    # Detectar procesos de PowerShell que estén ejecutando el comando de Django (manage.py runserver)
    $djangoProcesses = Get-Process -Name "powershell" -ErrorAction SilentlyContinue |
                       Where-Object { $_.CommandLine -like "*manage.py runserver*" }

    if ($djangoProcesses.Count -gt 0) {
        foreach ($process in $djangoProcesses) {
            try {
                Write-Host "• Deteniendo proceso Django '$($process.ProcessName)' (PID: $($process.Id))" -ForegroundColor Cyan
                # Cerrar la ventana de PowerShell asociada
                Stop-Process -Id $process.Id -Force -ErrorAction Stop
            } catch {
                Write-Host "  [ERROR] No se pudo detener PID $($process.Id): $($_.Exception.Message)" -ForegroundColor Red
            }
        }
    } else {
        Write-Host "[PUERTO 8000] Ningún proceso de Django encontrado" -ForegroundColor Green
    }
} catch {
    Write-Host "[PUERTO 8000] Error: $($_.Exception.Message)" -ForegroundColor Red
}

# Mensaje final con encoding UTF-8
Write-Host @"
`n===============================================
 Proceso de detención completado               
 Componentes detenidos:                        
  - PostgreSQL: $($service.Status)
  - Vite (puerto 5173)
  - Django (puerto 8000)
===============================================
"@ -ForegroundColor Cyan
