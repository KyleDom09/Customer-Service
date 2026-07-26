<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SelfserviceNotification extends Model
{
    protected $fillable = ['message', 'is_read'];
}
