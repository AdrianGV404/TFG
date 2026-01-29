<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{    
    use HasFactory;
    
    protected $fillable = [
        'project_id',
        'title',
        'description',
        'status',
        'priority',
        'processed_at',

    ];
    /**
     * Relación inversa: una tarea pertenece a un proyecto.
     * Permite acceder al proyecto asociado mediante $task->project.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function isDone(): bool
    {
        return $this->status === 'done';
    }
}