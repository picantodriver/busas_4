<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UserTracking;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Courses extends Model
{
    use HasFactory;
    use UserTracking;
    use SoftDeletes;

    protected $fillable = [
        'curricula_id',
        'descriptive_title',
        'course_code',
        'course_unit',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $dates = ['deleted_at'];


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
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by'); // Track who deleted the record
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($courses) {
            $courses->update(['deleted_by' => Auth::id()]);
        });
    }
}
