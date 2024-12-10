<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\UserTracking;

class Campuses extends Model
{
    use UserTracking;
    use HasFactory;

    protected $fillable = [
        'campus_name',
        'campus_address',
        'created_by',
        'updated_by',
    ];

    public function colleges()
    {
        return $this->hasMany(Colleges::class, 'campus_id');
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
