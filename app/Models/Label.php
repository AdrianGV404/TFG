<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Label extends Model
{
    use HasFactory;

    // Los campos permitidos basados en tu migración final
    protected $fillable = [
        'tenant_id',
        'name', 
    ];

    /**
     * Relación inversa: Las tareas que tienen esta etiqueta.
     */
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'label_task');
    }
}