<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    protected $fillable = [
        'name',
        'role',
        'team',
        'avatar',
        'active_status',
        'total_assigned',
        'total_resolved',
        'avg_response_minutes',
        'csat_score',
    ];
}
