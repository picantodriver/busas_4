<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\UserTracking;

class ProgramsMajor extends Model
{
    use HasFactory;
    use UserTracking;

    protected $fillable = [
        'program_id',
        'program_major_name',
        'program_major_abbreviation',
        'created_by',
        'updated_by',
    ];

    public function program()
    {
        return $this->belongsTo(Programs::class, 'program_id');
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
