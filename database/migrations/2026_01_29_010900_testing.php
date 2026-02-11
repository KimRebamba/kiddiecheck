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
            $table->password('password');
            $table->enum('role',['admin','teacher','family']);  
            $table->string('profile_path')->nullable();
            $table->timestamps();
        });

        Schema::create('teachers', function (Blueprint $table) {
            $table->foreignId('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('home_address');
            $table->string('phone_number');
            $table->date('hire_date');
            $table->string('feature_path')->nullable(); //picture
            $table->timestamps();
        });

        Schema::create('families', function (Blueprint $table){
            $table->foreignId('user_id')->references('user_id')->on('users')->onDelete('cascade'); 
            $table->string('family_name');
            $table->string('home_address');                        
            $table->string('emergency_contact'); // name
            $table->string('emergency_phone'); // phone number
            $table->string('feature_path')->nullable(); //picture
            $table->timestamps();
        });
        
        Schema::create('students', function (Blueprint $table) {
            $table->id('student_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth'); //age
            $table->foreignId('family_id')->references('user_id')->on('families')->onDelete('cascade');
            $table->string('feature_path')->nullable(); //picture
            $table->timestamps();
        });

        
        Schema::create('family_student', function (Blueprint $table) {
            $table->foreignId('family_id')->references('user_id')->on('families')->onDelete('cascade');
            $table->foreignId('student_id')->references('student_id')->on('students')->onDelete('cascade');
            $table->primary(['family_id', 'student_id']);
        });

        Schema::create('student_teacher', function (Blueprint $table) {
            $table->foreignId('student_id')->references('student_id')->on('students')->onDelete('cascade');
            $table->foreignId('teacher_id')->references('user_id')->on('teachers')->onDelete('cascade');
            $table->primary(['student_id', 'teacher_id']);
        });

        Schema::create('assessment_periods', function (Blueprint $table) {
            $table->id('period_id');
            $table->string('decription'); // "1st || 6 months Test"  -> "2nd || 12 months Test" -> "3rd || 18 months Test"  
            $table->foreignId('student_id')->references('student_id')->on('students')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['scheduled', 'completed', 'overdue'])->default('scheduled');
            $table->timestamps();
        });

        Schema::create('tests', function (Blueprint $table) {
            $table->id('test_id');
            $table->foreignId('period_id')->references('period_id')->on('assessment_periods')->onDelete('cascade');
            $table->foreignId('student_id')->references('student_id')->on('students')->onDelete('cascade');
            $table->foreignId('examiner_id')->references('user_id')->on('users')->onDelete('cascade'); //Questionable
            $table->date('test_date');
            $table->text('notes')->nullable();
            $table->enum('status', ['canceled', 'in_progress', 'completed', 'terminated'])->default('in_progress');
            $table->timestamps();
        });

        Schema::create('documentation_pictures', function (Blueprint $table) {
            $table->id('picture_id');
            $table->string('file_path');                
            $table->timestamps();
        });

         Schema::create('test_picture', function (Blueprint $table){
            $table->foreignId('test_id')->references('test_id')->on('tests')->onDelete('cascade');
            $table->foreignId('picture_id')->references('picture_id')->on('documentation_pictures')->onDelete('cascade');
            $table->primary(['test_id', 'picture_id']);
        });
        
        Schema::create('domains', function (Blueprint $table) {
            $table->id('domain_id');
            $table->string('name'); // "Cognitive", "Language", "Motor", etc..
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id('question_id');
            $table->foreignId('domain_id')->references('domain_id')->on('domains')->onDelete('cascade');
            $table->string('text');
            $table->enum('question_type', ['static', 'assisted']);
            $table->timestamps();
        });        

         Schema::create('test_responses', function (Blueprint $table) {
            $table->foreignId('test_id')->references('test_id')->on('tests')->onDelete('cascade');
            $table->foreignId('question_id')->references('question_id')->on('questions')->onDelete('cascade');
            $table->enum('response', ['yes', 'no', 'n/a']);
            $table->primary(['test_id', 'question_id']);        
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
