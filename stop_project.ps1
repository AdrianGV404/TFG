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

# Elevación de permisos si hace falta detener PostgreSQL
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
$pgStatus = "no estaba en ejecucion"
if ($service -and $service.Status -eq "Running") {
    Write-Host "[POSTGRESQL] Deteniendo servicio..." -ForegroundColor Yellow
    try {
        if (Test-Admin) {
            Stop-Service -Name $pgService -ErrorAction Stop
            Start-Sleep -Seconds 2
            $pgStatus = "detenido correctamente"
            Write-Host "[POSTGRESQL] Servicio detenido correctamente" -ForegroundColor Green
        } else {
            $pgStatus = "no se pudo detener (requiere admin)"
            Write-Host "[POSTGRESQL] No se pudo detener (requiere admin)" -ForegroundColor Red
        }
    } catch {
        $pgStatus = "error al detener: $($_.Exception.Message)"
        Write-Host "[POSTGRESQL] Error al detener: $($_.Exception.Message)" -ForegroundColor Red
    }
} else {
    $pgStatus = "no estaba en ejecucion"
    Write-Host "[POSTGRESQL] Servicio no esta en ejecucion" -ForegroundColor Green
}

# Resumen final
Write-Host @"
`n===============================================
 Proceso de detencion completado               
 Componentes detenidos:                        
  - PostgreSQL: $pgStatus
===============================================
"@ -ForegroundColor Cyan
