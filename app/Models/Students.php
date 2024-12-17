<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UserTracking;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Students extends Model
{
    use HasFactory;
    use UserTracking;

    protected $fillable = [
        'created_by',
        'updated_by',
        'last_name',
        'first_name',
        'middle_name',
        'suffix',
        'sex',
        'address',
        'birthdate',
        'birthplace',
        'gwa',
        'nstp_number',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function graduationInfos()
    {
        return $this->hasMany(StudentsGraduationInfos::class, 'student_id');
    }

    public function records()
    {
        return $this->hasMany(StudentsRecords::class, 'student_id');
    }

    public function registrationInfos()
    {
        return $this->hasMany(StudentsRegistrationInfos::class, 'student_id');
    }
};
