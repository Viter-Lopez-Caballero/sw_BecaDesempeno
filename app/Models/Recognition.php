<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recognition extends Model
{
    use HasFactory;

    protected $table = 'recognitions';

    protected $fillable = [
        'user_id',
        'announcement_id',
        'active',
        'sent_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'sent_at' => 'datetime',
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
}
