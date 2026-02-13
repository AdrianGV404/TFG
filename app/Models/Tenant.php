<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
    ];

    // Relación con usuarios
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Relación con proyectos
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
