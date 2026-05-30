<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    // Tipos de notificación
    const TYPE_ASSIGNMENT    = 'assignment';
    const TYPE_DUE_SOON      = 'due_soon';
    const TYPE_STATUS_CHANGE = 'status_change';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'body',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data'    => 'array',
        'read_at' => 'datetime',
    ];

    // ── Relaciones ───────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    // ── Icono por tipo ───────────────────────────────────────────────────────

    public function getIconAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_ASSIGNMENT    => 'fa-user-plus',
            self::TYPE_DUE_SOON      => 'fa-clock',
            self::TYPE_STATUS_CHANGE => 'fa-arrows-rotate',
            default                  => 'fa-bell',
        };
    }

    public function getIconColorAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_ASSIGNMENT    => '#3b82f6',
            self::TYPE_DUE_SOON      => '#f59e0b',
            self::TYPE_STATUS_CHANGE => '#8b5cf6',
            default                  => '#64748b',
        };
    }
}