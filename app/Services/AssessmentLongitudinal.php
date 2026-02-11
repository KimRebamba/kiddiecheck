<?php

namespace App\Services;

use App\Models\Student;
use App\Models\AssessmentPeriod;
use App\Services\EccdScoring;

class AssessmentLongitudinal
{
    /**
     * Compute longitudinal interpretation across assessment periods for a student.
     * Returns per-period aggregates and combined averages where applicable.
     */
    public static function summarize(Student $student)
    {
        $domains = \App\Models\Domain::all();
        $periods = $student->assessmentPeriods()->orderBy('index')->get();

        $perPeriod = [];
        foreach ($periods as $p) {
            $tests = $student->tests()->where('assessment_period_id', $p->id)->finalized()->with(['scores','observer'])->get();
            $aggByRole = EccdScoring::aggregateByRole($tests, $domains);
            $perPeriod[$p->index] = [
                'period' => $p,
                'teacher' => $aggByRole['teacher'],
                'family' => $aggByRole['family'],
                'combined' => $aggByRole['combined'],
                'familyOnly' => ($tests->filter(fn($t) => $t->observer?->role === 'family')->isNotEmpty()
                                 && $tests->filter(fn($t) => $t->observer?->role === 'teacher')->isEmpty()),
                'hasData' => $tests->isNotEmpty(),
            ];
        }

        // Build longitudinal averages using only completed periods
        $completedCombined = collect($perPeriod)->filter(fn($x) => $x['hasData'])->map(fn($x) => $x['combined']);

        $avgOf = function ($indexes) use ($perPeriod, $domains) {
            $testsAgg = collect();
            foreach ($indexes as $i) {
                if (!isset($perPeriod[$i])) { continue; }
                $combined = $perPeriod[$i]['combined'];
                if (!$combined) { continue; }
                $testsAgg->push($combined);
            }
            if ($testsAgg->isEmpty()) { return null; }
            // Average domain scaled scores across selected periods
            $domainIds = $domains->pluck('id');
            $perDomain = [];
            foreach ($domainIds as $did) {
                $vals = $testsAgg->map(fn($a) => $a['domains'][$did] ?? null)->filter()->values();
                $perDomain[$did] = $vals->isNotEmpty() ? round($vals->avg(), 2) : null;
            }
            $sum = collect($perDomain)->filter()->sum();
            $std = EccdScoring::deriveStandardScore($sum, $domains->count());
            return [ 'domains' => $perDomain, 'sumScaled' => round($sum,2), 'standardScore' => $std ];
        };

        $longitudinal = [
            1 => $perPeriod[1]['combined'] ?? null,
            2 => $avgOf([1,2]),
            3 => $avgOf([1,2,3]),
        ];

        return [ 'perPeriod' => $perPeriod, 'longitudinal' => $longitudinal ];
    }
}
