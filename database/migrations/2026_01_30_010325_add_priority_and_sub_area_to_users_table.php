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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('institucion_id')->nullable()->constrained('instituciones')->onDelete('set null');
            $table->foreignId('priority_area_id')->nullable()->constrained('priority_areas')->onDelete('set null');
            $table->foreignId('sub_area_id')->nullable()->constrained('sub_areas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['institucion_id']);
            $table->dropForeign(['priority_area_id']);
            $table->dropForeign(['sub_area_id']);
            $table->dropColumn(['institucion_id', 'priority_area_id', 'sub_area_id']);
        });
    }
};
