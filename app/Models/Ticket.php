<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'ticket_number',
        'customer_name',
        'customer_email',
        'subject',
        'sub_subject',
        'description',
        'category',
        'priority',
        'response_minutes',
        'status',
        'agent_id',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}