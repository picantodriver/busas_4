<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait UserTracking
{
    public static function bootUserTracking()
    {
        static::creating(function ($model) {
            if (Auth::check()) {{
                    $model->created_by = Auth::id();
                }
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {{
                    $model->updated_by = Auth::id();
                }
            }
        });
    }
}
