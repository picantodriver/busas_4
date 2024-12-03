<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UserTracking;

class Colleges extends Model
{
    use UserTracking;

    protected $fillable = [
        'campus_id',
        'college_name',
        'college_address',
        'created_by',
        'updated_by',
    ];

    public function campus()
    {
        return $this->belongsTo(Campuses::class);
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
