<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefundRequest extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'image_path',
        'status',
    ];
}