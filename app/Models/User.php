<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne; // Importación recomendada

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'tenant_id',
        'role',
        'profile_photo_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * RELACIÓN: Esta es la parte que faltaba.
     * Un usuario tiene una fila de configuración.
     */
    public function settings(): HasOne
    {
        return $this->hasOne(UserSettings::class);
    }

    // Relación con Tenant
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // Helper
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Obtener la URL de la foto de perfil o una por defecto.
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            return asset('storage/' . $this->profile_photo_path);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }
    // Proyectos creados por el usuario
    public function createdProjects()
    {
        return $this->hasMany(Project::class, 'created_by');
    }

    // Proyectos donde está asignado
    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }
    
    public function timeEntries()
    {
        return $this->hasMany(TaskTimeEntry::class);
    }
}