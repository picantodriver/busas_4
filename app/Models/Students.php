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
        'curriculum_id',
        'is_regular',
        'region',
        'province',
        'city_municipality',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($student) {
            // Get the authenticated user's ID
            $userId = auth()->id();

            // If records were submitted with the form
            if (request()->has('records_regular')) {
                foreach (request()->input('records_regular') as $record) {
                    $student->records()->create([
                        'acad_term_id' => $record['acad_term_id'],
                        'course_id' => $record['course_id'],
                        'final_grade' => $record['final_grade'],
                        'removal_rating' => $record['removal_rating'] ?? null,
                        'created_by' => $userId,
                    ]);
                }
            }
            static::creating(function ($student) {
                $student->created_by = auth()->id();
            });

            static::updating(function ($student) {
                $student->updated_by = auth()->id();
            });
        });
    }

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
};
