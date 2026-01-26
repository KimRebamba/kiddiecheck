<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

use App\Models\User;
use App\Models\Family;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Domain;
use App\Models\Section;
use App\Models\Test;
use App\Models\DomainScore;

class KiddieCheckDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure a section exists for listing students in Admin → Sections
        $section = Section::firstOrCreate([
            'name' => 'Demo Section'
        ], [
            'description' => 'Auto-created for demo scenarios'
        ]);
        // Ensure some domains exist
        $domains = Domain::orderBy('id')->get();
        if ($domains->count() === 0) {
            $domains = collect([
                Domain::create(['name' => 'Cognitive', 'description' => 'Cognitive development']),
                Domain::create(['name' => 'Motor', 'description' => 'Motor skills']),
                Domain::create(['name' => 'Language', 'description' => 'Language skills']),
            ]);
        }

        // Helper to create user by role
        $mkUser = function(string $name, string $email, string $role) {
            $u = User::firstOrCreate(['email' => $email], [
                'name' => $name,
                'password' => Hash::make('password123'),
                'role' => $role,
                'status' => 'active',
            ]);
            return $u;
        };

        // Scenario A: Student A with family + two teachers; tests by all three
        $famAUser = $mkUser('Family A', 'familyA@example.com', 'family');
        $teaA1User = $mkUser('Teacher A1', 'teacherA1@example.com', 'teacher');
        $teaA2User = $mkUser('Teacher A2', 'teacherA2@example.com', 'teacher');

        $familyA = Family::firstOrCreate(['user_id' => $famAUser->id], [
            'name' => 'Family A',
            'home_address' => '1 A Street',
        ]);
        // Teachers PK is users.id
        Teacher::firstOrCreate(['id' => $teaA1User->id], ['hire_date' => now()->subYears(2), 'status' => 'active']);
        Teacher::firstOrCreate(['id' => $teaA2User->id], ['hire_date' => now()->subYears(1), 'status' => 'active']);

        $studentA = Student::firstOrCreate([
            'family_id' => $familyA->id,
            'name' => 'Student A',
            'dob' => '2021-03-10',
            'gender' => 'female',
            'enrollment_date' => now()->subMonths(8)->toDateString(),
            'status' => 'active',
        ], [ 'section_id' => $section->id ]);
        // Assign teachers
        DB::table('teacher_student')->updateOrInsert([
            'teacher_id' => $teaA1User->id,
            'student_id' => $studentA->id,
            'role' => 'homeroom',
        ], [ 'assigned_at' => now(), 'status' => 'active' ]);
        DB::table('teacher_student')->updateOrInsert([
            'teacher_id' => $teaA2User->id,
            'student_id' => $studentA->id,
            'role' => 'specialist',
        ], [ 'assigned_at' => now(), 'status' => 'active' ]);

        // Create completed tests in current window (use window 2 start since enroll -8m)
        $win2StartA = \Illuminate\Support\Carbon::parse($studentA->enrollment_date)->addMonths(7)->toDateString();
        $this->makeCompletedTestWithScores($studentA, $teaA1User, $domains, $win2StartA);
        $this->makeCompletedTestWithScores($studentA, $teaA2User, $domains, $win2StartA);
        $this->makeCompletedTestWithScores($studentA, $famAUser, $domains, $win2StartA);

        // Scenario B: Student B with family + one teacher; tests by both
        $famBUser = $mkUser('Family B', 'familyB@example.com', 'family');
        $teaBUser = $mkUser('Teacher B', 'teacherB@example.com', 'teacher');

        $familyB = Family::firstOrCreate(['user_id' => $famBUser->id], [
            'name' => 'Family B', 'home_address' => '2 B Street'
        ]);
        Teacher::firstOrCreate(['id' => $teaBUser->id], ['hire_date' => now()->subYears(3), 'status' => 'active']);

        $studentB = Student::firstOrCreate([
            'family_id' => $familyB->id,
            'name' => 'Student B',
            'dob' => '2020-08-15',
            'gender' => 'male',
            'enrollment_date' => now()->subMonths(2)->toDateString(),
            'status' => 'active',
        ], [ 'section_id' => $section->id ]);
        DB::table('teacher_student')->updateOrInsert([
            'teacher_id' => $teaBUser->id,
            'student_id' => $studentB->id,
            'role' => 'homeroom',
        ], [ 'assigned_at' => now(), 'status' => 'active' ]);

        $win1StartB = \Illuminate\Support\Carbon::parse($studentB->enrollment_date)->addMonths(1)->toDateString();
        $this->makeCompletedTestWithScores($studentB, $teaBUser, $domains, $win1StartB);
        $this->makeCompletedTestWithScores($studentB, $famBUser, $domains, $win1StartB);

        // Scenario C: Student C with family + one teacher; test only by teacher
        $famCUser = $mkUser('Family C', 'familyC@example.com', 'family');
        $teaCUser = $mkUser('Teacher C', 'teacherC@example.com', 'teacher');

        $familyC = Family::firstOrCreate(['user_id' => $famCUser->id], [
            'name' => 'Family C', 'home_address' => '3 C Street'
        ]);
        Teacher::firstOrCreate(['id' => $teaCUser->id], ['hire_date' => now()->subYears(1), 'status' => 'active']);

        $studentC = Student::firstOrCreate([
            'family_id' => $familyC->id,
            'name' => 'Student C',
            'dob' => '2022-01-20',
            'gender' => 'female',
            'enrollment_date' => now()->subMonths(15)->toDateString(),
            'status' => 'active',
        ], [ 'section_id' => $section->id ]);
        DB::table('teacher_student')->updateOrInsert([
            'teacher_id' => $teaCUser->id,
            'student_id' => $studentC->id,
            'role' => 'homeroom',
        ], [ 'assigned_at' => now(), 'status' => 'active' ]);

        $win3StartC = \Illuminate\Support\Carbon::parse($studentC->enrollment_date)->addMonths(14)->toDateString();
        $this->makeCompletedTestWithScores($studentC, $teaCUser, $domains, $win3StartC);
    }

    private function makeCompletedTestWithScores(Student $student, User $observer, $domains, string $windowStartDate): void
    {
        // Find an available test_date within the 6-month window starting at windowStartDate
        $start = \Illuminate\Support\Carbon::parse($windowStartDate)->startOfDay();
        $end = $start->copy()->addMonths(6)->subDay();
        $used = Test::where('student_id', $student->id)
            ->whereBetween('test_date', [$start->toDateString(), $end->toDateString()])
            ->pluck('test_date')
            ->map(fn($d)=> (string)$d)
            ->toArray();
        $candidate = $start->toDateString();
        if (in_array($candidate, $used)) {
            $cursor = $start->copy();
            $found = null;
            while ($cursor->lte($end)) {
                $c = $cursor->toDateString();
                if (!in_array($c, $used)) { $found = $c; break; }
                $cursor->addDay();
            }
            if ($found) { $candidate = $found; } else { return; }
        }

        $test = Test::firstOrCreate([
            'student_id' => $student->id,
            'test_date' => $candidate,
        ], [
            'status' => 'completed',
            'started_at' => now()->subHour(),
            'submitted_by' => $observer->role,
            'submitted_at' => now(),
        ]);
        // Assign observer if missing or different
        if ($test->observer_id !== $observer->id) {
            // If this date was reused for another observer by previous seeding, try next free date
            if ($test->wasRecentlyCreated) {
                $test->observer_id = $observer->id;
                $test->save();
            } else {
                // pick another date within window
                $cursor = \Illuminate\Support\Carbon::parse($candidate)->addDay();
                $found = null;
                while ($cursor->lte($end)) {
                    $c = $cursor->toDateString();
                    $exists = Test::where('student_id', $student->id)->where('test_date', $c)->exists();
                    if (!$exists) { $found = $c; break; }
                    $cursor->addDay();
                }
                if ($found) {
                    $test = Test::create([
                        'student_id' => $student->id,
                        'observer_id' => $observer->id,
                        'test_date' => $found,
                        'status' => 'completed',
                        'started_at' => now()->subHour(),
                        'submitted_by' => $observer->role,
                        'submitted_at' => now(),
                    ]);
                }
            }
        }
        foreach ($domains as $d) {
            // Seed mid-range scaled scores with mild variance
            $scaled = max((int) config('eccd.scaled_score_min', 1), min((int) config('eccd.scaled_score_max', 19), rand(7, 13)));
            DomainScore::updateOrCreate([
                'test_id' => $test->id,
                'domain_id' => $d->id,
            ], [
                'raw_score' => $scaled, // placeholder raw equals scaled for demo
                'scaled_score' => $scaled,
                'scaled_score_based' => 10,
            ]);
        }
    }
}
