<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssessmentPeriodsSeeder extends Seeder
{
    public function run()
    {
        $now = now();
        
        // Clear existing assessment periods
        DB::table('assessment_periods')->delete();
        
        // Each enrolled student gets three assessment periods
        // representing 6th, 12th, and 18th months (6 months apart)
        DB::table('assessment_periods')->insert([
            // Juan (student 1)
            [
                'period_id' => 1,
                'description' => 'Juan - 6th month',
                'student_id' => 1,
                'start_date' => '2025-01-01',
                'end_date' => '2025-06-30',
                'status' => 'completed',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'period_id' => 2,
                'description' => 'Juan - 12th month',
                'student_id' => 1,
                'start_date' => '2025-07-01',
                'end_date' => '2025-12-31',
                'status' => 'completed',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'period_id' => 3,
                'description' => 'Juan - 18th month',
                'student_id' => 1,
                'start_date' => '2026-01-01',
                'end_date' => '2026-06-30',
                'status' => 'scheduled',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Maria (student 2)
            [
                'period_id' => 4,
                'description' => 'Maria - 6th month',
                'student_id' => 2,
                'start_date' => '2025-03-01',
                'end_date' => '2025-08-31',
                'status' => 'completed',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'period_id' => 5,
                'description' => 'Maria - 12th month',
                'student_id' => 2,
                'start_date' => '2025-09-01',
                'end_date' => '2026-02-28',
                'status' => 'scheduled',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'period_id' => 6,
                'description' => 'Maria - 18th month',
                'student_id' => 2,
                'start_date' => '2026-03-01',
                'end_date' => '2026-08-31',
                'status' => 'scheduled',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Pedro (student 3)
            [
                'period_id' => 7,
                'description' => 'Pedro - 6th month',
                'student_id' => 3,
                'start_date' => '2025-05-01',
                'end_date' => '2025-10-31',
                'status' => 'completed',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'period_id' => 8,
                'description' => 'Pedro - 12th month',
                'student_id' => 3,
                'start_date' => '2025-11-01',
                'end_date' => '2026-04-30',
                'status' => 'scheduled',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'period_id' => 9,
                'description' => 'Pedro - 18th month',
                'student_id' => 3,
                'start_date' => '2026-05-01',
                'end_date' => '2026-10-31',
                'status' => 'scheduled',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        $this->command->info('Assessment periods seeded successfully!');
    }
}
