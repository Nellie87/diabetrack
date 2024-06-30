<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diet extends Model
{
    protected $fillable=[
        'PatientID',
        'Datetime',
        'MealType',
        'FoodItems',
        'Carbohydrates',
        'Notes',
    ];

    use HasFactory;
}
