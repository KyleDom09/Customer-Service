<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Communication extends Model
{
    protected $table = 'communications';

    protected $fillable = [
        'customer_name',
        'customer_email',
        'date',
        'type',
        'subject',
        'staff',
        'status',
        'priority',
        'resp_time',
        'resolved_at',
        'agent_id',
        'ticket_id',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}