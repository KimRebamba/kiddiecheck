<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Family;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Domain;
use App\Models\Question;
use App\Models\Test;
use App\Models\Section;

class InitialDataSeeder extends Seeder
{
 
    public function run(): void
    {

        $familyUser = User::where('email', 'family1@example.com')->first();
        $teacherUser = User::where('email', 'teacher1@example.com')->first();

  
        $familyId = DB::table('families')->insertGetId([
            'user_id' => $familyUser->id,
            'name' => 'Family One',
            'home_address' => '123 Main St',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

  
        DB::table('teachers')->insert([
            'id' => $teacherUser->id,
            'hire_date' => Carbon::now()->subYears(2)->toDateString(),
            'status' => 'active',
        ]);

        // Create a default section
        $sectionId = DB::table('sections')->insertGetId([
            'name' => 'Nursery 1',
            'description' => 'Default section',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $childAId = DB::table('students')->insertGetId([
            'family_id' => $familyId,
            'name' => 'Alice',
            'dob' => '2021-05-10',
            'emergency_contact' => '555-0101',
            'gender' => 'female',
            'enrollment_date' => '2024-09-01',
            'status' => 'active',
            'profile_path' => '/storage/public/alice.jpg',
            'section_id' => $sectionId,
            'notes' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $childBId = DB::table('students')->insertGetId([
            'family_id' => $familyId,
            'name' => 'Ben',
            'dob' => '2020-11-22',
            'emergency_contact' => '555-0102',
            'gender' => 'male',
            'enrollment_date' => '2024-09-01',
            'status' => 'active',
            'profile_path' => '/storage/public/ben.jpg',
            'section_id' => $sectionId,
            'notes' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

    
        DB::table('student_tags')->insert([
            'student_id' => $childAId,
            'tag_type' => 'allergy',
            'notes' => 'Peanut allergy',
        ]);


        DB::table('teacher_student')->insert([
            'teacher_id' => $teacherUser->id,
            'student_id' => $childAId,
            'role' => 'homeroom',
            'assigned_at' => now(),
            'status' => 'active',
        ]);
        DB::table('teacher_student')->insert([
            'teacher_id' => $teacherUser->id,
            'student_id' => $childBId,
            'role' => 'homeroom',
            'assigned_at' => now(),
            'status' => 'active',
        ]);


        $cognitiveId = DB::table('domains')->insertGetId([
            'name' => 'Cognitive',
            'description' => 'Cognitive development domain',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $motorId = DB::table('domains')->insertGetId([
            'name' => 'Motor',
            'description' => 'Motor skills domain',
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        $q1 = DB::table('questions')->insertGetId([
            'domain_id' => $cognitiveId,
            'question_text' => 'Can sort objects by color?',
            'type' => 'static',
            'instructions' => 'Place colored blocks; ask student to group by color.',
            'materials' => 'blocks/crayons',
            'procedure' => 'Demonstrate one grouping; then ask student to match like colors.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $q2 = DB::table('questions')->insertGetId([
            'domain_id' => $cognitiveId,
            'question_text' => 'Recognizes numbers 1-10?',
            'type' => 'static',
            'instructions' => 'Show numbers 1-10; ask student to name them.',
            'materials' => 'flashcards 1-10',
            'procedure' => 'Present cards randomly; prompt if needed.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $q3 = DB::table('questions')->insertGetId([
            'domain_id' => $motorId,
            'question_text' => 'Can hop on one foot?',
            'type' => 'interactive',
            'instructions' => 'Observe student hopping 3 times',
            'materials' => 'open floor space',
            'procedure' => 'Ask student to hop on one foot three times without support.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

     
        $testDate = Carbon::now()->startOfMonth()->toDateString();
        $testId = DB::table('tests')->insertGetId([
            'student_id' => $childAId,
            'observer_id' => $teacherUser->id,
            'test_date' => $testDate,
            'status' => 'in_progress',
            'started_at' => now(),
            'submitted_by' => null,
            'submitted_at' => null,
        ]);

      
        DB::table('test_responses')->insert([
            'test_id' => $testId,
            'question_id' => $q1,
            'score' => 4.0,
            'comment' => 'Sorted 8/10 correctly',
            'updated_at' => now(),
        ]);
        DB::table('test_responses')->insert([
            'test_id' => $testId,
            'question_id' => $q2,
            'score' => 3.5,
            'comment' => 'Recognized most numbers',
            'updated_at' => now(),
        ]);
        DB::table('test_responses')->insert([
            'test_id' => $testId,
            'question_id' => $q3,
            'score' => 2.0,
            'comment' => 'Needed assistance',
            'updated_at' => now(),
        ]);

    
        DB::table('domain_scores')->insert([
            'test_id' => $testId,
            'domain_id' => $cognitiveId,
            'raw_score' => 7.5,
            'scaled_score' => 75.0,
            'scaled_score_based' => 10.0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('domain_scores')->insert([
            'test_id' => $testId,
            'domain_id' => $motorId,
            'raw_score' => 2.0,
            'scaled_score' => 20.0,
            'scaled_score_based' => 10.0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
