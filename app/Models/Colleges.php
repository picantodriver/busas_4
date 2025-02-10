<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UserTracking;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Colleges extends Model
{
    use UserTracking;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'campus_id',
        'college_name',
        'college_address',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $dates = ['deleted_at'];

    public function campus()
    {
        return $this->belongsTo(Campuses::class, 'campus_id');
    }
    public function programs()
    {
        return $this->hasMany(Programs::class, 'college_id');
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

        static::deleting(function ($colleges) {
            $colleges->update(['deleted_by' => Auth::id()]);
        });
    }
}
