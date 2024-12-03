<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UserTracking;

class Curricula extends Model
{
    use UserTracking;

    protected $fillable = [
        'acad_year_id',
        'curricula_name',
        'program_id',
        'program_major_id',
        'created_by',
        'updated_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
