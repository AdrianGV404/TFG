<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Task;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Prunable;

class Project extends Model
{
    use HasFactory, SoftDeletes, Prunable;

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

    // Soft delete en cascada
    protected static function booted()
    {
        static::deleting(function ($project) {
            if (!$project->isForceDeleting()) {
                $project->status = 'deleted';
                $project->saveQuietly();
                $project->tasks()->delete();
            } else {
                $project->tasks()->forceDelete();
            }
        });
    }
    public function restoreWithTasks()
    {
        $this->restore();
        $this->tasks()->withTrashed()->restore();
    }

    public function prunable()
    {
        $days = config('prune.days_to_keep_deleted.' . self::class, 60);
        return static::onlyTrashed()
            ->where('deleted_at', '<=', now()->subDays($days));
    }
}
