<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UserTracking;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Students extends Model
{
    use HasFactory;
    use UserTracking;
    use SoftDeletes;

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
        'deleted_by',
    ];

    protected $dates = ['deleted_at'];
    
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
    }
};
