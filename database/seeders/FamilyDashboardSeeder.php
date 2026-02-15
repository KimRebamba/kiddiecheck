<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Family;
use App\Models\Student;
use App\Models\AssessmentPeriod;
use App\Models\Test;
use App\Models\TestStandardScore;
use App\Models\ScaleVersion;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class FamilyDashboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding ECCD Checklist System...');

        // Create scale version first (needed for test scores)
        $scaleVersion = ScaleVersion::firstOrCreate(
            ['name' => 'ECCD 2004'],
            ['description' => 'Early Childhood Care and Development Checklist 2004']
        );

        // Create family users and their data
        $this->createFamilies($scaleVersion);

        $this->command->info('');
        $this->command->info('✓ Seeding completed successfully!');
        $this->command->info('');
        $this->command->info('Test Family Credentials:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('Email: santos@family.com  | Password: password');
        $this->command->info('Email: reyes@family.com   | Password: password');
        $this->command->info('Email: cruz@family.com    | Password: password');
    }

    /**
     * Create families with their children and assessments
     */
    private function createFamilies($scaleVersion): void
    {
        $families = [
            [
                'username' => 'santos_family',
                'email' => 'santos@family.com',
                'family_name' => 'Santos Family',
                'home_address' => 'Tanay, Calabarzon, Philippines',
                'emergency_contact' => 'Maria Santos',
                'emergency_phone' => '+63 912 345 6789'
            ],
            [
                'username' => 'reyes_family',
                'email' => 'reyes@family.com',
                'family_name' => 'Reyes Family',
                'home_address' => 'Antipolo, Rizal, Philippines',
                'emergency_contact' => 'Juan Reyes',
                'emergency_phone' => '+63 917 234 5678'
            ],
            [
                'username' => 'cruz_family',
                'email' => 'cruz@family.com',
                'family_name' => 'Cruz Family',
                'home_address' => 'Cainta, Rizal, Philippines',
                'emergency_contact' => 'Ana Cruz',
                'emergency_phone' => '+63 923 456 7890'
            ]
        ];

        foreach ($families as $familyData) {
            // Create user account
            $user = User::create([
                'username' => $familyData['username'],
                'email' => $familyData['email'],
                'password' => Hash::make('password'),
                'role' => 'family',
            ]);

            // Create family profile
            $family = Family::create([
                'user_id' => $user->user_id,
                'family_name' => $familyData['family_name'],
                'home_address' => $familyData['home_address'],
                'emergency_contact' => $familyData['emergency_contact'],
                'emergency_phone' => $familyData['emergency_phone'],
            ]);

            // Create children for this family
            $this->createChildrenForFamily($family, $user, $scaleVersion);
        }
    }

    /**
     * Create children and their assessments for a family
     */
    private function createChildrenForFamily($family, $familyUser, $scaleVersion): void
    {
        $children = [
            [
                'first_name' => 'Maria',
                'last_name' => explode(' ', $family->family_name)[0],
                'date_of_birth' => Carbon::now()->subYears(4)->subMonths(3),
                'test_count' => 3
            ],
            [
                'first_name' => 'Juan',
                'last_name' => explode(' ', $family->family_name)[0],
                'date_of_birth' => Carbon::now()->subYears(3)->subMonths(5),
                'test_count' => 2
            ],
            [
                'first_name' => 'Sofia',
                'last_name' => explode(' ', $family->family_name)[0],
                'date_of_birth' => Carbon::now()->subYears(5)->subMonths(2),
                'test_count' => 4
            ]
        ];

        foreach ($children as $childData) {
            $testCount = $childData['test_count'];
            unset($childData['test_count']);
            
            // Create student
            $student = Student::create([
                'family_id' => $family->user_id,
                ...$childData
            ]);

            // Create assessment periods and tests
            $this->createAssessmentsForStudent($student, $familyUser, $testCount, $scaleVersion);
        }
    }

    /**
     * Create assessment periods and tests for a student
     */
    private function createAssessmentsForStudent($student, $familyUser, $testCount, $scaleVersion): void
    {
        // Create past assessment periods with completed tests
        for ($i = 0; $i < $testCount; $i++) {
            $startDate = Carbon::now()->subMonths(($testCount - $i) * 3);
            $endDate = $startDate->copy()->addMonth();

            // Create assessment period
            $period = AssessmentPeriod::create([
                'student_id' => $student->student_id,
                'description' => 'Q' . ($i + 1) . ' ' . $startDate->year . ' Development Assessment',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => 'completed'
            ]);

            // Create test for this period
            $testDate = $startDate->copy()->addDays(rand(5, 25));
            
            $test = Test::create([
                'period_id' => $period->period_id,
                'student_id' => $student->student_id,
                'examiner_id' => $familyUser->user_id,
                'test_date' => $testDate,
                'notes' => 'Family assessment completed successfully.',
                'status' => 'finalized'
            ]);

            // Create test standard score
            $standardScore = rand(85, 125); // Typical range for standard scores
            $interpretation = $this->getInterpretation($standardScore, $student->date_of_birth);

            TestStandardScore::create([
                'test_id' => $test->test_id,
                'scale_version_id' => $scaleVersion->scale_version_id,
                'sum_scaled_scores' => rand(40, 80), // Sum of domain scaled scores
                'standard_score' => $standardScore,
                'interpretation' => $interpretation
            ]);
        }

        // Create upcoming assessment periods
        $upcomingDates = [
            ['months' => 1, 'status' => 'scheduled'],
            ['months' => 4, 'status' => 'scheduled'],
            ['months' => 7, 'status' => 'scheduled'],
        ];

        foreach ($upcomingDates as $upcoming) {
            $startDate = Carbon::now()->addMonths($upcoming['months']);
            $endDate = $startDate->copy()->addMonth();

            AssessmentPeriod::create([
                'student_id' => $student->student_id,
                'description' => $startDate->format('F Y') . ' Development Assessment',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $upcoming['status']
            ]);
        }
    }

    /**
     * Get interpretation based on standard score and age
     */
    private function getInterpretation($standardScore, $birthDate): string
    {
        // Age-based interpretation logic (simplified)
        if ($standardScore >= 115) {
            return 'Advanced Development';
        } elseif ($standardScore >= 85) {
            return 'Average Development';
        } else {
            return 'Re-Test After 6 Months';
        }
    }
}
