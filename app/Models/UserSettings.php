<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSettings extends Model
{
    // Esto es vital para que el updateOrCreate funcione
    protected $fillable = [
        'user_id', 
        'theme', 
        'notif_tasks', 
        'notif_alerts', 
        'notif_reports', 
        'retention_days', 
        'allow_employee_tags'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}