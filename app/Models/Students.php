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
        'is_regular',
<<<<<<< Updated upstream
=======
        'deleted_by',
        'status',
>>>>>>> Stashed changes
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
        return $this->hasOne(StudentsGraduationInfos::class, 'student_id');
    }

    public function records()
    {
        return $this->hasMany(StudentsRecords::class, 'student_id');
    }

    public function registrationInfos()
    {
        return $this->hasOne(StudentsRegistrationInfos::class, 'student_id');
    }
<<<<<<< Updated upstream
=======

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by'); // Track who deleted the record
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($students) {
            $students->update(['deleted_by' => Auth::id()]);
        });

        static::creating(function ($student) {
            if (empty($student->status)) {
                $student->status = 'unverified';
            }
        });

        static::updating(function ($student) {
            if ($student->isDirty() && $student->status === 'unverified') {
                $student->status = 'verified';
            }
        });
    }
>>>>>>> Stashed changes
};
