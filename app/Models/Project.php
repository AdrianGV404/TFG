<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Task;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
    ];
    
    /**
     * Relación uno a muchos con Task.
     * Un proyecto puede tener múltiples tareas asociadas.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
