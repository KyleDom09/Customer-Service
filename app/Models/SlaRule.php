<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlaRule extends Model 
{
    protected $fillable = ['name', 'response', 'resolution', 'active'];
    protected $casts = ['active' => 'boolean'];
}