<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'target_id',
        'type',
        'event',
        'description',
        'performed_by',
        'severity',
        'logged_at',
    ];

    protected $casts = [
        'logged_at' => 'datetime',
    ];
}
