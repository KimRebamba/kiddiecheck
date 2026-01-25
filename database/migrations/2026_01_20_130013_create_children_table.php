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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->constrained('families')->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->string('name');
            $table->date('dob');
            $table->string('emergency_contact', 50)->nullable();
            $table->enum('gender', ['male', 'female', 'other']);
            $table->enum('handedness', ['right','left','both','unknown'])->default('unknown');
            $table->boolean('is_studying')->default(false);
            $table->string('school_name')->nullable();
            $table->date('enrollment_date');
            $table->enum('status', ['active', 'transferred', 'graduated'])->default('active');
            $table->string('profile_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('children');
    }
};
