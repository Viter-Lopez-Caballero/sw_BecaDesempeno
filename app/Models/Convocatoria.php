<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Convocatoria extends Model
{
    use SoftDeletes;

    protected $table = 'convocatorias';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
        'archivo_path',
        'archivo_nombre',
        'archivo_tipo',
        'archivo_size',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function calendario()
    {
        return $this->hasOne(Calendario::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'convocatoria_user')
            ->withPivot('estado', 'fecha_inscripcion')
            ->withTimestamps();
    }

    public function documentosCatalogo()
    {
        return $this->belongsToMany(DocumentoCatalogo::class, 'convocatoria_documento', 'convocatoria_id', 'documento_catalogo_id')
            ->withPivot('es_obligatorio')
            ->withTimestamps();
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

        return $query->where('nombre', 'LIKE', "%{$search}%")
            ->orWhere('estado', 'LIKE', "%{$search}%");
    }

    public function scopeOrdenado($query, $sortField = 'id', $sortDirection = 'desc')
    {
        return $query->orderBy($sortField, $sortDirection);
    }
}
