<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UserTracking;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Courses extends Model
{
    use HasFactory;
    use UserTracking;

    protected $fillable = [
        'descriptive_title',
        'course_code',
        'course_unit',
        'program_id',
        'program_major_id',
        'created_by',
        'updated_by',
    ];

    public function programs()
    {
        return $this->belongsTo(Programs::class, 'program_id');
    }
    public function programMajor()
    {
        return $this->belongsTo(ProgramsMajor::class, 'program_major_id');
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
