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
Schema::create('users', function (Blueprint $table) {
    $table->id('user_id');
    $table->string('username');
    $table->string('email')->unique();
    $table->string('password');
    $table->enum('role', ['admin', 'teacher', 'family']);
    $table->string('profile_path')->nullable();
    $table->timestamps();
});

Schema::create('teachers', function (Blueprint $table) {
    $table->foreignId('user_id')
          ->primary()                                             
          ->references('user_id')->on('users')->onDelete('cascade');
    $table->string('first_name');
    $table->string('last_name');
    $table->string('home_address');
    $table->string('phone_number');
    $table->date('hire_date');
    $table->string('feature_path')->nullable();
    $table->timestamps();
});

Schema::create('families', function (Blueprint $table) {
    $table->foreignId('user_id')
          ->primary()                                            
          ->references('user_id')->on('users')->onDelete('cascade');
    $table->string('family_name');
    $table->string('home_address');
    $table->string('emergency_contact');
    $table->string('emergency_phone');
    $table->string('feature_path')->nullable();
    $table->timestamps();
});

Schema::create('students', function (Blueprint $table) {
    $table->id('student_id');
    $table->string('first_name');
    $table->string('last_name');
    $table->date('date_of_birth');
    $table->foreignId('family_id')
          ->references('user_id')->on('families')->onDelete('cascade'); // one family per student
    $table->string('feature_path')->nullable();
    $table->timestamps();
});

Schema::create('student_teacher', function (Blueprint $table) {
    $table->foreignId('student_id')
          ->references('student_id')->on('students')->onDelete('cascade');
    $table->foreignId('teacher_id')
          ->references('user_id')->on('teachers')->onDelete('cascade');
    $table->primary(['student_id', 'teacher_id']);
});

Schema::create('assessment_periods', function (Blueprint $table) {
    $table->id('period_id');
    $table->string('description');                                 
    $table->foreignId('student_id')
          ->references('student_id')->on('students')->onDelete('cascade');
    $table->date('start_date');
    $table->date('end_date');
    $table->enum('status', ['scheduled', 'completed', 'overdue'])->default('scheduled');
    $table->timestamps();
});

Schema::create('tests', function (Blueprint $table) {
    $table->id('test_id');
    $table->foreignId('period_id')
          ->references('period_id')->on('assessment_periods')->onDelete('cascade');
    $table->foreignId('student_id')
          ->references('student_id')->on('students')->onDelete('cascade');
    $table->foreignId('examiner_id')
          ->references('user_id')->on('users')->onDelete('cascade'); // teacher or family user
    $table->date('test_date');
    $table->text('notes')->nullable();
    $table->enum('status', ['canceled', 'in_progress', 'completed', 'terminated', 'finalized'])->default('in_progress');
    $table->timestamps();
});

Schema::create('documentation_pictures', function (Blueprint $table) {
    $table->id('picture_id');
    $table->string('file_path');
    $table->timestamps();
});

Schema::create('test_picture', function (Blueprint $table) {
    $table->foreignId('test_id')
          ->references('test_id')->on('tests')->onDelete('cascade');
    $table->foreignId('picture_id')
          ->references('picture_id')->on('documentation_pictures')->onDelete('cascade');
    $table->primary(['test_id', 'picture_id']);
});

Schema::create('domains', function (Blueprint $table) {
      $table->id('domain_id');
      $table->string('name');                                        // "Cognitive", "Language", "Motor", etc.
      $table->timestamps();
});

// Scale versions so that different checklists can define their own questions
Schema::create('scale_versions', function (Blueprint $table) {
      $table->id('scale_version_id');
      $table->string('name');                                        // e.g. "ECCD 2004"
      $table->string('description')->nullable();
      $table->timestamps();
});

Schema::create('questions', function (Blueprint $table) {
      $table->id('question_id');
      $table->foreignId('domain_id')
              ->references('domain_id')->on('domains')->onDelete('cascade');
      $table->foreignId('scale_version_id')
              ->nullable()
              ->references('scale_version_id')->on('scale_versions');
      $table->string('text');
      $table->enum('question_type', ['static', 'interactive']);
      $table->unsignedTinyInteger('order'); 
      $table->string('display_text')->nullable(); // plain language version for family examiners
                                                                        // null = use text (for teacher examiners)
      $table->timestamps();
});

Schema::create('test_responses', function (Blueprint $table) {
    $table->foreignId('test_id')
          ->references('test_id')->on('tests')->onDelete('cascade');
    $table->foreignId('question_id')
          ->references('question_id')->on('questions')->onDelete('cascade');
    $table->boolean('is_assumed')->default(false);  
// true = filled by basal/ceiling logic
// false = actually answered
    $table->enum('response', ['yes', 'no']);
    $table->primary(['test_id', 'question_id']);
});

// ─── Scale Lookup Tables ──────────────────────────────────────────────────────

