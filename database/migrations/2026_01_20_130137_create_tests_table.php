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
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('observer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('examiner_id')->nullable();
            $table->string('examiner_name')->nullable();
            $table->date('test_date');
            $table->decimal('age_months', 6, 2)->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed','cancelled','terminated'])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->string('submitted_by')->nullable();
            $table->timestamp('submitted_at')->nullable();

            $table->foreign('examiner_id')->references('id')->on('users')->nullOnDelete();
            $table->unique(['student_id', 'test_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tests');
    }
};
