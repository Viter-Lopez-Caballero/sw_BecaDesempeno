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
        Schema::create('convocatoria_documento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('convocatoria_id')->constrained('convocatorias')->onDelete('cascade');
            $table->foreignId('documento_catalogo_id')->constrained('documentos_catalogo')->onDelete('cascade');
            $table->boolean('es_obligatorio')->default(true); // Puede sobrescribir el valor del catálogo
            $table->timestamps();
            
            $table->unique(['convocatoria_id', 'documento_catalogo_id'], 'convocatoria_documento_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('convocatoria_documento');
    }
};
