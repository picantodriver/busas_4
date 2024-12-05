<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\UserTracking;

class Programs extends Model
{
    use UserTracking;
    use HasFactory;

    protected $fillable = [
        'college_id',
        'program_name',
        'program_abbreviation',
        'created_by',
        'updated_by',
    ];


    public function college()
    {
        return $this->belongsTo(Colleges::class);
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
}
