<?php

namespace App\Services;

use Illuminate\Support\Arr;

class EccdScoring
{
    public static function deriveStandardScore(float $sumScaled): ?int
    {
        $bands = config('eccd.standard_score_bands', []);
        foreach ($bands as $b) {
            if ($sumScaled >= ($b['min'] ?? 0) && $sumScaled <= ($b['max'] ?? 0)) {
                return (int) ($b['score'] ?? null);
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
                $scaled = $score?->scaled_score;
                if ($scaled !== null) { $sum += (float) $scaled; }
                $perDomain[$did] = ['raw' => $raw, 'scaled' => $scaled];
            }
            $standard = self::deriveStandardScore($sum);
            $out[$t->id] = [
                'sumScaled' => round($sum, 2),
                'standardScore' => $standard,
                'domains' => $perDomain,
            ];
        }
        return $out;
    }
}
