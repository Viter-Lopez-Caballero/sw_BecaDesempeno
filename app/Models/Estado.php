<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $fillable = [
        'nombre',
        'abreviatura'
    ];

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeOrdenado($query, $sortField = 'id', $sortDirection = 'asc')
    {
        return $query->orderBy($sortField, $sortDirection);
    }
}
