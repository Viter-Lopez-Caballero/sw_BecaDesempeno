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
        Schema::create('calendarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('convocatoria_id')->constrained('convocatorias')->onDelete('cascade');
            $table->date('publicacion_inicio');
            // 'publicacion_fin' removed
            $table->date('registro_inicio');
            $table->date('registro_fin');
            $table->date('evaluacion_inicio');
            $table->date('evaluacion_fin');
            $table->date('resultados_inicio');
            $table->date('resultados_fin');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendarios');
    }
};
