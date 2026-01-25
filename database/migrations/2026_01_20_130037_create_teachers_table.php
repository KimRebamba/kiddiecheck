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
       Schema::create('teachers', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->date('hire_date')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->primary('id');
            $table->foreign('id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
