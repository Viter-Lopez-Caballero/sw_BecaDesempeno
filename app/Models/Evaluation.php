<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluation extends Model
{
    use HasFactory;

    protected $table = 'evaluations';

    protected $fillable = [
        'application_id',
        'evaluator_id',
        'status',
        'score',
        'answers',
        'comment',
        'deadline_at',
    ];

    protected $casts = [
        'answers' => 'array',
        'score' => 'decimal:2',
        'deadline_at' => 'datetime',
    ];

    // Relaciones

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
}
