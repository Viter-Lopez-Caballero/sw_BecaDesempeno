<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modulo extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'modules';

    protected $fillable = ['nombre', 'descripcion', 'key'];

    public function audit_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'audit_user_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($rec){
            // Ensure auth user exists to avoid errors on seeding
            if(auth()->check()){
                $rec->audit_user_id = auth()->user()->id;
            }
        });
    }   
}
