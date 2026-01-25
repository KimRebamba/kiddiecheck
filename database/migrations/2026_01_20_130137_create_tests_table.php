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
            $table->foreignId('child_id')->constrained('children')->cascadeOnDelete();
            $table->foreignId('observer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('observer_role', ['teacher', 'family']);
            $table->date('test_date');
            $table->enum('status', ['pending', 'in_progress', 'completed','cancelled','terminated'])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->string('submitted_by')->nullable();
            $table->timestamp('submitted_at')->nullable();

            $table->unique(['child_id', 'test_date', 'observer_role']);
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
