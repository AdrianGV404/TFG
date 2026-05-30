<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoricoHorasDia extends Model
{
    protected $table = 'historico_horas_dia';

    protected $fillable = [
        'tenant_id',
        'project_id',
        'dia',
        'horas',
    ];

    protected $casts = [
        'dia'   => 'date',
        'horas' => 'decimal:2',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    // ── Helper estático ───────────────────────────────────────────────────────

    /**
     * Acumula los segundos dados en el histórico del día correspondiente.
     *
     * Usa updateOrCreate con la clave única (tenant_id, project_id, dia)
     * para hacer un upsert seguro: si ya existe la fila la incrementa,
     * si no existe la crea con el valor inicial.
     *
     * @param  int    $tenantId
     * @param  int    $projectId
     * @param  string $dia        Fecha en formato 'Y-m-d'
     * @param  int    $segundos   Segundos a acumular
     */
    public static function acumular(
        int    $tenantId,
        int    $projectId,
        string $dia,
        int    $segundos
    ): void {
        if ($segundos <= 0) {
            return;
        }

        $horas = round($segundos / 3600, 2);

        // Atomic increment: busca la fila y suma, o la crea con el valor inicial
        static::query()
            ->where('tenant_id',  $tenantId)
            ->where('project_id', $projectId)
            ->where('dia',        $dia)
            ->lockForUpdate()   // evita race conditions con múltiples workers
            ->exists()
            ? static::query()
                ->where('tenant_id',  $tenantId)
                ->where('project_id', $projectId)
                ->where('dia',        $dia)
                ->increment('horas', $horas)
            : static::create([
                'tenant_id'  => $tenantId,
                'project_id' => $projectId,
                'dia'        => $dia,
                'horas'      => $horas,
            ]);
    }
}