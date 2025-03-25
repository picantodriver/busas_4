<?php

namespace App\Models;

use App\Traits\HasHashedRouteKey;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UserTracking;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;

class Students extends Model
{
    use HasFactory;
    use UserTracking;
    use SoftDeletes;
    use HasHashedRouteKey;

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
        'student_type',
        'is_regular',
        'deleted_by',
        'region',
        'province',
        'city_municipality',
        'region_name',
        'province_name',
        'city_municipality_name',
        'country',
        'status',
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
        return $this->hasMany(StudentsGraduationInfos::class, 'student_id');
    }

    public function ladderized(): HasMany
    {
        return $this->hasMany(Ladderized::class, 'student_id');
    }

    public function records()
    {
        return $this->hasMany(StudentsRecords::class, 'student_id');
    }

    public function registrationInfos()
    {
        return $this->hasMany(StudentsRegistrationInfos::class, 'student_id');
    }
    
    public function college()
    {
        return $this->belongsTo(Colleges::class, 'college_id');
    }
    public function campus()
    {
        return $this->belongsTo(Campuses::class, 'campus_id');
    }
    public function studentRecords()
    {
        return $this->hasMany(StudentsRecords::class, 'student_id', 'id');
    }

    // If you want to specifically get the record with attachments
    public function recordWithAttachment()
    {
        return $this->hasMany(StudentsRecords::class, 'student_id', 'id')
                    ->whereNotNull('attachment')
                    ->first();
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

        // static::creating(function ($model) {
        //     $model->uuid = (string) Str::uuid();
        // });
        }

        // public function getRouteKeyName()
        // {
        //     return 'uuid';
        // }
    

        // public function up()
        // {
        // Schema::table('students', function (Blueprint $table) {
        //     $table->uuid('uuid')->unique()->after('id');
        // });
        // }
};
