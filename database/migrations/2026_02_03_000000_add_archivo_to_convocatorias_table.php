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
        Schema::table('convocatorias', function (Blueprint $table) {
            $table->string('archivo_path')->nullable()->after('descripcion');
            $table->string('archivo_nombre')->nullable()->after('archivo_path');
            $table->string('archivo_tipo')->nullable()->after('archivo_nombre');
            $table->unsignedBigInteger('archivo_size')->nullable()->after('archivo_tipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('convocatorias', function (Blueprint $table) {
            $table->dropColumn(['archivo_path', 'archivo_nombre', 'archivo_tipo', 'archivo_size']);
        });
    }
};
