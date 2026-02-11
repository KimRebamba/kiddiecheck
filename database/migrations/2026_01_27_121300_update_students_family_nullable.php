<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Drop existing foreign key if present
            try { $table->dropForeign(['family_id']); } catch (\Throwable $e) {}
        });
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('family_id')->nullable()->change();
        });
        Schema::table('students', function (Blueprint $table) {
            $table->foreign('family_id')->references('id')->on('families')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            try { $table->dropForeign(['family_id']); } catch (\Throwable $e) {}
        });
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('family_id')->nullable(false)->change();
        });
        Schema::table('students', function (Blueprint $table) {
            $table->foreign('family_id')->references('id')->on('families')->cascadeOnDelete();
        });
    }
};
