<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\AssessmentPeriod;
use App\Models\Test;

class EnforceAssessmentPeriods extends Command
{
    protected $signature = 'eccd:enforce-periods';
    protected $description = 'Update assessment periods statuses and auto-terminate expired drafts/in-progress tests';

    public function handle(): int
    {
        $now = Carbon::now();
        $teacherGraceDays = (int) config('eccd.period.teacher_grace_days', 7);

        $countUpdated = 0;
        $countTerminated = 0;

        AssessmentPeriod::query()->orderBy('starts_at')->chunk(500, function ($chunk) use ($now, $teacherGraceDays, &$countUpdated, &$countTerminated) {
            foreach ($chunk as $p) {
                $status = 'scheduled';
                if ($now->between($p->starts_at, $p->ends_at)) {
                    $status = 'active';
                } elseif ($now->greaterThan($p->ends_at)) {
                    $status = 'closed';
                }
                if ($status !== $p->status) {
                    $p->status = $status;
                    $p->save();
                    $countUpdated++;
                }

                // Auto-terminate tests beyond window/grace
                // Teachers: grace applies; Family: no grace.
                $teacherDeadline = Carbon::parse($p->ends_at)->addDays($teacherGraceDays);
                $familyDeadline = Carbon::parse($p->ends_at);

                $query = Test::query()
                    ->where('assessment_period_id', $p->id)
                    ->whereIn('status', ['draft','pending','in_progress','paused'])
                    ->with(['observer','student.teachers']);

                foreach ($query->get() as $t) {
                    $role = $t->observer?->role;
                    $deadline = $role === 'teacher' ? $teacherDeadline : $familyDeadline;
                    $terminate = false;
                    if ($now->greaterThan($deadline)) { $terminate = true; }
                    // Terminate teacher drafts if teacher no longer assigned
                    if ($role === 'teacher') {
                        // Use explicit pivot column to check active assignment
                        $assigned = $t->student->teachers()
                            ->where('teacher_id', $t->observer_id)
                            ->where('teacher_student.status','active')
                            ->exists();
                        if (!$assigned) { $terminate = true; }
                    }
                    // Terminate drafts if student is unenrolled/transferred
                    if (in_array($t->student->status, ['transferred','graduated'])) { $terminate = true; }
                    if ($terminate) {
                        $t->status = 'terminated';
                        if ($now->greaterThan($deadline)) {
                            $t->termination_reason = 'Eligibility window expired';
                        } elseif ($role === 'teacher' && !$assigned) {
                            $t->termination_reason = 'Teacher unassigned';
                        } elseif (in_array($t->student->status, ['transferred','graduated'])) {
                            $t->termination_reason = 'Student unenrolled / transferred';
                        }
                        $t->save();
                        $countTerminated++;
                    }
                }
            }
        });

        $this->info("Periods updated: {$countUpdated}; Tests terminated: {$countTerminated}");
        return Command::SUCCESS;
    }
}
