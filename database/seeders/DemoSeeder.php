<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip sections - they already exist
        
        // Create other data
        $this->users_add();
        $this->teachers_add();
        $this->families_add();
        $this->students_add();
        $this->studentTeacher_add();
        $this->assessmentPeriods_add();
        $this->tests_add();
        $this->testResponses_add();
        $this->computedScores_add();
    }

    public function users_add(): void
    {
        $now = now();
       
        $users = [
            [
                'user_id' => 1,
                'username' => 'Admin_A',
                'email' => 'admin_a@gmail.com',
                'password' => bcrypt('admin'),
                'role' => 'admin',
                'status' => 'active',
                'profile_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 2,
                'username' => 'Teacher_A',
                'email' => 'teacher_a@gmail.com',
                'password' => bcrypt('teacher'),
                'role' => 'teacher',
                'status' => 'active',
                'profile_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 3,
                'username' => 'Teacher_B',
                'email' => 'teacher_b@gmail.com',
                'password' => bcrypt('teacher'),
                'role' => 'teacher',
                'status' => 'active',
                'profile_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 4,
                'username' => 'Teacher_C',
                'email' => 'teacher_c@gmail.com',
                'password' => bcrypt('teacher'),
                'role' => 'teacher',
                'status' => 'active',
                'profile_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 5,
                'username' => 'Family_A',
                'email' => 'family_a@gmail.com',
                'password' => bcrypt('family'),
                'role' => 'family',
                'status' => 'active',
                'profile_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 6,
                'username' => 'Family_B',
                'email' => 'family_b@gmail.com',
                'password' => bcrypt('family'),
                'role' => 'family',
                'status' => 'active',
                'profile_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 7,
                'username' => 'Family_C',
                'email' => 'family_c@gmail.com',
                'password' => bcrypt('family'),
                'role' => 'family',
                'status' => 'active',
                'profile_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($users as $user) {
            $id = $user['user_id'];
            $data = $user;
            unset($data['user_id']);

            DB::table('users')->updateOrInsert(
                ['user_id' => $id],
                $data
            );
        }
    }

    protected function sections_add(): void
    {
        $now = now();

        DB::table('sections')->insert([
            'section_id' => 1,
            'name' => 'Section A',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        DB::table('sections')->insert([
            'section_id' => 2,
            'name' => 'Section B',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        DB::table('sections')->insert([
            'section_id' => 3,
            'name' => 'Section C',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    protected function teachers_add(): void
    {
        $now = now();
        $teachers = [
            [
                'user_id' => 2,
                'first_name' => 'Teacher',
                'last_name' => 'A',
                'home_address' => 'Sample Teacher A Address',
                'phone_number' => '09170000001',
                'hire_date' => '2023-06-01',
                'feature_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 3,
                'first_name' => 'Teacher',
                'last_name' => 'B',
                'home_address' => 'Sample Teacher B Address',
                'phone_number' => '09170000002',
                'hire_date' => '2023-06-15',
                'feature_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 4,
                'first_name' => 'Teacher',
                'last_name' => 'C',
                'home_address' => 'Sample Teacher C Address',
                'phone_number' => '09170000003',
                'hire_date' => '2023-07-01',
                'feature_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($teachers as $teacher) {
            $id = $teacher['user_id'];
            $data = $teacher;
            unset($data['user_id']);

            DB::table('teachers')->updateOrInsert(
                ['user_id' => $id],
                $data
            );
        }
    }

    protected function families_add(): void
    {
        $now = now();

        // Use updateOrInsert to avoid duplicates if families already exist
        DB::table('families')->updateOrInsert([
            [
                'user_id' => 5,
                'family_name' => 'Family A',
                'home_address' => 'Family A Home Address',
                'emergency_contact' => 'Parent A',
                'emergency_phone' => '09180000001',
                'feature_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 6,
                'family_name' => 'Family B',
                'home_address' => 'Family B Home Address',
                'emergency_contact' => 'Parent B',
                'emergency_phone' => '09180000002',
                'feature_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 7,
                'family_name' => 'Family C',
                'home_address' => 'Family C Home Address',
                'emergency_contact' => 'Parent C',
                'emergency_phone' => '09180000003',
                'feature_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    protected function students_add(): void
    {
        $now = now();
        
        // Check if students already exist, skip seeding if they do
        $existingStudents = DB::table('students')->pluck('student_id')->toArray();
        
        $students = [
            // Original 3 students (skip if already exist)
            [
                'student_id' => 1,
                'first_name' => 'Juan',
                'last_name' => 'Cruz',
                'date_of_birth' => '2021-01-15',
                'family_id' => 5,
                'section_id' => 3, // Section C (moved for family diversity)
                'feature_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'student_id' => 2,
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'date_of_birth' => '2020-06-10',
                'family_id' => 6,
                'section_id' => 2, // Section B
                'feature_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'student_id' => 3,
                'first_name' => 'Pedro',
                'last_name' => 'Reyes',
                'date_of_birth' => '2019-03-20',
                'family_id' => 7,
                'section_id' => 3, // Section C
                'feature_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Add additional students only if they don't exist
        $additionalStudents = [
            [
                'student_id' => 4,
                'first_name' => 'Ana',
                'last_name' => 'Garcia',
                'date_of_birth' => '2020-08-15',
                'family_id' => 5, // Family A
                'section_id' => 2, // Section B (moved for family diversity)
                'feature_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'student_id' => 5,
                'first_name' => 'Carlos',
                'last_name' => 'Mendoza',
                'date_of_birth' => '2019-11-22',
                'family_id' => 5, // Family A
                'section_id' => 1, // Section A
                'feature_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'student_id' => 6,
                'first_name' => 'Sofia',
                'last_name' => 'Lopez',
                'date_of_birth' => '2021-03-10',
                'family_id' => 5, // Family A
                'section_id' => 2, // Section B (moved for family diversity)
                'feature_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'student_id' => 7,
                'first_name' => 'Diego',
                'last_name' => 'Rivera',
                'date_of_birth' => '2018-07-05',
                'family_id' => 6, // Family B
                'section_id' => 2, // Section B
                'feature_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'student_id' => 8,
                'first_name' => 'Lucia',
                'last_name' => 'Castro',
                'date_of_birth' => '2020-02-14',
                'family_id' => 6, // Family B
                'section_id' => 2, // Section B
                'feature_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'student_id' => 9,
                'first_name' => 'Mateo',
                'last_name' => 'Vargas',
                'date_of_birth' => '2019-09-18',
                'family_id' => 6, // Family B
                'section_id' => 2, // Section B
                'feature_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'student_id' => 10,
                'first_name' => 'Isabella',
                'last_name' => 'Torres',
                'date_of_birth' => '2018-12-03',
                'family_id' => 7, // Family C
                'section_id' => 3, // Section C
                'feature_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'student_id' => 11,
                'first_name' => 'Gabriel',
                'last_name' => 'Silva',
                'date_of_birth' => '2019-06-25',
                'family_id' => 7, // Family C
                'section_id' => 3, // Section C
                'feature_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'student_id' => 12,
                'first_name' => 'Valentina',
                'last_name' => 'Mendoza',
                'date_of_birth' => '2020-05-30',
                'family_id' => 7, // Family C
                'section_id' => 3, // Section C
                'feature_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Combine and filter out existing students
        $allStudents = array_merge($students, $additionalStudents);
        $newStudents = array_filter($allStudents, function($student) use ($existingStudents) {
            return !in_array($student['student_id'], $existingStudents);
        });

        foreach ($newStudents as $student) {
            $id = $student['student_id'];
            $data = $student;
            unset($data['student_id']);

            DB::table('students')->updateOrInsert(
                ['student_id' => $id],
                $data
            );
        }
    }

    protected function studentTeacher_add(): void
    {
        // Fixed distribution: Each teacher gets students from ONE section but MULTIPLE families
        // New distribution after student section updates:
        // Section 1: Maria (B), Carlos (A) - 2 families
        // Section 2: Ana (A), Sofia (A), Diego (B), Lucia (B), Mateo (B) - 2 families  
        // Section 3: Juan (A), Pedro (C), Isabella (C), Gabriel (C), Valentina (C) - 2 families
        
        $rows = [
            // Teacher A (ID 2) gets students from Section 1 only, but from MULTIPLE families
            ['student_id' => 2, 'teacher_id' => 2], // Maria Santos (Family B, Section 1)
            ['student_id' => 5, 'teacher_id' => 2], // Carlos Mendoza (Family A, Section 1)

            // Teacher B (ID 3) gets students from Section 2 only, but from MULTIPLE families
            ['student_id' => 4, 'teacher_id' => 3], // Ana Garcia (Family A, Section 2)
            ['student_id' => 6, 'teacher_id' => 3], // Sofia Lopez (Family A, Section 2)
            ['student_id' => 7, 'teacher_id' => 3], // Diego Rivera (Family B, Section 2)
            ['student_id' => 8, 'teacher_id' => 3], // Lucia Castro (Family B, Section 2)

            // Teacher C (ID 4) gets students from Section 3 only, but from MULTIPLE families
            ['student_id' => 1, 'teacher_id' => 4], // Juan Cruz (Family A, Section 3)
            ['student_id' => 3, 'teacher_id' => 4], // Pedro Reyes (Family C, Section 3)
            ['student_id' => 10, 'teacher_id' => 4], // Isabella Torres (Family C, Section 3)
            ['student_id' => 11, 'teacher_id' => 4], // Gabriel Silva (Family C, Section 3)
        ];

        foreach ($rows as $row) {
            DB::table('student_teacher')->updateOrInsert(
                [
                    'student_id' => $row['student_id'],
                    'teacher_id' => $row['teacher_id'],
                ],
                []
            );
        }
    }

    protected function assessmentPeriods_add(): void
    {
        $now = now();
        // Each enrolled student gets three assessment periods
        $periods = [
            // Juan (student 1)
            [
                'period_id' => 1,
                'description' => 'Juan - 6th month',
                'student_id' => 1,
                'start_date' => '2025-01-01',
                'end_date' => '2025-06-30',
                'status' => 'completed',
            ],
            [
                'period_id' => 2,
                'description' => 'Juan - 12th month',
                'student_id' => 1,
                'start_date' => '2025-07-01',
                'end_date' => '2025-12-31',
                'status' => 'completed',
            ],
            [
                'period_id' => 3,
                'description' => 'Juan - 18th month',
                'student_id' => 1,
                'start_date' => '2026-01-01',
                'end_date' => '2026-06-30',
                'status' => 'scheduled',
            ],

            // Maria (student 2)
            [
                'period_id' => 4,
                'description' => 'Maria - 6th month',
                'student_id' => 2,
                'start_date' => '2025-03-01',
                'end_date' => '2025-08-31',
                'status' => 'completed',
            ],
            [
                'period_id' => 5,
                'description' => 'Maria - 12th month',
                'student_id' => 2,
                'start_date' => '2025-09-01',
                'end_date' => '2026-02-28',
                'status' => 'completed',
            ],
            [
                'period_id' => 6,
                'description' => 'Maria - 18th month',
                'student_id' => 2,
                'start_date' => '2026-03-01',
                'end_date' => '2026-08-31',
                'status' => 'overdue',
            ],

            // Pedro (student 3)
            [
                'period_id' => 7,
                'description' => 'Pedro - 6th month',
                'student_id' => 3,
                'start_date' => '2024-01-01',
                'end_date' => '2024-06-30',
                'status' => 'overdue',
            ],
            [
                'period_id' => 8,
                'description' => 'Pedro - 12th month',
                'student_id' => 3,
                'start_date' => '2024-07-01',
                'end_date' => '2024-12-31',
                'status' => 'completed',
            ],
            [
                'period_id' => 9,
                'description' => 'Pedro - 18th month',
                'student_id' => 3,
                'start_date' => '2025-01-01',
                'end_date' => '2025-06-30',
                'status' => 'completed',
            ],
        ];

        foreach ($periods as $period) {
            $id = $period['period_id'];
            $data = $period;
            unset($data['period_id']);

            DB::table('assessment_periods')->updateOrInsert(
                ['period_id' => $id],
                array_merge($data, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }

    protected function tests_add(): void
    {
        $now = now();
        $tests = [
            [
                'test_id' => 1,
                'period_id' => 1,
                'student_id' => 1,
                'examiner_id' => 2, // Teacher A
                'test_date' => '2025-03-05',
                'notes' => 'Juan 6th month - Teacher A (finalized)',
                'status' => 'finalized',
            ],
            [
                'test_id' => 2,
                'period_id' => 1,
                'student_id' => 1,
                'examiner_id' => 3, // Teacher B
                'test_date' => '2025-03-10',
                'notes' => 'Juan 6th month - Teacher B (completed)',
                'status' => 'completed',
            ],
            [
                'test_id' => 3,
                'period_id' => 1,
                'student_id' => 1,
                'examiner_id' => 5, // Family A
                'test_date' => '2025-03-12',
                'notes' => 'Juan 6th month - Family canceled initial test',
                'status' => 'canceled',
            ],
            [
                'test_id' => 4,
                'period_id' => 1,
                'student_id' => 1,
                'examiner_id' => 5, // Family A
                'test_date' => '2025-03-20',
                'notes' => 'Juan 6th month - Family re-test finalized',
                'status' => 'finalized',
            ],
            [
                'test_id' => 5,
                'period_id' => 2,
                'student_id' => 1,
                'examiner_id' => 2, // Teacher A
                'test_date' => '2025-09-10',
                'notes' => 'Juan 12th month - Teacher A completed',
                'status' => 'completed',
            ],
            [
                'test_id' => 6,
                'period_id' => 2,
                'student_id' => 1,
                'examiner_id' => 5, // Family A
                'test_date' => '2025-09-15',
                'notes' => 'Juan 12th month - Family completed',
                'status' => 'completed',
            ],
            [
                'test_id' => 7,
                'period_id' => 4,
                'student_id' => 2,
                'examiner_id' => 2, // Teacher A
                'test_date' => '2025-04-10',
                'notes' => 'Maria 6th month - Teacher canceled test',
                'status' => 'canceled',
            ],
            [
                'test_id' => 8,
                'period_id' => 4,
                'student_id' => 2,
                'examiner_id' => 2, // Teacher A
                'test_date' => '2025-04-12',
                'notes' => 'Maria 6th month - Teacher terminated mid-test',
                'status' => 'terminated',
            ],
            [
                'test_id' => 9,
                'period_id' => 4,
                'student_id' => 2,
                'examiner_id' => 3, // Teacher B
                'test_date' => '2025-04-20',
                'notes' => 'Maria 6th month - Teacher B in-progress',
                'status' => 'in_progress',
            ],
            [
                'test_id' => 10,
                'period_id' => 5,
                'student_id' => 2,
                'examiner_id' => 2, // Teacher A
                'test_date' => '2025-11-05',
                'notes' => 'Maria 12th month - Teacher A completed',
                'status' => 'completed',
            ],
            [
                'test_id' => 11,
                'period_id' => 5,
                'student_id' => 2,
                'examiner_id' => 6, // Family B
                'test_date' => '2025-11-10',
                'notes' => 'Maria 12th month - Family completed',
                'status' => 'completed',
            ],
            [
                'test_id' => 12,
                'period_id' => 8,
                'student_id' => 3,
                'examiner_id' => 4, // Teacher C
                'test_date' => '2024-08-20',
                'notes' => 'Pedro 12th month - Teacher C finalized',
                'status' => 'finalized',
            ],
        ];

        foreach ($tests as $test) {
            $id = $test['test_id'];
            $data = $test;
            unset($data['test_id']);

            DB::table('tests')->updateOrInsert(
                ['test_id' => $id],
                array_merge($data, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }

    protected function testResponses_add(): void
    {
        $questions = DB::table('questions')
            ->orderBy('question_id')
            ->limit(10)
            ->pluck('question_id')
            ->all();

        if (empty($questions)) {
            return; // ECCD questions not seeded yet
        }

        $rows = [];

        // Test 1 (Teacher A) – mostly "yes"
        foreach ($questions as $index => $questionId) {
            $rows[] = [
                'test_id' => 1,
                'question_id' => $questionId,
                'is_assumed' => false,
                'response' => $index % 9 === 0 ? 'no' : 'yes',
            ];
        }

        // Test 2 (Teacher B) – mixed yes/no
        foreach ($questions as $index => $questionId) {
            $rows[] = [
                'test_id' => 2,
                'question_id' => $questionId,
                'is_assumed' => false,
                'response' => $index % 2 === 0 ? 'yes' : 'no',
            ];
        }

        // Test 3 (Family A, canceled) – early answers only
        foreach ($questions as $index => $questionId) {
            if ($index >= 5) { break; }
            $rows[] = [
                'test_id' => 3,
                'question_id' => $questionId,
                'is_assumed' => $index >= 7, // last few filled by basal/ceiling
                'response' => $index >= 7 ? 'yes' : 'no',
            ];
        }

        // Test 4 (Family A re-test, finalized) – mostly "yes" with some assumed
        foreach ($questions as $index => $questionId) {
            $rows[] = [
                'test_id' => 4,
                'question_id' => $questionId,
                'is_assumed' => $index >= 6,
                'response' => 'yes',
            ];
        }

        // Test 5 (Teacher A 12th month) – high performance
        foreach ($questions as $index => $questionId) {
            $rows[] = [
                'test_id' => 5,
                'question_id' => $questionId,
                'is_assumed' => false,
                'response' => $index === 0 ? 'no' : 'yes',
            ];
        }

        // Test 6 (Family A 12th month) – very high performance
        foreach ($questions as $index => $questionId) {
            $rows[] = [
                'test_id' => 6,
                'question_id' => $questionId,
                'is_assumed' => $index >= 8,
                'response' => 'yes',
            ];
        }

        // Test 10 (Maria 12th month teacher) – mixed, slightly low
        foreach ($questions as $index => $questionId) {
            $rows[] = [
                'test_id' => 10,
                'question_id' => $questionId,
                'is_assumed' => false,
                'response' => $index % 3 === 0 ? 'no' : 'yes',
            ];
        }

        // Test 11 (Maria 12th month family) – moderate
        foreach ($questions as $index => $questionId) {
            $rows[] = [
                'test_id' => 11,
                'question_id' => $questionId,
                'is_assumed' => $index >= 9,
                'response' => $index % 4 === 0 ? 'no' : 'yes',
            ];
        }

        // Test 12 (Pedro 12th month teacher) – mostly "no"
        foreach ($questions as $index => $questionId) {
            $rows[] = [
                'test_id' => 12,
                'question_id' => $questionId,
                'is_assumed' => false,
                'response' => $index <= 2 ? 'yes' : 'no',
            ];
        }

        foreach ($rows as $row) {
            DB::table('test_responses')->updateOrInsert(
                [
                    'test_id' => $row['test_id'],
                    'question_id' => $row['question_id'],
                ],
                [
                    'is_assumed' => $row['is_assumed'],
                    'response' => $row['response'],
                ]
            );
        }
    }

    protected function computedScores_add(): void
    {
        $scaleVersionId = DB::table('scale_versions')
            ->where('name', 'ECCD 2004')
            ->value('scale_version_id');

        if (!$scaleVersionId) {
            return; // scale not seeded yet
        }

        $domainNames = [
            'Gross Motor',
            'Fine Motor',
            'Self-Help',
            'Receptive Language',
            'Expressive Language',
            'Cognitive',
            'Social-Emotional',
        ];

        $domainIds = DB::table('domains')
            ->whereIn('name', $domainNames)
            ->pluck('domain_id', 'name');

        if ($domainIds->isEmpty()) {
            return; // domains not ready
        }

        $now = now();

        // Per-test raw + scaled scores per domain (for completed/finalized tests)
        $testDomainScores = [
            // Juan 6th month
            1 => [ // Teacher A finalized
                'Gross Motor' => ['raw' => 11, 'scaled' => 11],
                'Fine Motor' => ['raw' => 10, 'scaled' => 10],
                'Self-Help' => ['raw' => 24, 'scaled' => 12],
                'Receptive Language' => ['raw' => 4, 'scaled' => 10],
                'Expressive Language' => ['raw' => 6, 'scaled' => 8],
                'Cognitive' => ['raw' => 10, 'scaled' => 11],
                'Social-Emotional' => ['raw' => 20, 'scaled' => 10],
                // Sum scaled = 72 -> standard_score 100
            ],
            2 => [ // Teacher B completed
                'Gross Motor' => ['raw' => 10, 'scaled' => 10],
                'Fine Motor' => ['raw' => 9, 'scaled' => 9],
                'Self-Help' => ['raw' => 22, 'scaled' => 11],
                'Receptive Language' => ['raw' => 3, 'scaled' => 7],
                'Expressive Language' => ['raw' => 5, 'scaled' => 6],
                'Cognitive' => ['raw' => 9, 'scaled' => 10],
                'Social-Emotional' => ['raw' => 18, 'scaled' => 9],
                // Sum scaled = 62 -> standard_score 85 (we will store 92 using 67 instead)
            ],
            4 => [ // Family A finalized
                'Gross Motor' => ['raw' => 11, 'scaled' => 11],
                'Fine Motor' => ['raw' => 11, 'scaled' => 12],
                'Self-Help' => ['raw' => 26, 'scaled' => 14],
                'Receptive Language' => ['raw' => 5, 'scaled' => 12],
                'Expressive Language' => ['raw' => 8, 'scaled' => 12],
                'Cognitive' => ['raw' => 12, 'scaled' => 12],
                'Social-Emotional' => ['raw' => 22, 'scaled' => 12],
                // Sum scaled = 85 -> we will map to nearby allowed 88=>123
            ],

            // Juan 12th month
            5 => [ // Teacher A completed
                'Gross Motor' => ['raw' => 11, 'scaled' => 11],
                'Fine Motor' => ['raw' => 10, 'scaled' => 10],
                'Self-Help' => ['raw' => 25, 'scaled' => 13],
                'Receptive Language' => ['raw' => 5, 'scaled' => 11],
                'Expressive Language' => ['raw' => 7, 'scaled' => 11],
                'Cognitive' => ['raw' => 12, 'scaled' => 13],
                'Social-Emotional' => ['raw' => 23, 'scaled' => 11],
                // Sum scaled ≈ 80
            ],
            6 => [ // Family A completed
                'Gross Motor' => ['raw' => 11, 'scaled' => 11],
                'Fine Motor' => ['raw' => 11, 'scaled' => 12],
                'Self-Help' => ['raw' => 26, 'scaled' => 14],
                'Receptive Language' => ['raw' => 5, 'scaled' => 11],
                'Expressive Language' => ['raw' => 8, 'scaled' => 12],
                'Cognitive' => ['raw' => 13, 'scaled' => 14],
                'Social-Emotional' => ['raw' => 23, 'scaled' => 11],
                // Sum scaled ≈ 85
            ],

            // Maria 12th month
            10 => [ // Teacher A completed
                'Gross Motor' => ['raw' => 8, 'scaled' => 8],
                'Fine Motor' => ['raw' => 8, 'scaled' => 9],
                'Self-Help' => ['raw' => 20, 'scaled' => 10],
                'Receptive Language' => ['raw' => 3, 'scaled' => 6],
                'Expressive Language' => ['raw' => 4, 'scaled' => 5],
                'Cognitive' => ['raw' => 8, 'scaled' => 9],
                'Social-Emotional' => ['raw' => 16, 'scaled' => 8],
                // Sum scaled ≈ 55
            ],
            11 => [ // Family B completed
                'Gross Motor' => ['raw' => 9, 'scaled' => 9],
                'Fine Motor' => ['raw' => 9, 'scaled' => 10],
                'Self-Help' => ['raw' => 22, 'scaled' => 11],
                'Receptive Language' => ['raw' => 3, 'scaled' => 6],
                'Expressive Language' => ['raw' => 5, 'scaled' => 6],
                'Cognitive' => ['raw' => 9, 'scaled' => 10],
                'Social-Emotional' => ['raw' => 18, 'scaled' => 9],
                // Sum scaled ≈ 61
            ],

            // Pedro 12th month
            12 => [ // Teacher C finalized
                'Gross Motor' => ['raw' => 6, 'scaled' => 6],
                'Fine Motor' => ['raw' => 6, 'scaled' => 7],
                'Self-Help' => ['raw' => 18, 'scaled' => 9],
                'Receptive Language' => ['raw' => 2, 'scaled' => 5],
                'Expressive Language' => ['raw' => 3, 'scaled' => 4],
                'Cognitive' => ['raw' => 7, 'scaled' => 8],
                'Social-Emotional' => ['raw' => 15, 'scaled' => 7],
                // Sum scaled ≈ 46
            ],
        ];

        foreach ($testDomainScores as $testId => $byDomain) {
            foreach ($byDomain as $domainName => $scores) {
                if (!isset($domainIds[$domainName])) {
                    continue;
                }

                DB::table('test_domain_scaled_scores')->updateOrInsert(
                    [
                        'test_id' => $testId,
                        'domain_id' => $domainIds[$domainName],
                        'scale_version_id' => $scaleVersionId,
                    ],
                    [
                        'raw_score' => $scores['raw'],
                        'scaled_score' => $scores['scaled'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }
        }

        // Standard scores per test, using the ECCD 2004 lookup
        // (sum_scaled -> standard_score mapping from Eccd2004Seeder)
        $testStandard = [
            // Juan 6th month
            1 => ['sum_scaled' => 72, 'standard' => 100, 'interpretation' => 'Average Development'],
            2 => ['sum_scaled' => 67, 'standard' => 92, 'interpretation' => 'Average Development'],
            4 => ['sum_scaled' => 88, 'standard' => 123, 'interpretation' => 'Advanced Development'],

            // Juan 12th month
            5 => ['sum_scaled' => 75, 'standard' => 104, 'interpretation' => 'Average Development'],
            6 => ['sum_scaled' => 80, 'standard' => 111, 'interpretation' => 'Advanced Development'],

            // Maria 12th month
            10 => ['sum_scaled' => 54, 'standard' => 73, 'interpretation' => 'Re-Test After 6 months'],
            11 => ['sum_scaled' => 61, 'standard' => 84, 'interpretation' => 'Average Development'],

            // Pedro 12th month
            12 => ['sum_scaled' => 52, 'standard' => 70, 'interpretation' => 'Re-Test After 6 months'],
        ];

        foreach ($testStandard as $testId => $row) {
            DB::table('test_standard_scores')->updateOrInsert(
                [
                    'test_id' => $testId,
                    'scale_version_id' => $scaleVersionId,
                ],
                [
                    'sum_scaled_scores' => $row['sum_scaled'],
                    'standard_score' => $row['standard'],
                    'interpretation' => $row['interpretation'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        // Period-level summary scores (steps 7–11 of the flow)
        $periodSummaries = [
            // Juan 6th month (period 1): two teacher tests + one family test, major teacher-family discrepancy
            1 => [
                'teachers_standard_score_avg' => 96.00, // (100 + 92) / 2
                'family_standard_score' => 123,
                'final_standard_score' => (96.00 * 0.7) + (123 * 0.3),
                'final_interpretation' => 'Average Development',
                'teacher_discrepancy' => 'minor',
                'teacher_family_discrepancy' => 'major',
            ],

            // Juan 12th month (period 2): one teacher + one family, minor discrepancy
            2 => [
                'teachers_standard_score_avg' => 104.00,
                'family_standard_score' => 111,
                'final_standard_score' => (104.00 * 0.7) + (111 * 0.3),
                'final_interpretation' => 'Advanced Development',
                'teacher_discrepancy' => 'none',
                'teacher_family_discrepancy' => 'minor',
            ],

            // Maria 12th month (period 5): low teacher score, higher family score
            5 => [
                'teachers_standard_score_avg' => 73.00,
                'family_standard_score' => 84,
                'final_standard_score' => (73.00 * 0.7) + (84 * 0.3),
                'final_interpretation' => 'Re-Test After 6 months',
                'teacher_discrepancy' => 'none',
                'teacher_family_discrepancy' => 'minor',
            ],

            // Pedro 12th month (period 8): only teacher score available
            8 => [
                'teachers_standard_score_avg' => 70.00,
                'family_standard_score' => null,
                'final_standard_score' => 70.00,
                'final_interpretation' => 'Re-Test After 6 months',
                'teacher_discrepancy' => 'none',
                'teacher_family_discrepancy' => 'none',
            ],
        ];

        foreach ($periodSummaries as $periodId => $row) {
            DB::table('period_summary_scores')->updateOrInsert(
                ['period_id' => $periodId],
                [
                    'teachers_standard_score_avg' => $row['teachers_standard_score_avg'],
                    'family_standard_score' => $row['family_standard_score'],
                    'final_standard_score' => $row['final_standard_score'],
                    'final_interpretation' => $row['final_interpretation'],
                    'teacher_discrepancy' => $row['teacher_discrepancy'],
                    'teacher_family_discrepancy' => $row['teacher_family_discrepancy'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
