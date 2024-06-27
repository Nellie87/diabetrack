<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{

    protected $fillable = [
        'Gender',
        'PhoneNo',
        'Address',
        'EmergencyContactName',
        'EmergencyContactPhone',
        'DoctorID',
    ];

    public function getGender(){
        return $this->Gender;
    }

    use HasFactory;
}
