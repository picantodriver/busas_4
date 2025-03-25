<?php

namespace App\Traits;

use Hashids\Hashids;
use Illuminate\Database\Eloquent\Model;

trait HasHashedRouteKey
{
    public function resolveRouteBinding($value, $field = null)
    {
        $hashids = app('hashids');
        $id = $hashids->decode($value);

        if (empty($id)) {
            return null;
        }

        return static::query()->find($id[0]);
    }

    public function resolveRouteBindingQuery($query, $value, $field = null)
    {
        $hashids = app('hashids');
        $id = $hashids->decode($value);

        if (empty($id)) {
            return $query->whereRaw('1 = 0'); // Force no results if ID can't be decoded
        }

        return $query->where('id', $id[0]);
    }
    public function getRouteKey()
    {
        $hashids = app('hashids');
        return $hashids->encode($this->getKey());
    }
}
