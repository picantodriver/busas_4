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
        'curricula_id',
        'descriptive_title',
        'course_code',
        'course_unit',
        'created_by',
        'updated_by',
    ];


    public function curricula()
    {
        return $this->belongsTo(Curricula::class, 'curricula_id');
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
