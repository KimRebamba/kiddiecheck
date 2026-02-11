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
        Schema::table('tests', function (Blueprint $table) {
            $table->foreignId('assessment_period_id')->nullable()->after('student_id')->constrained('assessment_periods')->nullOnDelete();
            // Expand status to support idea.txt workflow; keep existing values compatible
            $table->enum('status', ['draft', 'paused', 'finalized', 'archived', 'terminated', 'cancelled', 'pending', 'in_progress', 'completed'])
                ->default('draft')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('assessment_period_id');
            $table->enum('status', ['pending', 'in_progress', 'completed','cancelled','terminated'])->default('pending')->change();
        });
    }
};
