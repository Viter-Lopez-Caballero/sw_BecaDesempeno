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
        Schema::table('evaluaciones', function (Blueprint $table) {
            $table->decimal('score', 8, 2)->nullable()->after('status'); // Puntuación total
            $table->json('respuestas')->nullable()->after('score'); // Guardar respuestas seleccionadas (id opción, puntos)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluaciones', function (Blueprint $table) {
            //
        });
    }
};
