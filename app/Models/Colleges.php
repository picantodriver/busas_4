<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UserTracking;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Colleges extends Model
{
    use UserTracking;
    use HasFactory;

    protected $fillable = [
        'campus_id',
        'college_name',
        'college_address',
        'college_abbreviation',
        'created_by',
        'updated_by',
    ];

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
}
