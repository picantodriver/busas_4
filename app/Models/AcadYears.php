<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UserTracking;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Translation\Command\TranslationTrait;

class AcadYears extends Model
{
    use HasFactory;
    use UserTracking;
    use SoftDeletes;

    protected $fillable = [
        'year',
        'start_date',
        'end_date',
        'created_by',
        'updated_by',
        'deleted_by', // Add deleted_by
    ];

    protected $dates = ['deleted_at'];

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by'); // Track who deleted the record
    }
    public function acadTerms()
    {
        return $this->hasMany(AcadTerms::class, 'acad_year_id');
    }
    public function curricula()
    {
        return $this->belongsTo(Curricula::class, 'curricula_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($acad_year) {
            $acad_year->update(['deleted_by' => Auth::id()]);
        });
    }

    public static function booted()
    {
        static::created(function ($acadYear) {
            $terms = [
                '1st Semester',
                '2nd Semester',
                'Midyear',
                'Summer',
            ];
            foreach ($terms as $term) {
                $termName = $term;
                if (in_array($term, ['Midyear', 'Summer'])) {
                    $termName .= ' ' . substr($acadYear->year, -4); // Concatenate the last four characters of the year with a space
                } else {
                    $termName .= ' ' . $acadYear->year; // Concatenate the full year with a space
                }
                AcadTerms::create([
                    'acad_year_id' => $acadYear->id,
                    'acad_term' => $termName, // Use the termName with the space
                    'created_by' => Auth::check() ? Auth::id() : null,
                ]);
            }
        });
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
