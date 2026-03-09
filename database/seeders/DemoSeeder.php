<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->users_add();
        $this->families_add();
        $this->students_add();
        $this->studentTeacher_add();
        $this->assessmentPeriods_add();
    }

    protected function users_add(): void
    {
        $now = now();
        
        $users = [
            // Admin user
            [
                'user_id' => 1,
                'username' => 'admin',
                'email' => 'admin@kiddiecheck.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Teachers
            [
                'user_id' => 2,
                'username' => 'Teacher_A',
                'email' => 'teacher_a@kiddiecheck.com',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 3,
                'username' => 'Teacher_B',
                'email' => 'teacher_b@kiddiecheck.com',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 4,
                'username' => 'Teacher_C',
                'email' => 'teacher_c@kiddiecheck.com',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Families
            [
                'user_id' => 5,
                'username' => 'Family_A',
                'email' => 'family_a@kiddiecheck.com',
                'password' => Hash::make('password'),
                'role' => 'family',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 6,
                'username' => 'Family_B',
                'email' => 'family_b@kiddiecheck.com',
                'password' => Hash::make('password'),
                'role' => 'family',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 7,
                'username' => 'Family_C',
                'email' => 'family_c@kiddiecheck.com',
                'password' => Hash::make('password'),
                'role' => 'family',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['user_id' => $user['user_id']],
                $user
            );
        }
    }

    protected function families_add(): void
    {
        $now = now();
        
        $families = [
            [
                'user_id' => 5,
                'family_name' => 'Family A',
                'home_address' => 'Address A',
                'emergency_contact' => 'Parent A',
                'emergency_phone' => '123-456-7890',
                'feature_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 6,
                'family_name' => 'Family B',
                'home_address' => 'Address B',
                'emergency_contact' => 'Parent B',
                'emergency_phone' => '234-567-8901',
                'feature_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 7,
                'family_name' => 'Family C',
                'home_address' => 'Address C',
                'emergency_contact' => 'Parent C',
                'emergency_phone' => '345-678-9012',
                'feature_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($families as $family) {
            DB::table('families')->updateOrInsert(
                ['user_id' => $family['user_id']],
                $family
            );
        }
    }

    protected function students_add(): void
    {
        $now = now();
        
        // Clear existing students to avoid conflicts
        DB::table('students')->delete();
        
        $students = [
            // Teacher_A students - ALL in Section 1 but from different families
            [
                'student_id' => 1,
                'first_name' => 'Juan',
                'last_name' => 'Cruz',
                'date_of_birth' => '2021-01-15',
                'family_id' => 5, // Family A
                'section_id' => 1, // Section A
                'feature_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'student_id' => 2,
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'date_of_birth' => '2020-06-10',
                'family_id' => 6, // Family B
                'section_id' => 1, // Section A
                'feature_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'student_id' => 3,
                'first_name' => 'Pedro',
                'last_name' => 'Reyes',
                'date_of_birth' => '2019-03-20',
                'family_id' => 7, // Family C
                'section_id' => 1, // Section A
                'feature_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'student_id' => 4,
                'first_name' => 'Ana',
                'last_name' => 'Garcia',
                'date_of_birth' => '2020-08-15',
                'family_id' => 5, // Family A
                'section_id' => 1, // Section A
                'feature_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Teacher_B students - ALL in Section 2 but from different families
            [
                'student_id' => 5,
                'first_name' => 'Carlos',
                'last_name' => 'Mendoza',
                'date_of_birth' => '2019-11-22',
                'family_id' => 5, // Family A
                'section_id' => 2, // Section B
                'feature_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'student_id' => 6,
                'first_name' => 'Sofia',
                'last_name' => 'Lopez',
                'date_of_birth' => '2021-03-10',
                'family_id' => 6, // Family B
                'section_id' => 2, // Section B
                'feature_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'student_id' => 7,
                'first_name' => 'Diego',
                'last_name' => 'Rivera',
                'date_of_birth' => '2018-07-05',
                'family_id' => 7, // Family C
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
                'family_id' => 5, // Family A
                'section_id' => 2, // Section B
                'feature_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Teacher_C students - ALL in Section 3 but from different families
            [
                'student_id' => 9,
                'first_name' => 'Mateo',
                'last_name' => 'Vargas',
                'date_of_birth' => '2019-09-18',
                'family_id' => 6, // Family B
                'section_id' => 3, // Section C
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
                'family_id' => 5, // Family A
                'section_id' => 3, // Section C
                'feature_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'student_id' => 12,
                'first_name' => 'Valentina',
                'last_name' => 'Mendoza',
                'date_of_birth' => '2020-11-30',
                'family_id' => 6, // Family B
                'section_id' => 3, // Section C
                'feature_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('students')->insert($students);
    }

    protected function studentTeacher_add(): void
    {
        $now = now();
        
        // Clear existing assignments
        DB::table('student_teacher')->delete();
        
        $assignments = [
            // Teacher_A (user_id = 2) - Students 1, 2, 3, 4 (all in Section 1)
            ['student_id' => 1, 'teacher_id' => 2],
            ['student_id' => 2, 'teacher_id' => 2],
            ['student_id' => 3, 'teacher_id' => 2],
            ['student_id' => 4, 'teacher_id' => 2],
            // Teacher_B (user_id = 3) - Students 5, 6, 7, 8 (all in Section 2)
            ['student_id' => 5, 'teacher_id' => 3],
            ['student_id' => 6, 'teacher_id' => 3],
            ['student_id' => 7, 'teacher_id' => 3],
            ['student_id' => 8, 'teacher_id' => 3],
            // Teacher_C (user_id = 4) - Students 9, 10, 11, 12 (all in Section 3)
            ['student_id' => 9, 'teacher_id' => 4],
            ['student_id' => 10, 'teacher_id' => 4],
            ['student_id' => 11, 'teacher_id' => 4],
            ['student_id' => 12, 'teacher_id' => 4],
        ];

        DB::table('student_teacher')->insert($assignments);
    }

    protected function assessmentPeriods_add(): void
    {
        $now = now();
        
        // Clear existing assessment periods
        DB::table('assessment_periods')->delete();
        
        // Create assessment periods for all students (1-12)
        $periods = [];
        
        // Students 1-3 (existing pattern from AssessmentPeriodsSeeder)
        // Juan (student 1)
        $periods[] = [
            'description' => 'Juan - 6th month',
            'student_id' => 1,
            'start_date' => '2025-01-01',
            'end_date' => '2025-06-30',
            'status' => 'completed',
            'created_at' => $now,
            'updated_at' => $now,
        ];
        $periods[] = [
            'description' => 'Juan - 12th month',
            'student_id' => 1,
            'start_date' => '2025-07-01',
            'end_date' => '2025-12-31',
            'status' => 'completed',
            'created_at' => $now,
            'updated_at' => $now,
        ];
        $periods[] = [
            'description' => 'Juan - 18th month',
            'student_id' => 1,
            'start_date' => '2026-01-01',
            'end_date' => '2026-06-30',
            'status' => 'scheduled',
            'created_at' => $now,
            'updated_at' => $now,
        ];
        
        // Maria (student 2)
        $periods[] = [
            'description' => 'Maria - 6th month',
            'student_id' => 2,
            'start_date' => '2025-03-01',
            'end_date' => '2025-08-31',
            'status' => 'completed',
            'created_at' => $now,
            'updated_at' => $now,
        ];
        $periods[] = [
            'description' => 'Maria - 12th month',
            'student_id' => 2,
            'start_date' => '2025-09-01',
            'end_date' => '2026-02-28',
            'status' => 'scheduled',
            'created_at' => $now,
            'updated_at' => $now,
        ];
        $periods[] = [
            'description' => 'Maria - 18th month',
            'student_id' => 2,
            'start_date' => '2026-03-01',
            'end_date' => '2026-08-31',
            'status' => 'scheduled',
            'created_at' => $now,
            'updated_at' => $now,
        ];
        
        // Pedro (student 3)
        $periods[] = [
            'description' => 'Pedro - 6th month',
            'student_id' => 3,
            'start_date' => '2025-05-01',
            'end_date' => '2025-10-31',
            'status' => 'completed',
            'created_at' => $now,
            'updated_at' => $now,
        ];
        $periods[] = [
            'description' => 'Pedro - 12th month',
            'student_id' => 3,
            'start_date' => '2025-11-01',
            'end_date' => '2026-04-30',
            'status' => 'scheduled',
            'created_at' => $now,
            'updated_at' => $now,
        ];
        $periods[] = [
            'description' => 'Pedro - 18th month',
            'student_id' => 3,
            'start_date' => '2026-05-01',
            'end_date' => '2026-10-31',
            'status' => 'scheduled',
            'created_at' => $now,
            'updated_at' => $now,
        ];
        
        // Students 4-12 (new students with current timing)
        foreach (range(4, 12) as $studentId) {
            for ($i = 1; $i <= 3; $i++) {
                if ($i == 1) {
                    // Period 1: Current - immediately available
                    $pStart = $now->copy()->startOfDay();
                    $pEnd = $pStart->copy()->addMonths(6);
                } elseif ($i == 2) {
                    // Period 2: Available after 6 months
                    $pStart = $now->copy()->addMonths(6)->startOfDay();
                    $pEnd = $pStart->copy()->addMonths(6);
                } else {
                    // Period 3: Available after 12 months
                    $pStart = $now->copy()->addMonths(12)->startOfDay();
                    $pEnd = $pStart->copy()->addMonths(6);
                }
                
                $periods[] = [
                    'description' => "Assessment Period $i",
                    'student_id' => $studentId,
                    'start_date' => $pStart->toDateString(),
                    'end_date' => $pEnd->toDateString(),
                    'status' => 'scheduled',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
        
        DB::table('assessment_periods')->insert($periods);
    }
}
