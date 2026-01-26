<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            if (!Schema::hasColumn('tests', 'question_order')) {
                $table->json('question_order')->nullable()->after('submitted_at');
            }
            $table->index(['student_id', 'status'], 'tests_student_status_index');
        });
    }

    public function down(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            if (Schema::hasColumn('tests', 'question_order')) {
                $table->dropColumn('question_order');
            }
            $table->dropIndex('tests_student_status_index');
        });
    }
};
