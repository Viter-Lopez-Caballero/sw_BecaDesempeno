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
        Schema::create('convocatoria_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('convocatoria_id')->constrained('convocatorias')->onDelete('cascade');
            $table->enum('estado', ['inscrito', 'aprobado', 'rechazado', 'retirado'])->default('inscrito');
            $table->timestamp('fecha_inscripcion')->useCurrent();
            $table->timestamps();
            
            $table->unique(['user_id', 'convocatoria_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('convocatoria_user');
    }
};
