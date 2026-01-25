<?php

return [
    // Map sum of scaled scores to standard score bands.
    // TODO: Replace with official ECCD tables.
    'standard_score_bands' => [
        ['min' => 0,   'max' => 40,  'score' => 70],
        ['min' => 41,  'max' => 60,  'score' => 80],
        ['min' => 61,  'max' => 80,  'score' => 90],
        ['min' => 81,  'max' => 100, 'score' => 100],
        ['min' => 101, 'max' => 120, 'score' => 110],
        ['min' => 121, 'max' => 140, 'score' => 120],
    ],
];
