<?php

namespace App\Services;

use App\Models\AssessmentPeriod;
use App\Models\Domain;
use App\Models\PeriodSummaryScore;
use App\Models\ScaleVersion;
use App\Models\StandardScoreScale;
use App\Models\Student;
use App\Models\Test;
use App\Models\TestDomainScaledScore;
use App\Models\TestResponse;
use App\Models\TestStandardScore;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class EccdScoring
{
    /**
     * Ensure test_domain_scaled_scores and test_standard_scores are populated for a test.
     */
    public function scoreTest(Test $test): ?TestStandardScore
    {
        $student = $test->student;
        if (!$student instanceof Student) {
            $student = Student::find($test->student_id);
        }

        if (!$student || !$student->date_of_birth) {
            return null;
        }

        $scaleVersion = ScaleVersion::where('name', 'ECCD 2004')->first();
        if (!$scaleVersion) {
            return null;
        }

        $scaleVersionId = $scaleVersion->scale_version_id;
        // Prefer age at test date when available; otherwise fall back to current age in months.
        $ageMonths = $test->age_months ?? ($student->age_in_months ?? $student->getAgeInMonthsAttribute());

        $domains = Domain::orderBy('domain_id')->get();
        if ($domains->isEmpty()) {
            return null;
        }

        $sumScaled = 0;

        DB::transaction(function () use ($test, $domains, $scaleVersionId, $ageMonths, &$sumScaled) {
            // Clear any existing per-domain scores for this test to avoid stale data.
            TestDomainScaledScore::where('test_id', $test->test_id)->delete();

            foreach ($domains as $domain) {
                $questionIds = $domain->questions()
                    ->where('scale_version_id', $scaleVersionId)
                    ->pluck('question_id')
                    ->all();

                if (empty($questionIds)) {
                    continue;
                }

                $yesCount = TestResponse::where('test_id', $test->test_id)
                    ->whereIn('question_id', $questionIds)
                    ->where('response', 'yes')
                    ->count();

                // Look up scaled score for this domain based on age and raw score.
                $scaledRow = DB::table('domain_scaled_scores')
                    ->where('scale_version_id', $scaleVersionId)
                    ->where('domain_id', $domain->domain_id)
                    ->where('age_min_months', '<=', $ageMonths)
                    ->where('age_max_months', '>=', $ageMonths)
                    ->where('raw_min', '<=', $yesCount)
                    ->where('raw_max', '>=', $yesCount)
                    ->orderBy('age_min_months')
                    ->first();

                if (!$scaledRow) {
                    continue;
                }

                $scaledScore = (int) $scaledRow->scaled_score;
                $sumScaled += $scaledScore;

                TestDomainScaledScore::updateOrCreate(
                    [
                        'test_id' => $test->test_id,
                        'domain_id' => $domain->domain_id,
                    ],
                    [
                        'scale_version_id' => $scaleVersionId,
                        'raw_score' => $yesCount,
                        'scaled_score' => $scaledScore,
                    ]
                );
            }
        });

        if ($sumScaled <= 0) {
            return null;
        }

        // Map sum_scaled_scores to a standard score using standard_score_scales table.
        $standardRow = StandardScoreScale::where('scale_version_id', $scaleVersionId)
            ->where('sum_scaled_min', '<=', $sumScaled)
            ->where('sum_scaled_max', '>=', $sumScaled)
            ->orderBy('sum_scaled_min')
            ->first();

        if (!$standardRow) {
            Log::warning('No standard_score_scales row for sum_scaled_scores=' . $sumScaled . ' (test ' . $test->test_id . ').');
            return null;
        }

        $standardScore = (int) $standardRow->standard_score;
        $interpretation = $this->interpretationForScore($standardScore, $ageMonths);

        return TestStandardScore::updateOrCreate(
            ['test_id' => $test->test_id],
            [
                'scale_version_id' => $scaleVersionId,
                'sum_scaled_scores' => $sumScaled,
                'standard_score' => $standardScore,
                'interpretation' => $interpretation,
            ]
        );
    }

    /**
     * Recompute period_summary_scores for a given assessment period.
     */
    public function recomputePeriodSummaryForPeriodId(int $periodId): ?PeriodSummaryScore
    {
        $period = AssessmentPeriod::with(['student', 'tests.standardScore', 'tests.observer'])
            ->find($periodId);

        if (!$period) {
            return null;
        }

        return $this->recomputePeriodSummary($period);
    }

    public function recomputePeriodSummary(AssessmentPeriod $period): ?PeriodSummaryScore
    {
        $student = $period->student;
        if (!$student instanceof Student) {
            $student = Student::find($period->student_id);
        }

        if (!$student || !$student->date_of_birth) {
            return null;
        }

        $tests = $period->tests()->with(['standardScore', 'observer'])->get();
        if ($tests->isEmpty()) {
            return null;
        }

        $ageMonthsAtPeriodEnd = $student->date_of_birth
            ? Carbon::parse($student->date_of_birth)->diffInMonths(Carbon::parse($period->end_date))
            : 0;

        $teacherTests = $tests->filter(function (Test $t) {
            return $t->observer && $t->observer->role === 'teacher' && in_array($t->status, ['completed', 'finalized'], true);
        });

        $familyTests = $tests->filter(function (Test $t) {
            return $t->observer && $t->observer->role === 'family' && in_array($t->status, ['completed', 'finalized'], true);
        });

        // If any relevant test in this period is completed or finalized, mark the period as completed.
        $hasCompletedOrFinalized = $tests->contains(function (Test $t) {
            return in_array($t->status, ['completed', 'finalized'], true);
        });

        if ($hasCompletedOrFinalized && $period->status !== 'completed') {
            $period->status = 'completed';
            $period->save();
        }

        // Ensure all relevant tests have standard scores.
        foreach ($teacherTests->merge($familyTests) as $test) {
            if (!$test->standardScore) {
                $this->scoreTest($test);
                $test->load('standardScore');
            }
        }

        $teacherScores = $teacherTests
            ->pluck('standardScore')
            ->filter()
            ->pluck('standard_score')
            ->map(fn ($v) => (float) $v);

        $teacherAvg = $teacherScores->isNotEmpty() ? $teacherScores->avg() : null;

        // Determine teacher discrepancy based on spread among teacher scores.
        $teacherDiscrepancy = 'none';
        if ($teacherScores->count() > 1) {
            $spread = $teacherScores->max() - $teacherScores->min();
            if ($spread >= 15) {
                $teacherDiscrepancy = 'major';
            } elseif ($spread >= 5) {
                $teacherDiscrepancy = 'minor';
            }
        }

        // Choose the family test to represent the family score: latest completed/finalized by date.
        $familyTestForSummary = $familyTests
            ->filter(fn (Test $t) => in_array($t->status, ['completed', 'finalized'], true))
            ->sortByDesc('test_date')
            ->first();

        $familyScore = null;
        if ($familyTestForSummary && $familyTestForSummary->standardScore) {
            $familyScore = (float) $familyTestForSummary->standardScore->standard_score;
        }

        // Teacher-family discrepancy
        $teacherFamilyDiscrepancy = 'none';
        if ($teacherAvg !== null && $familyScore !== null) {
            $diff = abs($teacherAvg - $familyScore);
            if ($diff >= 15) {
                $teacherFamilyDiscrepancy = 'major';
            } elseif ($diff >= 5) {
                $teacherFamilyDiscrepancy = 'minor';
            }
        }

        // Final standard score with 70/30 weighting when both present.
        $finalStandard = null;
        if ($teacherAvg !== null && $familyScore !== null) {
            $finalStandard = ($teacherAvg * 0.7) + ($familyScore * 0.3);
        } elseif ($teacherAvg !== null) {
            $finalStandard = $teacherAvg;
        } elseif ($familyScore !== null) {
            $finalStandard = $familyScore;
        }

        $finalInterpretation = $finalStandard !== null
            ? $this->interpretationForScore((float) $finalStandard, $ageMonthsAtPeriodEnd)
            : null;

        return PeriodSummaryScore::updateOrCreate(
            ['period_id' => $period->period_id],
            [
                'teachers_standard_score_avg' => $teacherAvg !== null ? round($teacherAvg, 2) : null,
                'family_standard_score' => $familyScore !== null ? (int) round($familyScore) : null,
                'final_standard_score' => $finalStandard !== null ? round($finalStandard, 2) : null,
                'final_interpretation' => $finalInterpretation,
                'teacher_discrepancy' => $teacherDiscrepancy,
                'teacher_family_discrepancy' => $teacherFamilyDiscrepancy,
            ]
        );
    }

    /**
     * Convenience: score a test and recompute its period summary.
     */
    public function scoreTestAndRecompute(Test $test): void
    {
        $this->scoreTest($test);

        if ($test->assessmentPeriod instanceof AssessmentPeriod) {
            $this->recomputePeriodSummary($test->assessmentPeriod);
        } elseif ($test->period_id) {
            $this->recomputePeriodSummaryForPeriodId($test->period_id);
        }
    }

    /**
     * Map a standard score + age (months) to interpretation.
     */
    private function interpretationForScore(float $standardScore, int $ageMonths): string
    {
        // Age buckets based on migration comments (3.1–4.0, 4.1–5.0, 5.1–5.11 years).
        // For now, thresholds are uniform across ages as in TestController.
        if ($ageMonths >= 61 && $ageMonths <= 71) {
            if ($standardScore >= 85) {
                return 'Advanced Development';
            }
            if ($standardScore >= 70) {
                return 'Average Development';
            }
            return 'Re-Test After 6 months';
        }

        if ($ageMonths >= 49 && $ageMonths <= 60) {
            if ($standardScore >= 85) {
                return 'Advanced Development';
            }
            if ($standardScore >= 70) {
                return 'Average Development';
            }
            return 'Re-Test After 6 months';
        }

        if ($ageMonths >= 37 && $ageMonths <= 48) {
            if ($standardScore >= 85) {
                return 'Advanced Development';
            }
            if ($standardScore >= 70) {
                return 'Average Development';
            }
            return 'Re-Test After 6 months';
        }

        // Default for other ages
        if ($standardScore >= 85) {
            return 'Advanced Development';
        }
        if ($standardScore >= 70) {
            return 'Average Development';
        }
        return 'Re-Test After 6 months';
    }
}
