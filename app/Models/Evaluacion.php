<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluacion extends Model
{
    use HasFactory;

    protected $table = 'evaluaciones';

    protected $fillable = [
        'solicitud_id',
        'user_id',
        'status', // pending, approved, rejected
        'score',
        'respuestas',
        'comentario',
    ];

    protected $casts = [
        'respuestas' => 'array',
        'score' => 'decimal:2',
    ];

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class);
    }

    public function evaluador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
