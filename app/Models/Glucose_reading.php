<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class glucose_reading extends Model
{
    protected $fillable=[
        'Datetime',
        'GlucoseLevel',
        'Notes'
    ];

    use HasFactory;
}
