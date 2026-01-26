<?php

namespace App\Services;

use Illuminate\Support\Arr;
use App\Models\Domain;

class EccdScoring
{
    /**
     * Convert a percentage (0–100) to scaled score (1–19).
     */
    public static function percentageToScaled(float $percentage): int
    {
        $min = (int) config('eccd.scaled_score_min', 1);
        $max = (int) config('eccd.scaled_score_max', 19);
        $p = max(0.0, min(100.0, $percentage));
        $scaled = (int) round($min + ($p / 100.0) * ($max - $min));
        return max($min, min($max, $scaled));
    }

    /**
     * Derive overall standard score (70–160) from sum of scaled domain scores.
     * Domain count can be provided; if null, it will be looked up.
     */
    public static function deriveStandardScore(float $sumScaled, ?int $domainCount = null): ?int
    {
        // Prefer explicit lookup table if provided
        $lookup = config('eccd.standard_score_lookup', []);
        $rounded = (int) round($sumScaled);
        if (isset($lookup[$rounded])) {
            return (int) $lookup[$rounded];
        }

        $minScaled = (int) config('eccd.scaled_score_min', 1);
        $maxScaled = (int) config('eccd.scaled_score_max', 19);
        $minStd = (int) config('eccd.standard_score_min', 70);
        $maxStd = (int) config('eccd.standard_score_max', 160);

        $domains = $domainCount ?? Domain::count();
        if ($domains <= 0) { return null; }

        $minSum = $minScaled * $domains;
        $maxSum = $maxScaled * $domains;
        if ($maxSum <= $minSum) { return null; }

        $clamped = max($minSum, min($maxSum, $sumScaled));
        $ratio = ($clamped - $minSum) / ($maxSum - $minSum);
        $std = (int) round($minStd + $ratio * ($maxStd - $minStd));
        return max($minStd, min($maxStd, $std));
    }

    /**
     * Classify a scaled score per domain.
     */
    public static function classifyScaled(int $scaled): ?string
    {
        foreach (config('eccd.scaled_score_categories', []) as $c) {
            if ($scaled >= ($c['min'] ?? 0) && $scaled <= ($c['max'] ?? 0)) {
                return (string) $c['label'];
            }
        }
        return null;
    }

    /**
     * Classify a standard score overall.
     */
    public static function classifyStandard(int $standard): ?string
    {
        foreach (config('eccd.standard_score_categories', []) as $c) {
            if ($standard >= ($c['min'] ?? 0) && $standard <= ($c['max'] ?? 0)) {
                return (string) $c['label'];
            }
        }
        return null;
    }

    /**
     * Build per-test summary from loaded relations.
     * @param \Illuminate\Support\Collection $tests
     * @param \Illuminate\Support\Collection $domains
     * @return array [testId => ['sumScaled' => float, 'standardScore' => int|null, 'domains' => [domainId => ['raw' => float|null, 'scaled' => float|null]]]]
     */
    public static function summarize($tests, $domains): array
    {
        $domainIds = $domains->pluck('id');
        $out = [];
        foreach ($tests as $t) {
            $perDomain = [];
            $sum = 0.0;
            foreach ($domainIds as $did) {
                $score = $t->scores->firstWhere('domain_id', $did);
                $raw = $score?->raw_score;
                $scaledRaw = $score?->scaled_score;
                $scaled = null;
                if ($scaledRaw !== null) {
                    $max = (int) config('eccd.scaled_score_max', 19);
                    $scaled = $scaledRaw > $max ? self::percentageToScaled((float) $scaledRaw) : (float) $scaledRaw;
                    $sum += (float) $scaled;
                }
                $perDomain[$did] = ['raw' => $raw, 'scaled' => $scaled];
            }
            $standard = self::deriveStandardScore($sum, $domains->count());
            $out[$t->id] = [
                'sumScaled' => round($sum, 2),
                'standardScore' => $standard,
                'domains' => $perDomain,
            ];
        }
        return $out;
    }

