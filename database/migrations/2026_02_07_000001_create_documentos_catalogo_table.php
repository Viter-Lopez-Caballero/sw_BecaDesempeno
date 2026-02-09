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
        Schema::create('documentos_catalogo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('archivo_path')->nullable(); // Ruta del archivo de plantilla/ejemplo
            $table->string('archivo_nombre')->nullable(); // Nombre original del archivo
            $table->string('archivo_tipo')->nullable(); // Tipo MIME del archivo
            $table->integer('archivo_size')->nullable(); // Tamaño del archivo en bytes
            $table->boolean('activo')->default(false);
            $table->boolean('es_fundamental')->default(false); // Documentos del seeder que no se pueden eliminar
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos_catalogo');
    }
};
