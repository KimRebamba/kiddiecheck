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
        
        Schema::create('teacher_child', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teacher')->cascadeOnDelete();
            $table->foreignId('child_id')->constrained('child')->cascadeOnDelete();
            $table->enum('role', ['homeroom', 'specialist', 'others'])->nullable();
            $table->timestamp('assigned_at')->useCurrent();
            $table->enum('status', ['active', 'inactive'])->default('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_child');
    }
};