    /**
     * Aggregate across multiple tests: return averaged domain scaled scores (optionally incl. family).
     * @param \Illuminate\Support\Collection $tests
     * @param \Illuminate\Support\Collection $domains
     * @return array ['sumScaled' => float, 'standardScore' => int|null, 'domains' => [domainId => float]]
     */
    public static function aggregate($tests, $domains): array
    {
        $includeFamily = (bool) data_get(config('eccd.aggregation'), 'include_family', true);
        $familyWeight = (float) data_get(config('eccd.aggregation'), 'family_weight', 1.0);
        $mode = (string) data_get(config('eccd.aggregation'), 'mode', 'average');

        $teacher = $tests->filter(fn($t) => $t->observer?->role === 'teacher');
        $family = $includeFamily ? $tests->filter(fn($t) => $t->observer?->role === 'family') : collect();

        $domainIds = $domains->pluck('id');
        $perDomain = [];
        foreach ($domainIds as $did) {
            $tVals = $teacher->map(fn($t) => optional($t->scores->firstWhere('domain_id', $did))->scaled_score)->filter()->values();
            $fVals = $family->map(fn($t) => optional($t->scores->firstWhere('domain_id', $did))->scaled_score)->filter()->values();

            $val = null;
            if ($mode === 'average') {
                $tAvg = $tVals->avg();
                $fAvg = $fVals->avg();
                if ($tAvg !== null && $fAvg !== null) {
                    $val = ($tAvg + ($familyWeight * $fAvg)) / (1.0 + $familyWeight);
                } elseif ($tAvg !== null) {
                    $val = $tAvg;
                } elseif ($fAvg !== null) {
                    $val = $fAvg; // family only if no teacher
                }
            } elseif ($mode === 'median') {
                $vals = $tVals->merge($fVals);
                $count = $vals->count();
                if ($count > 0) {
                    $sorted = $vals->sort()->values();
                    $mid = intdiv($count, 2);
                    $val = $count % 2 ? $sorted[$mid] : (($sorted[$mid - 1] + $sorted[$mid]) / 2);
                }
            } elseif ($mode === 'latest') {
                $latest = $tests->sortByDesc('test_date')->first();
                $val = optional($latest?->scores->firstWhere('domain_id', $did))->scaled_score;
            }

            $perDomain[$did] = $val !== null ? round((float) $val, 2) : null;
        }

        $sum = collect($perDomain)->filter()->sum();
        $std = self::deriveStandardScore($sum, $domains->count());
        return [
            'sumScaled' => round((float) $sum, 2),
            'standardScore' => $std,
            'domains' => $perDomain,
        ];
    }

    /**
     * Aggregate separately for teacher and family groups, plus combined.
     * Returns array with keys: teacher, family, combined.
     */
    public static function aggregateByRole($tests, $domains): array
    {
        $teacher = $tests->filter(fn($t) => $t->observer?->role === 'teacher');
        $family = $tests->filter(fn($t) => $t->observer?->role === 'family');
        return [
            'teacher' => self::aggregate($teacher, $domains),
            'family' => self::aggregate($family, $domains),
            'combined' => self::aggregate($teacher->merge($family), $domains),
        ];
    }

    /**
     * Analyze discrepancies between teacher and family aggregates.
     * Returns per-domain differences and overall standard difference.
     */
    public static function analyzeDiscrepancies(array $teacherAgg, array $familyAgg, $domains): array
    {
        $domainThreshold = (float) data_get(config('eccd.discrepancy'), 'domain_delta_threshold', 3);
        $stdThreshold = (float) data_get(config('eccd.discrepancy'), 'standard_delta_threshold', 10);

        $perDomain = [];
        foreach ($domains as $d) {
            $t = data_get($teacherAgg, [ 'domains', $d->id ]);
            $f = data_get($familyAgg, [ 'domains', $d->id ]);
            if ($t === null || $f === null) {
                $perDomain[$d->id] = [
                    'domain' => $d->name,
                    'teacher' => $t,
                    'family' => $f,
                    'delta' => null,
                    'flag' => false,
                    'direction' => null,
                ];
                continue;
            }
            $delta = round(abs((float)$t - (float)$f), 2);
            $direction = null;
            if ($t < $f) { $direction = 'teacher_lower'; }
            if ($t > $f) { $direction = 'teacher_higher'; }
            $perDomain[$d->id] = [
                'domain' => $d->name,
                'teacher' => round((float)$t, 2),
                'family' => round((float)$f, 2),
                'delta' => $delta,
                'flag' => $delta >= $domainThreshold,
                'direction' => $direction,
            ];
        }

        $tStd = $teacherAgg['standardScore'] ?? null;
        $fStd = $familyAgg['standardScore'] ?? null;
        $overall = [
            'teacher' => $tStd,
            'family' => $fStd,
            'delta' => ($tStd !== null && $fStd !== null) ? abs((float)$tStd - (float)$fStd) : null,
            'flag' => ($tStd !== null && $fStd !== null) ? (abs((float)$tStd - (float)$fStd) >= $stdThreshold) : false,
            'direction' => ($tStd !== null && $fStd !== null) ? ($tStd < $fStd ? 'teacher_lower' : ($tStd > $fStd ? 'teacher_higher' : null)) : null,
        ];

        return [ 'domains' => $perDomain, 'overall' => $overall ];
    }
}
