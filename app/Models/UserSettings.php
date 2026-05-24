<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSettings extends Model
{
    protected $fillable = [
        'user_id',
        'theme',
        'notif_tasks',
        'notif_alerts',
        'notif_reports',
        'notif_status_change',
        'notif_channel',
        'retention_days',
        'allow_employee_tags',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}