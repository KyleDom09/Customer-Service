<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarSetting extends Model 
{
    
    protected $table = 'calendar_data'; 
    
    protected $fillable = ['date', 'month', 'year', 'hour', 'minute', 'ampm'];
}