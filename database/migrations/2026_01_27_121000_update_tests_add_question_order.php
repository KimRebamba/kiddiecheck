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
        if (!Schema::hasColumn('tests', 'question_order')) {
            Schema::table('tests', function (Blueprint $table) {
                $table->json('question_order')->nullable()->after('submitted_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('tests', 'question_order')) {
            Schema::table('tests', function (Blueprint $table) {
                $table->dropColumn('question_order');
            });
        }
    }
};
