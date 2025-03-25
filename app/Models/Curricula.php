<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UserTracking;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Curricula extends Model
{
    use UserTracking;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'acad_year_id',
        'college_id',
        'curricula_name',
        'program_id',
        'program_major_id',
        'created_by',
        'updated_by',
        'status',
        'deleted_by',
    ];

    protected $dates = ['deleted_at'];

    public function courses()
    {
        return $this->hasMany(Courses::class, 'curricula_id');
    }

    public function campus()
    {
        return $this->belongsTo(Campuses::class, 'campus_id');
    }
    public function college()
    {
        return $this->belongsTo(Colleges::class);
    }
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

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($curricula) {
            $curricula->update(['deleted_by' => Auth::id()]);
        });

        static::creating(function ($curricula) {
            if (empty($curricula->status)) {
                $curricula->status = 'unverified';
            }
        });

        static::updating(function ($curricula) {
            if ($curricula->isDirty() && $curricula->status === 'unverified') {
                $curricula->status = 'verified';
            }
        });
    }
}
