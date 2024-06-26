<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{

    protected $fillable = [
        'DateOfBirth',
        'Gender',
        'PhoneNo',
        'Address',
        'EmergencyContactName',
        'EmergencyContactPhone',
        'DoctorID',
    ];

    use HasFactory;
}
