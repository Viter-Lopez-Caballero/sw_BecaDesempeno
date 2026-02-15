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
            $table->foreignId('institution_id')->nullable()->after('password')->constrained('institutions')->onDelete('set null');
            $table->foreignId('priority_area_id')->nullable()->after('institution_id')->constrained('priority_areas')->onDelete('set null');
            $table->foreignId('sub_area_id')->nullable()->after('priority_area_id')->constrained('sub_areas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['institution_id']);
            $table->dropColumn('institution_id');
            $table->dropForeign(['priority_area_id']);
            $table->dropColumn('priority_area_id');
            $table->dropForeign(['sub_area_id']);
            $table->dropColumn('sub_area_id');
        });
    }
};
