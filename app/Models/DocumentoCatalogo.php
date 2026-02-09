<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentoCatalogo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'documentos_catalogo';

    protected $fillable = [
        'nombre',
        'descripcion',
        'archivo_path',
        'archivo_nombre',
        'archivo_tipo',
        'archivo_size',
        'activo',
        'es_fundamental',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'es_fundamental' => 'boolean',
    ];

    /**
     * Relación con Convocatorias (muchos a muchos)
     */
    public function convocatorias(): BelongsToMany
    {
        return $this->belongsToMany(Convocatoria::class, 'convocatoria_documento', 'documento_catalogo_id', 'convocatoria_id')
            ->withPivot('es_obligatorio')
            ->withTimestamps();
    }

    /**
     * Scope para documentos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para ordenar por nombre
     */
    public function scopeOrdenado($query)
    {
        return $query->orderBy('nombre', 'asc');
    }
}
