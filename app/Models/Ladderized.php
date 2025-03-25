<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ladderized extends Model
{
    protected $table = 'ladderized';

    protected $fillable = [
        'board_approval',
        'latin_honor',
        'program_cert',
        'graduation_date',
        'student_id',
        'acad_year_id',
    ];

    protected $casts = [
        'graduation_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Students::class, 'student_id');
    }
}