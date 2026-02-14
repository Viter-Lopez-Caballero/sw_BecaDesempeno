<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'announcements';

    protected $fillable = [
        'name',
        'description',
        'image_path',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */
    /**
     * Obtiene los usuarios (docentes) asociados a esta convocatoria.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'announcement_user')
            ->withPivot('status', 'registration_date')
            ->withTimestamps();
    }

    /**
     * Obtiene los documentos de catálogo requeridos para esta convocatoria.
     */
    public function catalogDocuments(): BelongsToMany
    {
        return $this->belongsToMany(CatalogDocument::class, 'announcement_document', 'announcement_id', 'catalog_document_id')
            ->withPivot('is_mandatory')
            ->withTimestamps();
    }

    /**
     * Obtiene el calendario asociado a esta convocatoria.
     */
    public function calendar(): HasOne
    {
        return $this->hasOne(Calendar::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope para búsqueda global en el modelo.
     */
    public function scopeBuscarGlobal(\Illuminate\Database\Eloquent\Builder $query, ?string $search): \Illuminate\Database\Eloquent\Builder
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%")
              ->orWhere('status', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope para ordenar resultados.
     */
    public function scopeOrdenado(\Illuminate\Database\Eloquent\Builder $query, string $sortField = 'id', string $sortDirection = 'desc'): \Illuminate\Database\Eloquent\Builder
    {
        return $query->orderBy($sortField, $sortDirection);
    }
}
