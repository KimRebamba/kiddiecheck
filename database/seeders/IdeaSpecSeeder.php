<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Family;
use App\Models\Section;
use App\Models\Student;
use App\Models\Domain;
use App\Models\Question;
use App\Models\Test;
use App\Models\AssessmentPeriod;

class IdeaSpecSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $admin = User::firstOrCreate(['email' => 'admin@example.com'], [ 'name' => 'Admin', 'password' => 'password', 'role' => 'admin', 'status' => 'active' ]);
        $t1u = User::firstOrCreate(['email' => 'teacher1@example.com'], [ 'name' => 'Teacher One', 'password' => 'password', 'role' => 'teacher', 'status' => 'active' ]);
        $t2u = User::firstOrCreate(['email' => 'teacher2@example.com'], [ 'name' => 'Teacher Two', 'password' => 'password', 'role' => 'teacher', 'status' => 'active' ]);
        $t3u = User::firstOrCreate(['email' => 'teacher3@example.com'], [ 'name' => 'Teacher Three', 'password' => 'password', 'role' => 'teacher', 'status' => 'active' ]);
        $f1u = User::firstOrCreate(['email' => 'family1@example.com'], [ 'name' => 'Family One', 'password' => 'password', 'role' => 'family', 'status' => 'active' ]);
        $f2u = User::firstOrCreate(['email' => 'family2@example.com'], [ 'name' => 'Family Two', 'password' => 'password', 'role' => 'family', 'status' => 'active' ]);
        $f3u = User::firstOrCreate(['email' => 'family3@example.com'], [ 'name' => 'Family Three', 'password' => 'password', 'role' => 'family', 'status' => 'active' ]);

        // Teacher profiles
        $t1 = Teacher::firstOrCreate(['id' => $t1u->id], ['hire_date' => now(), 'status' => 'active']);
        $t2 = Teacher::firstOrCreate(['id' => $t2u->id], ['hire_date' => now(), 'status' => 'active']);
        $t3 = Teacher::firstOrCreate(['id' => $t3u->id], ['hire_date' => now(), 'status' => 'active']);

        // Families
        $fam1 = Family::firstOrCreate(['user_id' => $f1u->id], ['name' => 'Family One', 'home_address' => 'A Street']);
        $fam2 = Family::firstOrCreate(['user_id' => $f2u->id], ['name' => 'Family Two', 'home_address' => 'B Street']);
        $fam3 = Family::firstOrCreate(['user_id' => $f3u->id], ['name' => 'Family Three', 'home_address' => 'C Street']);

        // Section
        $sec = Section::firstOrCreate(['name' => 'Kinder A']);

        // Domains & Questions
        $domains = collect(['Cognitive','Motor','Language'])->map(function($name){ return Domain::firstOrCreate(['name' => $name]); });
        foreach ($domains as $d) {
            for ($i=1;$i<=5;$i++) {
                Question::firstOrCreate(['domain_id' => $d->id, 'question_text' => $d->name.' Q'.$i], ['type' => 'static']);
            }
        }

        // Helper to create finalized test with domain scores
        $makeFinalized = function(Student $student, $periodIndex, User $observer, array $scaledByDomain, ?string $note = null) use ($domains) {
            $period = $student->assessmentPeriods()->where('index', $periodIndex)->first();
            if (!$period) { return null; }
            $base = Carbon::parse($period->starts_at)->addDays(3);
            while (DB::table('tests')
                ->where('student_id', $student->id)
                ->where('test_date', $base->toDateString())
                ->exists()) {
                $base->addDay();
            }
            $date = $base->toDateString();
            $id = DB::table('tests')->insertGetId([
                'student_id' => $student->id,
                'assessment_period_id' => $period->id,
                'observer_id' => $observer->id,
                'test_date' => $date,
                'status' => 'finalized',
                'submitted_by' => $observer->role,
                'submitted_at' => now(),
            ]);
            foreach ($domains as $d) {
                $scaled = $scaledByDomain[$d->name] ?? null;
                DB::table('domain_scores')->updateOrInsert([
                    'test_id' => $id,
                    'domain_id' => $d->id,
                ], [
                    'raw_score' => $scaled !== null ? (float) $scaled : null,
                    'scaled_score' => $scaled !== null ? (float) $scaled : null,
                    'scaled_score_based' => $note === 'all_na' ? 0 : 5,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            return $id;
        };

        // Ensure periods exist utility
        $ensurePeriods = function (Student $student) {
            if ($student->assessmentPeriods()->count() >= 3) {
                return;
            }
            $months = (int) config('eccd.period.months', 6);
            $graceDays = (int) config('eccd.period.teacher_grace_days', 7);
            $start = Carbon::parse($student->enrollment_date)->startOfDay();
            for ($i = 1; $i <= 3; $i++) {
                $pStart = (clone $start)->addMonths($months * ($i - 1));
                $pEnd = (clone $pStart)->addMonths($months)->subSecond();
                $grace = (clone $pEnd)->addDays($graceDays);
                AssessmentPeriod::firstOrCreate([
                    'student_id' => $student->id,
                    'index' => $i,
                ], [
                    'starts_at' => $pStart,
                    'ends_at' => $pEnd,
                    'teacher_grace_end' => $grace,
                    'status' => $i === 1 ? 'active' : 'scheduled',
                ]);
            }
        };

        // Students and scenarios
        // S1: Family-only finalized in Period 1
        $s1 = Student::create([
            'family_id' => $fam1->id, 'section_id' => $sec->id, 'name' => 'Child S1', 'dob' => '2022-06-01',
            'gender' => 'male', 'handedness' => 'unknown', 'is_studying' => false, 'school_name' => null,
            'enrollment_date' => Carbon::now()->subMonths(1)->toDateString(), 'status' => 'active'
        ]);
        $ensurePeriods($s1);
        $makeFinalized($s1, 1, $f1u, ['Cognitive'=>12,'Motor'=>10,'Language'=>11]);

        // S2: Two teachers finalized in Period 1 + family to trigger discrepancies
        $s2 = Student::create([
            'family_id' => $fam2->id, 'section_id' => $sec->id, 'name' => 'Child S2', 'dob' => '2022-01-01',
            'gender' => 'female', 'handedness' => 'unknown', 'is_studying' => false, 'school_name' => null,
            'enrollment_date' => Carbon::now()->subMonths(2)->toDateString(), 'status' => 'active'
        ]);
        $ensurePeriods($s2);
        // assign teachers
        $s2->teachers()->attach([$t1->id => ['role'=>'homeroom','assigned_at'=>now(),'status'=>'active']]);
        $s2->teachers()->attach([$t2->id => ['role'=>'specialist','assigned_at'=>now(),'status'=>'active']]);
        $makeFinalized($s2, 1, $t1u, ['Cognitive'=>14,'Motor'=>13,'Language'=>15]);
        $makeFinalized($s2, 1, $t2u, ['Cognitive'=>8,'Motor'=>9,'Language'=>7]);
        $makeFinalized($s2, 1, $f2u, ['Cognitive'=>11,'Motor'=>12,'Language'=>10]);

        // S3: Teacher draft terminated due to unassignment
        $s3 = Student::create([
            'family_id' => $fam3->id, 'section_id' => $sec->id, 'name' => 'Child S3', 'dob' => '2021-12-12',
            'gender' => 'male', 'handedness' => 'unknown', 'is_studying' => false, 'school_name' => null,
            'enrollment_date' => Carbon::now()->subMonths(1)->toDateString(), 'status' => 'active'
        ]);
        $ensurePeriods($s3);
        $s3->teachers()->attach([$t3->id => ['role'=>'homeroom','assigned_at'=>now(),'status'=>'active']]);
        // Create draft then detach teacher
        $p3 = $s3->assessmentPeriods()->where('index',1)->first();
        $draftId = DB::table('tests')->insertGetId([
            'student_id' => $s3->id,
            'assessment_period_id' => $p3->id,
            'observer_id' => $t3u->id,
            'test_date' => Carbon::parse($p3->starts_at)->addDays(2)->toDateString(),
            'status' => 'draft',
            'started_at' => now(),
        ]);
        $s3->teachers()->detach($t3->id);
        DB::table('tests')->where('id', $draftId)->update(['status' => 'terminated', 'termination_reason' => 'Teacher unassigned']);

        // S4: Window expired mid-assessment (terminated)
        $s4 = Student::create([
            'family_id' => null, 'section_id' => $sec->id, 'name' => 'Child S4', 'dob' => '2021-01-01',
            'gender' => 'female', 'handedness' => 'unknown', 'is_studying' => false, 'school_name' => null,
            'enrollment_date' => Carbon::now()->subMonths(12)->toDateString(), 'status' => 'active'
        ]);
        $ensurePeriods($s4);
        $p4 = $s4->assessmentPeriods()->where('index', 1)->first();
        DB::table('assessment_periods')->where('id',$p4->id)->update(['starts_at' => Carbon::now()->subMonths(13), 'ends_at' => Carbon::now()->subMonths(7)]);
        $expiredId = DB::table('tests')->insertGetId([
            'student_id' => $s4->id,
            'assessment_period_id' => $p4->id,
            'observer_id' => $t1u->id,
            'test_date' => Carbon::now()->subMonths(8)->toDateString(),
            'status' => 'terminated',
            'termination_reason' => 'Eligibility window expired',
        ]);

        // S5: All domains N/A by teacher
        $s5 = Student::create([
            'family_id' => $fam1->id, 'section_id' => $sec->id, 'name' => 'Child S5', 'dob' => '2022-08-08',
            'gender' => 'male', 'handedness' => 'unknown', 'is_studying' => false, 'school_name' => null,
            'enrollment_date' => Carbon::now()->subMonths(1)->toDateString(), 'status' => 'active'
        ]);
        $ensurePeriods($s5);
        $makeFinalized($s5, 1, $t1u, ['Cognitive'=>null,'Motor'=>null,'Language'=>null], 'all_na');

        // S6: No data available in Period 2 (no finalized)
        $s6 = Student::create([
            'family_id' => $fam2->id, 'section_id' => $sec->id, 'name' => 'Child S6', 'dob' => '2023-03-03',
            'gender' => 'female', 'handedness' => 'unknown', 'is_studying' => false, 'school_name' => null,
            'enrollment_date' => Carbon::now()->subMonths(1)->toDateString(), 'status' => 'active'
        ]);
        $ensurePeriods($s6);
        // Period 2 terminated examples
        $p6_2 = $s6->assessmentPeriods()->where('index',2)->first();
        DB::table('tests')->insert([
            'student_id' => $s6->id,
            'assessment_period_id' => $p6_2->id,
            'observer_id' => $t1u->id,
            'test_date' => Carbon::parse($p6_2->starts_at)->addDays(1)->toDateString(),
            'status' => 'terminated',
            'termination_reason' => 'Eligibility window expired',
        ]);

        // S7: Student transferred after completing Period 1; future periods terminated
        $s7 = Student::create([
            'family_id' => $fam3->id, 'section_id' => $sec->id, 'name' => 'Child S7', 'dob' => '2021-09-09',
            'gender' => 'female', 'handedness' => 'unknown', 'is_studying' => false, 'school_name' => null,
            'enrollment_date' => Carbon::now()->subMonths(2)->toDateString(), 'status' => 'active'
        ]);
        $ensurePeriods($s7);
        $makeFinalized($s7, 1, $t2u, ['Cognitive'=>10,'Motor'=>12,'Language'=>9]);
        // Mark transferred and terminate future period tests
        $s7->status = 'transferred'; $s7->save();
        foreach ([2,3] as $idx) {
            $p = $s7->assessmentPeriods()->where('index',$idx)->first();
            if ($p) {
                DB::table('tests')->insert([
                    'student_id' => $s7->id,
                    'assessment_period_id' => $p->id,
                    'observer_id' => $t1u->id,
                    'test_date' => Carbon::parse($p->starts_at)->addDays(1)->toDateString(),
                    'status' => 'terminated',
                    'termination_reason' => 'Student unenrolled / transferred',
                ]);
            }
        }

        // S8: Longitudinal complete across 1,2,3
        $s8 = Student::create([
            'family_id' => $fam1->id, 'section_id' => $sec->id, 'name' => 'Child S8', 'dob' => '2021-05-05',
            'gender' => 'male', 'handedness' => 'unknown', 'is_studying' => false, 'school_name' => null,
            'enrollment_date' => Carbon::now()->subMonths(18)->toDateString(), 'status' => 'active'
        ]);
        $ensurePeriods($s8);
        $makeFinalized($s8, 1, $t1u, ['Cognitive'=>9,'Motor'=>11,'Language'=>10]);
        $makeFinalized($s8, 2, $t1u, ['Cognitive'=>12,'Motor'=>13,'Language'=>11]);
        $makeFinalized($s8, 3, $t1u, ['Cognitive'=>14,'Motor'=>15,'Language'=>13]);
    }
}
