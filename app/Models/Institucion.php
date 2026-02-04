<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Estado;

class Institucion extends Model
{
    use SoftDeletes;

    protected $table = 'instituciones';

    protected $fillable = [
        'nombre',
        'estado_id'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function solicitudes()
    {
        return $this->hasManyThrough(Solicitud::class, User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeBuscarGlobal($query, $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('nombre', 'LIKE', "%{$search}%")
              ->orWhereHas('estado', function ($qEstado) use ($search) {
                  $qEstado->where('nombre', 'LIKE', "%{$search}%");
              });
        });
    }

    public function scopeOrdenado($query, $sortField = 'id', $sortDirection = 'desc')
    {
        return $query->orderBy($sortField, $sortDirection);
    }
}
