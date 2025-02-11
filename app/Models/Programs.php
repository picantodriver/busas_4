<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\UserTracking;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Programs extends Model
{
    use UserTracking;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'college_id',
        'program_name',
        'program_abbreviation',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $dates = ['deleted_at'];

    public function college()
    {
        return $this->belongsTo(Colleges::class);
    }
    public function curricula()
    {
        return $this->belongsTo(Curricula::class, 'curricula_id');
    }
    public function programMajors()
    {
        return $this->hasMany(ProgramsMajor::class, 'program_id');
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

        static::deleting(function ($programs) {
            $programs->update(['deleted_by' => Auth::id()]);
        });
    }
}
