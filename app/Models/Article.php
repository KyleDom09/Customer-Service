<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = ['title', 'description', 'is_new', 'rating'];

    protected $casts = [
        'is_new' => 'boolean',
    ];
}