<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// Importaciones necesarias para la evaluación de permisos
use App\Models\UserPermission;
use App\Models\Task;
use App\Models\Project;

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
        'password'          => 'hashed',
    ];

    // ── Relaciones ───────────────────────────────────────────────────────────

    public function settings(): HasOne
    {
        return $this->hasOne(UserSettings::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function createdProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'created_by');
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TaskTimeEntry::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)->latest();
    }

    /**
     * Relación con la tabla de permisos personalizados.
     */
    public function customPermissions(): HasOne
    {
        return $this->hasOne(UserPermission::class);
    }

    // ── Sistema de Permisos Personalizados ───────────────────────────────────

    /**
     * Evalúa si el usuario actual tiene permisos aplicando las reglas de persistencia.
     * * @param string $action ('create_project', 'create_task', 'edit_task', 'delete_task', 'reassign_users')
     * @param mixed $target Objeto Task o Project sobre el que se quiere operar
     * @return bool
     */
    public function hasCustomPermission(string $action, $target = null): bool
    {
        // Administradores globales (o Responsables) tienen acceso total por defecto
        if ($this->isAdmin()) {
            return true;
        }

        $perms = $this->customPermissions;
        if (!$perms) {
            return false; // Sin registro de permisos, se deniega la acción
        }

        // 1. Acciones simples sin contexto complejo
        if ($action === 'create_project') {
            return $perms->can_create_projects;
        }

        if ($action === 'reassign_users') {
            return $perms->can_reassign_users;
        }

        // 2. Acciones complejas con regla de persistencia jerárquica
        if (in_array($action, ['create_task', 'edit_task', 'delete_task'])) {
            
            $can_any = false;
            $can_project_by_others = false;

            if ($action === 'create_task') {
                $can_any = $perms->can_create_any_task;
                $can_project_by_others = $perms->can_create_project_tasks_by_others;
            } elseif ($action === 'edit_task') {
                $can_any = $perms->can_edit_any_task;
                $can_project_by_others = $perms->can_edit_project_tasks_by_others;
            } elseif ($action === 'delete_task') {
                $can_any = $perms->can_delete_any_task;
                $can_project_by_others = $perms->can_delete_project_tasks_by_others;
            }

            // REGLA: Si 'any' está activa y 'project' no, persiste 'any' (permite hacer la acción sin filtro adicional).
            // Sino, persiste la otra.
            if ($can_any && !$can_project_by_others) {
                return true;
            } else {
                // Si la que persiste es 'la otra' pero tampoco la tiene activa, denegamos.
                if (!$can_project_by_others) {
                    return false;
                }

                // Para evaluar si es 'de su proyecto' necesitamos obligatoriamente el contexto (Project o Task)
                if (!$target) {
                    return false; 
                }

                $project = null;
                $taskCreatorId = null;

                if ($target instanceof Task) {
                    $project = $target->project;
                    // Extraemos el creador usando user_id o created_by según tu nomenclatura
                    $taskCreatorId = $target->user_id ?? $target->created_by; 
                } elseif ($target instanceof Project) {
                    $project = $target;
                }

                if (!$project) {
                    return false;
                }

                // Evaluar si es "su proyecto" mediante el tenant o relación existente
                $isMyProject = ($this->tenant_id === $project->tenant_id);

                // Evaluar si "fue creada por otro usuario"
                $isCreatedByAnotherUser = ($taskCreatorId !== null && $taskCreatorId !== $this->id);

                if ($action === 'create_task') {
                    // Para crear una tarea en su proyecto solo requiere pertenecer al mismo
                    return $isMyProject;
                }

                // Para editar y borrar, debe ser su proyecto Y creada por otro usuario (según el nombre del permiso)
                return $isMyProject && $isCreatedByAnotherUser;
            }
        }

        return false;
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profile_photo_path) {
            return asset('storage/' . $this->profile_photo_path);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }
}