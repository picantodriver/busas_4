<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UserTracking;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentsGraduationInfos extends Model
{
    use HasFactory;
    use UserTracking;

    protected $fillable = [
        'created_by',
        'updated_by',
        'student_id',
        'graduation_date',
        'board_approval',
        'latin_honor',
        'degree_attained',
        'dates_of_attendance',
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

    // public function students()
    // {
    //     return $this->belongsTo(Students::class, 'student_id');
    // }
}