// Step 3 lookup: raw → scaled per domain + age range
Schema::create('domain_scaled_scores', function (Blueprint $table) {
    $table->id();
    $table->foreignId('scale_version_id')
          ->references('scale_version_id')->on('scale_versions');
    $table->foreignId('domain_id')
          ->references('domain_id')->on('domains');
    $table->unsignedSmallInteger('age_min_months');
    $table->unsignedSmallInteger('age_max_months');
    $table->unsignedTinyInteger('raw_min');
    $table->unsignedTinyInteger('raw_max');
      $table->unsignedTinyInteger('scaled_score');
      $table->unique(
            ['scale_version_id', 'domain_id', 'age_min_months', 'raw_min'],
            'domain_scaled_scores_lookup_unique'
      );
    $table->timestamps();
});

// Step 5 lookup: sum_of_scaled → standard_score (general, no age)
Schema::create('standard_score_scales', function (Blueprint $table) {
    $table->id();
    $table->foreignId('scale_version_id')
          ->references('scale_version_id')->on('scale_versions');
    $table->unsignedSmallInteger('sum_scaled_min');
    $table->unsignedSmallInteger('sum_scaled_max');
    $table->unsignedSmallInteger('standard_score');
    $table->unique(['scale_version_id', 'sum_scaled_min']);        
    $table->timestamps();
});

// ─── Computed Result Tables ───────────────────────────────────────────────────

// Steps 2–3 result: raw + scaled score per domain per test
Schema::create('test_domain_scaled_scores', function (Blueprint $table) {
    $table->foreignId('test_id')
          ->references('test_id')->on('tests')->onDelete('cascade');
    $table->foreignId('domain_id')
          ->references('domain_id')->on('domains')->onDelete('cascade');
    $table->foreignId('scale_version_id')
          ->references('scale_version_id')->on('scale_versions');
    $table->unsignedTinyInteger('raw_score');
    $table->unsignedTinyInteger('scaled_score');
    $table->primary(['test_id', 'domain_id']);
    $table->timestamps();
});

// Steps 4–6 result: sum of scaled + standard score + interpretation per test
Schema::create('test_standard_scores', function (Blueprint $table) {
    $table->id();
    $table->foreignId('test_id')
          ->references('test_id')->on('tests')->onDelete('cascade')->unique();
    $table->foreignId('scale_version_id')
          ->references('scale_version_id')->on('scale_versions');
    $table->unsignedSmallInteger('sum_scaled_scores');
    $table->unsignedSmallInteger('standard_score');
    $table->string('interpretation');                              //e.g. "Average Development"
    $table->timestamps();
});

// Steps 7–11 result: final period-level summary
Schema::create('period_summary_scores', function (Blueprint $table) {
    $table->id();
    $table->foreignId('period_id')
          ->references('period_id')->on('assessment_periods')->onDelete('cascade')->unique();
    $table->decimal('teachers_standard_score_avg', 6, 2)->nullable();
    $table->unsignedSmallInteger('family_standard_score')->nullable();
    $table->decimal('final_standard_score', 6, 2)->nullable();
    $table->string('final_interpretation')->nullable();
    $table->enum('teacher_discrepancy', ['none', 'minor', 'major'])->nullable();
    $table->enum('teacher_family_discrepancy', ['none', 'minor', 'major'])->nullable();
    $table->timestamps();
});

        //FLOW:
        // 1. Answer tests -> show all questions each domain
        // 2. After all domains are done -> get raw score of each domain (from test_responses - yes = 1, no = 0, n/a = ignore)
        // 3. Based on age of student, get scale to convert raw score to scaled score for each domain
        // 4. Sum all scaled scores from all domains to get sum_of_scaled_scores
        // 5. Convert sum_of_scaled_scores to standard_score. (No age, general scale)
        // 6. Using standard_score, based on the age of student, convert standard_score to interpretation 
        //      -> "Re-Test After 6 months", "Average Development", "Advanced Development") 
        //      -> Age-range 3.1 - 4.0 years || 4.1 - 5.0 years || 5.1 - 5.11 years
        // 7. Compare all standard_scores of all teachers and provide note_of_discrepency_teacher
        //      -> "No Discrepency", "Minor Discrepency", "Major Discrepency"
        // 8. Combine all standard_scores from all tests of the student by teachers and average (teachers_standard_scores_avg).
        // 9. Compare teachers_standard_scores_avg to family_standard_score and provide note_of_discrepency_teacher-family
        //      -> "No Discrepency", "Minor Discrepency", "Major Discrepency"
        // 10. Weighted Average formula: (70% teachers_standard_scores_avg || 30% family_standard_score)
        //      -> final_standard_score = (teachers_standard_scores_avg * 0.7) + (family_standard_score * 0.3)
        // 11. Using final_standard_score, based on age of student, convert final_standard_score to final_interpretation
        
        // three tables -> teacher_test -> family_test -> combined
        

        // calculate domain scores, don't put to own table        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
