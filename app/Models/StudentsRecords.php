<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\UserTracking;

class StudentsRecords extends Model
{
    use HasFactory;
    use UserTracking;

    protected $fillable = [
        'created_by',
        'updated_by',
        'final_grade',
        'removal_rating',
        'student_id',
        'acad_term_id',
        'deleted_at',
        //'is_regular',
        'student_id',
        'curricula_id',
        'course_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (Auth::hasUser()) {
                $model->created_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::hasUser()) {
                $model->updated_by = Auth::id();
            }
        });
    }

    public function student()
    {
        return $this->belongsTo(Students::class, 'student_id');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function course()
    {
        return $this->belongsTo(Courses::class, 'course_id');
    }
    public function academicTerm()
    {
        return $this->belongsTo(AcadTerms::class, 'acad_term_id');
    }
    public function curricula()
    {
        return $this->belongsTo(Curricula::class, 'curricula_id');
    }
}
