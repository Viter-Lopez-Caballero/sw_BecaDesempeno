<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reconocimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Evaluador
            $table->foreignId('convocatoria_id')->constrained()->onDelete('cascade');
            $table->boolean('activo')->default(false); // Switch estado
            $table->timestamp('enviado_at')->nullable(); // Cuándo se envió/activó
            $table->timestamps();
            
            // Un evaluador solo puede tener un reconocimiento por convocatoria
            $table->unique(['user_id', 'convocatoria_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reconocimientos');
    }
};
