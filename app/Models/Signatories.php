<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UserTracking;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Signatories extends Model
{

    use UserTracking;
    use SoftDeletes;
    protected $fillable = [
        'employee_name',
        'suffix',
        'employee_designation',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    public $timestamps = true;

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

        static::deleting(function ($signatories) {
            $signatories->update(['deleted_by' => Auth::id()]);
        });

        static::creating(function ($signatories) {
            if (empty($signatories->status)) {
                $signatories->status = 'unverified';
            }
        });

        static::updating(function ($signatories) {
            if ($signatories->isDirty() && $signatories->status === 'unverified') {
                $signatories->status = 'verified';
            }
        });
    }
}
