<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingItem extends Model
{
    protected $fillable = ['title', 'icon', 'problem', 'steps', 'is_new', 'rating'];

    protected $casts = [
        'steps' => 'array',
        'is_new' => 'boolean',
    ];
}