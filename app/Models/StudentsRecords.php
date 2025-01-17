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
        'is_regular',
    ];

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
}
