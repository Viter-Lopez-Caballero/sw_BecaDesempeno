<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reconocimiento extends Model
{
    protected $fillable = [
        'user_id',
        'convocatoria_id',
        'activo',
        'enviado_at',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'enviado_at' => 'datetime',
    ];

    /**
     * Relación con el evaluador
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con la convocatoria
     */
    public function convocatoria(): BelongsTo
    {
        return $this->belongsTo(Convocatoria::class);
    }
}
