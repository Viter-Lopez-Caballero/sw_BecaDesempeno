<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'applications';

    protected $fillable = [
        'user_id',
        'announcement_id',
        'status',
        'admin_comment',
    ];

    // Relaciones

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function announcement(): BelongsTo
    {
        return $this->belongsTo(Announcement::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope para búsqueda global en solicitudes.
     */
    public function scopeBuscarGlobal(\Illuminate\Database\Eloquent\Builder $query, ?string $search): \Illuminate\Database\Eloquent\Builder
    {
        if (!$search) {
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
