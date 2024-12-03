<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UserTracking;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AcadTerms extends Model
{
    use UserTracking;
    use HasFactory;

    protected $fillable = [
        'acad_year_id',
        'acad_term',
        'start_date',
        'end_date',
        'created_by',
        'updated_by'
    ];

    public function acadYear()
    {
        return $this->belongsTo(AcadYears::class, 'acad_year_id');
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
