<?php

namespace App\Livewire\Traits;

use App\Models\HistoricoHorasDia;
use App\Models\Task;
use App\Models\TaskTimeEntry;

/**
 * Trait RegistraHistoricoHoras
 *
 * Centraliza la lógica de registro en historico_horas_dia
 * y el cambio automático de estado de la tarea al grabar/detener tiempo.
 *
 * Regla de negocio:
 *   - Al INICIAR grabación → estado pasa a 'in_progress'
 *   - Al DETENER grabación → estado pasa a 'on_hold'  (en pausa)
 *     (salvo que ya estuviera en 'done' o 'testing', que no se toca)
 */
trait RegistraHistoricoHoras
{
    /**
     * Inicia el contador de tiempo en una tarea.
     * Crea la entrada TaskTimeEntry y cambia el estado a 'in_progress'.
     *
     * @param  Task  $task
     * @return TaskTimeEntry  La entrada recién creada
     */
    protected function iniciarGrabacion(Task $task): TaskTimeEntry
    {
        // Cambiar estado a "en progreso" solo si no está en un estado final o de pruebas
        if (! in_array($task->status, ['done', 'testing'])) {
            $task->update(['status' => 'in_progress']);
        }

        return TaskTimeEntry::create([
            'task_id'    => $task->id,
            'user_id'    => auth()->id(),
            'started_at' => now(),
            'is_running' => true,
        ]);
    }

    /**
     * Detiene un registro de tiempo activo:
     *   1. Cierra la entrada (ended_at, duration_seconds, is_running = false).
     *   2. Cambia el estado de la tarea a 'on_hold' (en pausa).
     *   3. Acumula las horas en historico_horas_dia.
     *
     * @param  TaskTimeEntry  $entry   Entrada en curso (is_running = true)
     * @param  Task           $task    Tarea a la que pertenece la entrada
     */
    protected function detenerYRegistrar(TaskTimeEntry $entry, Task $task): void
    {
        $now      = now();
        $segundos = $now->diffInSeconds($entry->started_at);

        // 1. Cerrar la entrada de tiempo
        $entry->update([
            'ended_at'         => $now,
            'duration_seconds' => $segundos,
            'is_running'       => false,
        ]);

        // 2. Cambiar estado a "en pausa" salvo que sea final o de pruebas
        if (! in_array($task->status, ['done', 'testing'])) {
            $task->update(['status' => 'on_hold']);
        }

        // 3. Acumular en el histórico diario
        //    El día que cuenta es el de started_at
        $this->acumularEnHistorico($task, $entry->started_at->toDateString(), $segundos);
    }

    /**
     * Registra segundos de tiempo manual en el histórico del día actual.
     * No modifica el estado de la tarea (es una entrada retroactiva).
     *
     * @param  Task  $task
     * @param  int   $segundos
     */
    protected function registrarTiempoManual(Task $task, int $segundos): void
    {
        $this->acumularEnHistorico($task, now()->toDateString(), $segundos);
    }

    /**
     * Llama al modelo para acumular horas, resolviendo tenant y proyecto desde la tarea.
     */
    private function acumularEnHistorico(Task $task, string $dia, int $segundos): void
    {
        $tenantId = $task->tenant_id
            ?? $task->project?->tenant_id
            ?? null;

        if (! $tenantId || ! $task->project_id || $segundos <= 0) {
            return;
        }

        HistoricoHorasDia::acumular(
            tenantId:  $tenantId,
            projectId: $task->project_id,
            dia:       $dia,
            segundos:  $segundos
        );
    }
}