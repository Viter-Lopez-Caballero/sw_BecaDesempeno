<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Calendario extends Model
{
    use SoftDeletes;

    protected $table = 'calendarios';

    protected $fillable = [
        'convocatoria_id',
        'publicacion_inicio',
        'registro_inicio',
        'registro_fin',
        'evaluacion_inicio',
        'evaluacion_fin',
        'resultados_inicio',
        'resultados_fin',
    ];

    protected $casts = [
        'publicacion_inicio' => 'date',
        'registro_inicio' => 'date',
        'registro_fin' => 'date',
        'evaluacion_inicio' => 'date',
        'evaluacion_fin' => 'date',
        'resultados_inicio' => 'date',
        'resultados_fin' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function convocatoria()
    {
        return $this->belongsTo(Convocatoria::class);
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

        return $query->whereHas('convocatoria', function ($q) use ($search) {
            $q->where('nombre', 'LIKE', "%{$search}%");
        });
    }

    public function scopeOrdenado($query, $sortField = 'id', $sortDirection = 'desc')
    {
        return $query->orderBy($sortField, $sortDirection);
    }
}
