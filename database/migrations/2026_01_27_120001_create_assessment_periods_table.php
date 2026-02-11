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
        Schema::create('assessment_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->unsignedTinyInteger('index'); // 1, 2, 3 within enrollment cycle
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->dateTime('teacher_grace_end')->nullable();
            $table->enum('status', ['scheduled', 'active', 'closed'])->default('scheduled');
            $table->unique(['student_id', 'index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_periods');
    }
};
