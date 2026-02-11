<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ECCD Configuration
    |--------------------------------------------------------------------------
    |
    | This file is for storing the configuration settings for the ECCD application.
    |
    */

    // ── Score Ranges ──────────────────────────────────────────────────────────

    //Per domain scaled score range (1–19 scale)
    'scaled_score_min' => 1,
    'scaled_score_max' => 19,

    // Overall standard score range 
    'standard_score_min' => 20,
    'standard_score_max' => 160,

    // ── Score Interpretation Categories ───────────────────────────────────────

    // Per-domain scaled score interpretation (1–19 scale)
    'scaled_score_categories' => [
        ['min' => 14, 'max' => 19, 'label' => 'Suggests Advanced Development'],
        ['min' => 7,  'max' => 13, 'label' => 'Average Development'],
        ['min' => 1,  'max' => 6,  'label' => 'Re-test after 6 months'],
    ],

    // Overall standard score interpretation (derived from sum of scaled scores)
    'standard_score_categories' => [
        ['min' => 121, 'max' => 160, 'label' => 'Suggests Advanced Development'],
        ['min' => 71, 'max' => 120, 'label' => 'Average Development'],
        ['min' => 0,   'max' => 70,  'label' => 'Re-test after 6 months'],
    ],

    // Note: Standard score lookup (sum of scaled → standard score)
    // is now stored in the database table `standard_score_scales`
    // and seeded by Database\Seeders\Eccd2004Seeder.

    // ── Aggregation Behavior ──────────────────────────────────────────────────

    'aggregation' => [
        // Weighted average: 70% teachers, 30% family (steps 8–10 of flow)
        'teacher_weight' => 0.7,
        'family_weight'  => 0.3,
    ],

    // ── Discrepancy Detection Thresholds ─────────────────────────────────────

    // Used in steps 7 and 9 of the flow
    'discrepancy' => [
        // Absolute difference in standard score to classify discrepancy level
        'minor_threshold' => 10, // >= 10 points apart → Minor
        'major_threshold' => 20, // >= 20 points apart → Major
                                 //  < 10 points apart → None
    ],

    // ── Assessment Period ─────────────────────────────────────────────────────

    'period' => [
        'months' => 6, // each assessment window spans 6 months
    ],
];