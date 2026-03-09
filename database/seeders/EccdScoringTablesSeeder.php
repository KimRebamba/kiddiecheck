<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EccdScoringTablesSeeder extends Seeder
{
    public function run(): void
    {
        $scaleVersionId = DB::table('scale_versions')
            ->where('name', 'ECCD 2004')
            ->value('scale_version_id');

        if (!$scaleVersionId) {
            $scaleVersionId = DB::table('scale_versions')->insertGetId([
                'name' => 'ECCD 2004',
                'description' => 'ECCD Checklist 2004 Standard',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Get all domains
        $domains = DB::table('domains')->pluck('domain_id', 'name');

        // Clear existing data
        DB::table('domain_scaled_scores')->where('scale_version_id', $scaleVersionId)->delete();
        DB::table('standard_score_scales')->where('scale_version_id', $scaleVersionId)->delete();

        $now = now();

        // === CHILD'S RECORD 1: Ages 0-22 months ===

        // Ages 0-3 months
        $this->insertDomainScores($domains, $scaleVersionId, 0, 3, [
            1 => [0, 0],
            2 => [1, 1],
            3 => [2, 2],
            4 => [3, 3],
            5 => [4, 14],
        ]);

        // Ages 4-6 months
        $this->insertDomainScores($domains, $scaleVersionId, 4, 6, [
            1 => [0, 1],
            2 => [2, 2],
            3 => [3, 3],
            4 => [4, 4],
            5 => [5, 5],
            6 => [6, 6],
            7 => [7, 7],
            8 => [8, 8],
            9 => [9, 9],
            10 => [10, 10],
            11 => [11, 11],
            12 => [12, 22],
        ]);

        // Ages 7-9 months
        $this->insertDomainScores($domains, $scaleVersionId, 7, 9, [
            1 => [0, 5],
            2 => [6, 6],
            3 => [7, 7],
            4 => [8, 8],
            5 => [9, 9],
            6 => [10, 10],
            7 => [11, 11],
            8 => [12, 12],
            9 => [13, 13],
            10 => [14, 14],
            11 => [15, 15],
            12 => [16, 16],
            13 => [17, 17],
            14 => [18, 18],
            15 => [19, 22],
        ]);

        // Ages 10-12 months
        $this->insertDomainScores($domains, $scaleVersionId, 10, 12, [
            1 => [0, 2],
            2 => [3, 3],
            3 => [4, 4],
            4 => [5, 5],
            5 => [6, 6],
            6 => [7, 7],
            7 => [8, 8],
            8 => [9, 9],
            9 => [10, 10],
            10 => [11, 11],
            11 => [12, 12],
            12 => [13, 13],
            13 => [14, 14],
            14 => [15, 15],
            15 => [16, 16],
            16 => [17, 17],
            17 => [18, 18],
            18 => [19, 19],
            19 => [20, 22],
        ]);

        // === CHILD'S RECORD 2: Ages 3.1-5.11 years ===

        // Ages 3.1-4.0 years (37-48 months)
        $this->insertDomainScores($domains, $scaleVersionId, 37, 48, [
            1 => [0, 3],
            2 => [4, 4],
            3 => [5, 5],
            4 => [4, 4],
            5 => [6, 6],
            6 => [7, 7],
            7 => [8, 8],
            8 => [9, 9],
            9 => [7, 7],
            10 => [10, 10],
            11 => [21, 21],
            12 => [12, 12],
            13 => [23, 24],
            14 => [13, 13],
            15 => [11, 11],
            16 => [27, 27],
            17 => [17, 17],
            18 => [18, 18],
            19 => [19, 21],
        ]);

        // Ages 4.1-5.0 years (49-60 months)
        $this->insertDomainScores($domains, $scaleVersionId, 49, 60, [
            1 => [0, 5],
            2 => [6, 6],
            3 => [7, 7],
            4 => [8, 8],
            5 => [9, 9],
            6 => [10, 10],
            7 => [11, 11],
            8 => [12, 12],
            9 => [13, 13],
            10 => [14, 14],
            11 => [15, 15],
            12 => [16, 16],
            13 => [17, 17],
            14 => [18, 18],
            15 => [19, 19],
            16 => [20, 20],
            17 => [21, 21],
            18 => [22, 22],
            19 => [23, 27],
        ]);

        // Ages 5.1-5.11 years (61-71 months)
        $this->insertDomainScores($domains, $scaleVersionId, 61, 71, [
            1 => [0, 3],     // Raw 0-3 → Scaled 1
            2 => [4, 4],     // Raw 4 → Scaled 2 (filling gap)
            3 => [5, 7],     // Raw 5-7 → Scaled 3 (filling gap)  
            4 => [8, 8],     // Raw 8 → Scaled 4 (corrected)
            5 => [9, 10],    // Raw 9-10 → Scaled 5 (filling gap)
            6 => [11, 12],   // Raw 11-12 → Scaled 6 (filling gap)
            7 => [13, 13],   // Raw 13 → Scaled 7
            8 => [14, 16],   // Raw 14-16 → Scaled 8 (filling gap)
            9 => [17, 19],   // Raw 17-19 → Scaled 9 (filling gap)
            10 => [20, 21],  // Raw 20-21 → Scaled 10 (filling gap)
            11 => [22, 22],  // Raw 22 → Scaled 11
            12 => [23, 25],  // Raw 23-25 → Scaled 12 (filling gap)
            13 => [26, 27],  // Raw 26-27 → Scaled 13 (filling gap)
        ]);

        // === STANDARD SCORE SCALES ===
        
        // Standard Score Equivalent of Sum of Scaled Scores
        $standardScores = [
            // Add lower ranges for cases where sum is less than 21
            7 => 35, 8 => 36, 9 => 37, 10 => 38, 11 => 40, 12 => 41, 13 => 43, 14 => 44,
            15 => 45, 16 => 47, 17 => 48, 18 => 50, 19 => 51, 20 => 53,
            // Original mappings starting from 21
            21 => 39, 22 => 40, 23 => 42, 24 => 43, 25 => 44, 26 => 45, 27 => 46, 28 => 48,
            29 => 49, 30 => 50, 31 => 51, 32 => 53, 33 => 54, 34 => 55, 35 => 56, 36 => 57,
            37 => 59, 38 => 60, 39 => 61, 40 => 62, 41 => 64, 42 => 65, 43 => 66, 44 => 67,
            45 => 68, 46 => 70, 47 => 71, 48 => 72, 49 => 73, 50 => 75,
        ];

        // Continue the pattern up to 112 -> 150
        for ($sum = 51; $sum <= 112; $sum++) {
            if ($sum <= 56) {
                $standardScores[$sum] = 76 + ($sum - 51);
            } elseif ($sum <= 62) {
                $standardScores[$sum] = 82 + ($sum - 57);
            } elseif ($sum <= 68) {
                $standardScores[$sum] = 89 + ($sum - 63);
            } elseif ($sum <= 74) {
                $standardScores[$sum] = 96 + ($sum - 69);
            } elseif ($sum <= 80) {
                $standardScores[$sum] = 103 + ($sum - 75);
            } elseif ($sum <= 86) {
                $standardScores[$sum] = 110 + ($sum - 81);
            } elseif ($sum <= 92) {
                $standardScores[$sum] = 117 + ($sum - 87);
            } elseif ($sum <= 98) {
                $standardScores[$sum] = 124 + ($sum - 93);
            } elseif ($sum <= 104) {
                $standardScores[$sum] = 131 + ($sum - 99);
            } elseif ($sum <= 110) {
                $standardScores[$sum] = 138 + ($sum - 105);
            } else {
                $standardScores[$sum] = 145 + ($sum - 111);
            }
        }

        // Insert standard score scales
        foreach ($standardScores as $sumScaled => $standardScore) {
            DB::table('standard_score_scales')->insert([
                'scale_version_id' => $scaleVersionId,
                'sum_scaled_min' => $sumScaled,
                'sum_scaled_max' => $sumScaled,
                'standard_score' => $standardScore,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $this->command->info('ECCD scoring tables seeded successfully!');
    }

    private function insertDomainScores($domains, $scaleVersionId, $ageMin, $ageMax, $scaledScores)
    {
        $now = now();
        
        foreach ($domains as $domainName => $domainId) {
            foreach ($scaledScores as $scaledScore => $rawRange) {
                // Check if this exact entry already exists
                $exists = DB::table('domain_scaled_scores')
                    ->where('scale_version_id', $scaleVersionId)
                    ->where('domain_id', $domainId)
                    ->where('age_min_months', $ageMin)
                    ->where('raw_min', $rawRange[0])
                    ->exists();
                
                if (!$exists) {
                    DB::table('domain_scaled_scores')->insert([
                        'scale_version_id' => $scaleVersionId,
                        'domain_id' => $domainId,
                        'age_min_months' => $ageMin,
                        'age_max_months' => $ageMax,
                        'raw_min' => $rawRange[0],
                        'raw_max' => $rawRange[1],
                        'scaled_score' => $scaledScore,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }
}
