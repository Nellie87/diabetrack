<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{

    protected $fillable=[
        'PatientID',
        'MedicationName',
        'Type',
        'Dosage',
        'Frequency',
        'StartDate',
    ];

    use HasFactory;
}
