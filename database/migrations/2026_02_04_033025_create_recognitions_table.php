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
        Schema::create('recognitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Evaluator
            $table->foreignId('announcement_id')->constrained('announcements')->onDelete('cascade');
            $table->boolean('active')->default(false); // Switch estado
            $table->timestamp('sent_at')->nullable(); // When it was sent/activated
            $table->timestamps();
            
            // An evaluator can only have one recognition per announcement
            $table->unique(['user_id', 'announcement_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recognitions');
    }
};
