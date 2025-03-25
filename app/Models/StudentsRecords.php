<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\UserTracking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentsRecords extends Model
{
    use HasFactory;
    use UserTracking;
    use SoftDeletes;

    protected $table = 'students_records';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'student_id',
        'campus_id',
        'college_id',
        'program_id',
        'program_major_id',
        'acad_term_id',
        'course_id',
        'created_by',
        'updated_by',
        'final_grade',
        'removal_rating',
        'deleted_at',
        // 'is_regular',
        'student_id',
        'curricula_id',
        'course_id',
        'course_code',
        'descriptive_title',
        'final_grade',
        'removal_rating',
        'course_unit',
        'curricula_name',
        'acad_term_id',
        'attachment',
        'created_by',
        'updated_by',
    ];
    protected $casts = [
        'final_grade' => 'string',
        'removal_rating' => 'decimal:1',
        'course_unit' => 'string',
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
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
    
    public function curricula()
    {
        return $this->belongsTo(Curricula::class, 'curricula_id');
    }
    public function student()
    {
        return $this->belongsTo(Students::class, 'student_id', 'id');
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
    public function program()
    {
        return $this->belongsTo(Programs::class, 'program_id');
    }
    public function college()
    {
        return $this->belongsTo(Colleges::class, 'college_id');
    }

    public function campus()
    {
        return $this->belongsTo(Campuses::class, 'campus_id');
    }
    public function programMajor()
    {
        return $this->belongsTo(ProgramsMajor::class, 'program_major_id');
    }
}