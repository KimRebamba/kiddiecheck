<?php

return [
    // Scaled score per domain is on a 1–19 scale.
    'scaled_score_min' => 1,
    'scaled_score_max' => 19,

    // Standard score overall (derived) is on a 70–160 scale.
    'standard_score_min' => 70,
    'standard_score_max' => 160,

    // Classification for scaled scores (per domain).
    // 14–19: Advanced, 7–13: Average, 1–6: Re-test in 3–6 months
    'scaled_score_categories' => [
        ['min' => 14, 'max' => 19, 'label' => 'Suggests Advanced'],
        ['min' => 7,  'max' => 13, 'label' => 'Average'],
        ['min' => 1,  'max' => 6,  'label' => 'Re-test 3–6 months'],
    ],

    // Classification for standard scores (overall).
    // >120 to 160: Advanced Development; >70 to 120: Average Development; <70: Re-test after 3–6 months
    'standard_score_categories' => [
        ['min' => 121, 'max' => 160, 'label' => 'Suggests Advanced Development'],
        ['min' => 70,  'max' => 120, 'label' => 'Average Development'],
        ['min' => 0,   'max' => 69,  'label' => 'Re-test after 3–6 months'],
    ],

    // Optional explicit lookup from Sum of Scaled Scores → Standard Score
    // Based on the Child's Record 2 table in reference-scale.html.
    'standard_score_lookup' => [
        29 => 37, 30 => 38, 31 => 40, 32 => 41, 33 => 43, 34 => 44,
        35 => 45, 36 => 47, 37 => 48, 38 => 50, 39 => 51, 40 => 53,
        41 => 54, 42 => 56, 43 => 57, 44 => 59, 45 => 60, 46 => 62,
        47 => 63, 48 => 65, 49 => 66, 50 => 67, 51 => 69, 52 => 70,
        53 => 72, 54 => 73, 55 => 75, 56 => 76, 57 => 78, 58 => 79,
        59 => 81, 60 => 82, 61 => 84, 62 => 85, 63 => 86,
        64 => 88, 65 => 89, 66 => 91, 67 => 92, 68 => 94, 69 => 95,
        70 => 97, 71 => 98, 72 => 100, 73 => 101, 74 => 103, 75 => 104,
        76 => 105, 77 => 107, 78 => 108, 79 => 110, 80 => 111, 81 => 113,
        82 => 114, 83 => 116, 84 => 117, 85 => 119, 86 => 120, 87 => 122,
        88 => 123, 89 => 124, 90 => 126, 91 => 127, 92 => 129, 93 => 130,
        94 => 132, 95 => 133, 96 => 135, 97 => 136, 98 => 138,
    ],

    // Aggregation behavior across multiple tests for a student.
    'aggregation' => [
        // average | median | latest
        'mode' => 'average',
        // Include optional family test(s) when aggregating (if present)
        'include_family' => true,
        // Weight for family tests compared to teacher tests (1.0 = equal)
        'family_weight' => 1.0,
    ],

    // Discrepancy detection thresholds between teacher and family assessments
    'discrepancy' => [
        // Flag per-domain if absolute difference in scaled score >= threshold
        'domain_delta_threshold' => 3, // scaled points (1–19)
        // Flag overall if absolute difference in derived standard score >= threshold
        'standard_delta_threshold' => 10, // standard score points (70–160)
    ],

    // Assessment period configuration
    'period' => [
        // Months per assessment window
        'months' => 6,
        // Grace period in days for teachers beyond window end
        'teacher_grace_days' => 7,
    ],

    // Assessment period scheduling settings
    'period' => [
        // Months per assessment window
        'months' => 6,
        // Grace period (days) after window end for teachers only
        'teacher_grace_days' => 7,
    ],
];
