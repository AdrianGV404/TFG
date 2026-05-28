<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Prunable;
use App\Models\TaskTimeEntry;
use App\Models\Label;
use App\Models\User;

class Task extends Model
{
    use HasFactory, SoftDeletes, Prunable;

    protected $fillable = [
        'project_id',
        'user_id', // Añadido para poder identificar al creador en los permisos
        'title',
        'description',
        'status',
        'priority',
        'processed_at',
        'due_date',
        'created_by',
    ];
    
    protected $casts = [
        'due_date' => 'date',
    ];

    /**
     * Relación inversa: una tarea pertenece a un proyecto.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Comprueba si la tarea está completada.
     */
    public function isDone(): bool
    {
        return $this->status === 'done';
    }

    /**
     * Mapas de prioridad numérica a etiquetas y clases CSS.
     */
    public const PRIORITY_LABELS = [
        0  => 'Muy Alta',
        1  => 'Alta',
        2  => 'Alta',
        3  => 'Alta',
        4  => 'Media',
        5  => 'Media',
        6  => 'Media',
        7  => 'Baja',
        8  => 'Baja',
        9  => 'Muy Baja',
        10 => 'Muy Baja',
    ];

    public const PRIORITY_CLASSES = [
        0  => 'very_high',
        1  => 'high',
        2  => 'high',
        3  => 'high',
        4  => 'mid',
        5  => 'mid',
        6  => 'mid',
        7  => 'low',
        8  => 'low',
        9  => 'very_low',
        10 => 'very_low',
    ];

    /**
     * Devuelve la etiqueta de prioridad según el valor numérico.
     */
    public function getPriorityLabelAttribute(): string
    {
        return self::PRIORITY_LABELS[$this->priority] ?? 'Desconocida';
    }

    /**
     * Devuelve la clase CSS asociada a la prioridad.
     */
    public function getPriorityClassAttribute(): string
    {
        return self::PRIORITY_CLASSES[$this->priority] ?? 'unknown';
    }

    public function prunable()
    {
        $days = config('prune.days_to_keep_deleted.' . self::class, 30);
        return static::onlyTrashed()
            ->where('deleted_at', '<=', now()->subDays($days));
    }

    public function timeEntries()
    {
        return $this->hasMany(TaskTimeEntry::class);
    }

    public function labels()
    {
        return $this->belongsToMany(Label::class, 'label_task'); 
    }
}