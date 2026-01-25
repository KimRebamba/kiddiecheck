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
        Schema::create('child_tags', function (Blueprint $table) {
            $table->foreignId('child_id')->constrained('children')->cascadeOnDelete();
            $table->enum('tag_type', ['allergy', 'medical', 'learning_needs', 'others']);
            $table->text('notes')->nullable();
            $table->unique(['child_id', 'tag_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('child_tags');
    }
};
