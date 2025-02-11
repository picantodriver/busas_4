<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UserTracking;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;


class Signatories extends Model
{
    use SoftDeletes;
    use UserTracking;
    protected $fillable = [
        'employee_name',
        'suffix',
        'employee_designation',
        'status',
        'created_by',
        'updated_by',
        'deleted_by', // Add deleted_by
    ];

    protected $dates = ['deleted_at'];

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by'); // Track who deleted the record
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

        static::deleting(function ($signatory) {
            $signatory->update(['deleted_by' => Auth::id()]);
        });
    }
}
