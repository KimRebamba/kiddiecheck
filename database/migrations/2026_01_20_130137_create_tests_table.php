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
            $table->foreignId('children_id')->constrained('children')->cascadeOnDelete();
            $table->foreignId('observer_id')->constrained('users')->cascadeOnDelete();
            $table->date('test_date'); // 'YYYY-MM' format 
            $table->enum('status', ['in_progress', 'completed','cancelled','terminated'])->default('in_progress');
            $table->timestamp('started_at')->useCurrent();
            $table->string('submitted_by');
            $table->timestamp('submitted_at')->nullable();

            $table->unique(['observer_id', 'test_date', 'children_id']);
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
