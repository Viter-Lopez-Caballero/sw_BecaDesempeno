<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Solicitud extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'solicitudes';

    protected $fillable = [
        'user_id',
        'convocatoria_id',
        'status',
        'admin_comment',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function convocatoria(): BelongsTo
    {
        return $this->belongsTo(Convocatoria::class);
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class);
    }

    public function evaluaciones(): HasMany
    {
        return $this->hasMany(Evaluacion::class);
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
             $q->whereHas('user', function ($subQ) use ($search) {
                $subQ->where('name', 'like', "%{$search}%")
                     ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('id', 'like', "%{$search}%");
        });
    }

    public function scopePorEstatus($query, $status)
    {
        if (empty($status)) {
            return $query;
        }
        return $query->where('status', $status);
    }

    public function scopeOrdenado($query, $sortField = 'id', $sortDirection = 'desc')
    {
        // Allow sorting by related user name if needed, assuming basic sort for now
        return $query->orderBy($sortField, $sortDirection);
    }
}
