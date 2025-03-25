<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UserTracking;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

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
        'status',
        'deleted_by',
    ];

    protected $dates = ['deleted_at'];
    public function acadTerms()
    {
        return $this->hasMany(AcadTerms::class, 'acad_year_id');
    }
    public function curricula()
    {
        return $this->belongsTo(Curricula::class, 'curricula_id');
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

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($acadYear) {
            $acadYear->update(['deleted_by' => Auth::id()]);
        });

        static::creating(function ($acadYear) {
            if (empty($acadYear->status)) {
                $acadYear->status = 'unverified';
            }
        });

        static::updating(function ($acadYear) {
            if ($acadYear->isDirty() && $acadYear->status === 'unverified') {
                $acadYear->status = 'verified';
            }
        });
    }
}
