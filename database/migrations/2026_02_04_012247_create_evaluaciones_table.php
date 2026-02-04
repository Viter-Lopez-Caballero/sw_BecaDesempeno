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
        Schema::create('evaluaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_id')->constrained('solicitudes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Evaluador
            
            // Status: pending, approved, rejected (by the evaluator)
            // Note: The final status of the Solicitud is controlled by Admin. THIS status is just the evaluator's opinion.
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('comentario')->nullable();
            
            $table->timestamps();
            
            // Un evaluador solo puede evaluar una solicitud una vez
            $table->unique(['solicitud_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluaciones');
    }
};
