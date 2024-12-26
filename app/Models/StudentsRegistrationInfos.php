<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\UserTracking;

class StudentsRegistrationInfos extends Model
{
    use HasFactory;
    use UserTracking;

    protected $fillable = [
        'last_school_attended',
        'last_year_attended',
        'category',
        'created_by',
        'updated_by',
    ];

    public function student()
    {
        return $this->hasOne(Students::class, 'student_id');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
