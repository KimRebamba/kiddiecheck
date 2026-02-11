<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Eccd2004Seeder extends Seeder
{
    public function run(): void
    {
        // Create or get scale version
        $scaleVersionId = DB::table('scale_versions')
            ->where('name', 'ECCD 2004')
            ->value('scale_version_id');

        if (!$scaleVersionId) {
            $scaleVersionId = DB::table('scale_versions')->insertGetId([
                'name' => 'ECCD 2004',
                'description' => 'Child\'s Record 2 — ECCD Checklist, First Printing January 2004, Manila, Philippines',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Domain scaled score lookup rows 
        $domainScaled = [
            // Ages 3.1 – 4.0 years (37–48 months)
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Gross Motor',          'scaled' => 1,  'raw_min' => 0,  'raw_max' => 3],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Gross Motor',          'scaled' => 2,  'raw_min' => 4,  'raw_max' => 4],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Gross Motor',          'scaled' => 3,  'raw_min' => 5,  'raw_max' => 5],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Gross Motor',          'scaled' => 5,  'raw_min' => 6,  'raw_max' => 6],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Gross Motor',          'scaled' => 6,  'raw_min' => 7,  'raw_max' => 7],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Gross Motor',          'scaled' => 7,  'raw_min' => 8,  'raw_max' => 8],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Gross Motor',          'scaled' => 8,  'raw_min' => 9,  'raw_max' => 9],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Gross Motor',          'scaled' => 10, 'raw_min' => 10, 'raw_max' => 10],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Gross Motor',          'scaled' => 11, 'raw_min' => 11, 'raw_max' => 11],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Gross Motor',          'scaled' => 12, 'raw_min' => 12, 'raw_max' => 12],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Gross Motor',          'scaled' => 14, 'raw_min' => 13, 'raw_max' => 13],

            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Fine Motor',           'scaled' => 2,  'raw_min' => 0,  'raw_max' => 3],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Fine Motor',           'scaled' => 4,  'raw_min' => 4,  'raw_max' => 4],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Fine Motor',           'scaled' => 5,  'raw_min' => 5,  'raw_max' => 5],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Fine Motor',           'scaled' => 7,  'raw_min' => 6,  'raw_max' => 6],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Fine Motor',           'scaled' => 9,  'raw_min' => 7,  'raw_max' => 7],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Fine Motor',           'scaled' => 10, 'raw_min' => 8,  'raw_max' => 8],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Fine Motor',           'scaled' => 12, 'raw_min' => 9,  'raw_max' => 9],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Fine Motor',           'scaled' => 14, 'raw_min' => 10, 'raw_max' => 10],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Fine Motor',           'scaled' => 15, 'raw_min' => 11, 'raw_max' => 11],

            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Self-Help',            'scaled' => 1,  'raw_min' => 0,  'raw_max' => 9],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Self-Help',            'scaled' => 2,  'raw_min' => 10, 'raw_max' => 10],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Self-Help',            'scaled' => 3,  'raw_min' => 11, 'raw_max' => 11],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Self-Help',            'scaled' => 4,  'raw_min' => 12, 'raw_max' => 12],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Self-Help',            'scaled' => 5,  'raw_min' => 13, 'raw_max' => 14],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Self-Help',            'scaled' => 6,  'raw_min' => 15, 'raw_max' => 15],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Self-Help',            'scaled' => 7,  'raw_min' => 16, 'raw_max' => 16],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Self-Help',            'scaled' => 8,  'raw_min' => 17, 'raw_max' => 17],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Self-Help',            'scaled' => 9,  'raw_min' => 18, 'raw_max' => 19],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Self-Help',            'scaled' => 10, 'raw_min' => 20, 'raw_max' => 20],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Self-Help',            'scaled' => 11, 'raw_min' => 21, 'raw_max' => 21],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Self-Help',            'scaled' => 12, 'raw_min' => 22, 'raw_max' => 22],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Self-Help',            'scaled' => 13, 'raw_min' => 23, 'raw_max' => 24],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Self-Help',            'scaled' => 14, 'raw_min' => 25, 'raw_max' => 25],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Self-Help',            'scaled' => 15, 'raw_min' => 26, 'raw_max' => 26],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Self-Help',            'scaled' => 16, 'raw_min' => 27, 'raw_max' => 27],

            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Receptive Language',   'scaled' => 3,  'raw_min' => 0,  'raw_max' => 1],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Receptive Language',   'scaled' => 5,  'raw_min' => 2,  'raw_max' => 2],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Receptive Language',   'scaled' => 7,  'raw_min' => 3,  'raw_max' => 3],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Receptive Language',   'scaled' => 10, 'raw_min' => 4,  'raw_max' => 4],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Receptive Language',   'scaled' => 12, 'raw_min' => 5,  'raw_max' => 5],

            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Expressive Language',  'scaled' => 1,  'raw_min' => 0,  'raw_max' => 2],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Expressive Language',  'scaled' => 3,  'raw_min' => 3,  'raw_max' => 3],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Expressive Language',  'scaled' => 4,  'raw_min' => 4,  'raw_max' => 4],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Expressive Language',  'scaled' => 6,  'raw_min' => 5,  'raw_max' => 5],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Expressive Language',  'scaled' => 8,  'raw_min' => 6,  'raw_max' => 6],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Expressive Language',  'scaled' => 10, 'raw_min' => 7,  'raw_max' => 7],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Expressive Language',  'scaled' => 12, 'raw_min' => 8,  'raw_max' => 8],

            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Cognitive',            'scaled' => 3,  'raw_min' => 0,  'raw_max' => 0],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Cognitive',            'scaled' => 4,  'raw_min' => 1,  'raw_max' => 1],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Cognitive',            'scaled' => 5,  'raw_min' => 2,  'raw_max' => 3],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Cognitive',            'scaled' => 6,  'raw_min' => 4,  'raw_max' => 4],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Cognitive',            'scaled' => 7,  'raw_min' => 5,  'raw_max' => 5],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Cognitive',            'scaled' => 8,  'raw_min' => 6,  'raw_max' => 6],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Cognitive',            'scaled' => 9,  'raw_min' => 7,  'raw_max' => 7],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Cognitive',            'scaled' => 10, 'raw_min' => 8,  'raw_max' => 9],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Cognitive',            'scaled' => 11, 'raw_min' => 10, 'raw_max' => 10],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Cognitive',            'scaled' => 12, 'raw_min' => 11, 'raw_max' => 11],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Cognitive',            'scaled' => 13, 'raw_min' => 12, 'raw_max' => 12],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Cognitive',            'scaled' => 14, 'raw_min' => 13, 'raw_max' => 14],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Cognitive',            'scaled' => 15, 'raw_min' => 15, 'raw_max' => 15],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Cognitive',            'scaled' => 16, 'raw_min' => 16, 'raw_max' => 16],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Cognitive',            'scaled' => 17, 'raw_min' => 17, 'raw_max' => 17],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Cognitive',            'scaled' => 18, 'raw_min' => 18, 'raw_max' => 18],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Cognitive',            'scaled' => 19, 'raw_min' => 19, 'raw_max' => 21],

            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Social-Emotional',     'scaled' => 1,  'raw_min' => 0,  'raw_max' => 9],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Social-Emotional',     'scaled' => 2,  'raw_min' => 10, 'raw_max' => 11],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Social-Emotional',     'scaled' => 3,  'raw_min' => 12, 'raw_max' => 12],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Social-Emotional',     'scaled' => 4,  'raw_min' => 13, 'raw_max' => 13],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Social-Emotional',     'scaled' => 5,  'raw_min' => 14, 'raw_max' => 14],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Social-Emotional',     'scaled' => 6,  'raw_min' => 15, 'raw_max' => 15],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Social-Emotional',     'scaled' => 7,  'raw_min' => 16, 'raw_max' => 16],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Social-Emotional',     'scaled' => 8,  'raw_min' => 17, 'raw_max' => 18],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Social-Emotional',     'scaled' => 9,  'raw_min' => 19, 'raw_max' => 19],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Social-Emotional',     'scaled' => 10, 'raw_min' => 20, 'raw_max' => 20],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Social-Emotional',     'scaled' => 11, 'raw_min' => 21, 'raw_max' => 21],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Social-Emotional',     'scaled' => 12, 'raw_min' => 22, 'raw_max' => 22],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Social-Emotional',     'scaled' => 13, 'raw_min' => 23, 'raw_max' => 23],
            ['age_min' => 37, 'age_max' => 48, 'domain' => 'Social-Emotional',     'scaled' => 14, 'raw_min' => 24, 'raw_max' => 24],

            // Ages 4.1 – 5.0 years (49–60 months)
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Gross Motor',          'scaled' => 1,  'raw_min' => 0,  'raw_max' => 5],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Gross Motor',          'scaled' => 2,  'raw_min' => 6,  'raw_max' => 6],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Gross Motor',          'scaled' => 4,  'raw_min' => 7,  'raw_max' => 7],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Gross Motor',          'scaled' => 5,  'raw_min' => 8,  'raw_max' => 8],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Gross Motor',          'scaled' => 7,  'raw_min' => 9,  'raw_max' => 9],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Gross Motor',          'scaled' => 8,  'raw_min' => 10, 'raw_max' => 10],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Gross Motor',          'scaled' => 10, 'raw_min' => 11, 'raw_max' => 11],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Gross Motor',          'scaled' => 11, 'raw_min' => 12, 'raw_max' => 12],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Gross Motor',          'scaled' => 13, 'raw_min' => 13, 'raw_max' => 13],

            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Fine Motor',           'scaled' => 1,  'raw_min' => 0,  'raw_max' => 3],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Fine Motor',           'scaled' => 2,  'raw_min' => 4,  'raw_max' => 4],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Fine Motor',           'scaled' => 4,  'raw_min' => 5,  'raw_max' => 5],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Fine Motor',           'scaled' => 5,  'raw_min' => 6,  'raw_max' => 6],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Fine Motor',           'scaled' => 7,  'raw_min' => 7,  'raw_max' => 7],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Fine Motor',           'scaled' => 9,  'raw_min' => 8,  'raw_max' => 8],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Fine Motor',           'scaled' => 10, 'raw_min' => 9,  'raw_max' => 9],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Fine Motor',           'scaled' => 12, 'raw_min' => 10, 'raw_max' => 10],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Fine Motor',           'scaled' => 14, 'raw_min' => 11, 'raw_max' => 11],

            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Self-Help',            'scaled' => 1,  'raw_min' => 0,  'raw_max' => 15],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Self-Help',            'scaled' => 2,  'raw_min' => 16, 'raw_max' => 16],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Self-Help',            'scaled' => 3,  'raw_min' => 17, 'raw_max' => 17],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Self-Help',            'scaled' => 4,  'raw_min' => 18, 'raw_max' => 18],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Self-Help',            'scaled' => 5,  'raw_min' => 19, 'raw_max' => 19],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Self-Help',            'scaled' => 6,  'raw_min' => 20, 'raw_max' => 20],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Self-Help',            'scaled' => 8,  'raw_min' => 21, 'raw_max' => 21],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Self-Help',            'scaled' => 9,  'raw_min' => 22, 'raw_max' => 22],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Self-Help',            'scaled' => 10, 'raw_min' => 23, 'raw_max' => 23],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Self-Help',            'scaled' => 11, 'raw_min' => 24, 'raw_max' => 24],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Self-Help',            'scaled' => 12, 'raw_min' => 25, 'raw_max' => 25],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Self-Help',            'scaled' => 13, 'raw_min' => 26, 'raw_max' => 26],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Self-Help',            'scaled' => 14, 'raw_min' => 27, 'raw_max' => 27],

            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Receptive Language',   'scaled' => 1,  'raw_min' => 0,  'raw_max' => 1],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Receptive Language',   'scaled' => 3,  'raw_min' => 2,  'raw_max' => 2],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Receptive Language',   'scaled' => 6,  'raw_min' => 3,  'raw_max' => 3],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Receptive Language',   'scaled' => 9,  'raw_min' => 4,  'raw_max' => 4],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Receptive Language',   'scaled' => 11, 'raw_min' => 5,  'raw_max' => 5],

            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Expressive Language',  'scaled' => 2,  'raw_min' => 0,  'raw_max' => 5],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Expressive Language',  'scaled' => 5,  'raw_min' => 6,  'raw_max' => 6],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Expressive Language',  'scaled' => 8,  'raw_min' => 7,  'raw_max' => 7],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Expressive Language',  'scaled' => 11, 'raw_min' => 8,  'raw_max' => 8],

            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Cognitive',            'scaled' => 1,  'raw_min' => 0,  'raw_max' => 0],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Cognitive',            'scaled' => 2,  'raw_min' => 1,  'raw_max' => 1],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Cognitive',            'scaled' => 3,  'raw_min' => 2,  'raw_max' => 3],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Cognitive',            'scaled' => 4,  'raw_min' => 4,  'raw_max' => 4],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Cognitive',            'scaled' => 5,  'raw_min' => 6,  'raw_max' => 6],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Cognitive',            'scaled' => 6,  'raw_min' => 6,  'raw_max' => 7],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Cognitive',            'scaled' => 7,  'raw_min' => 8,  'raw_max' => 8],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Cognitive',            'scaled' => 8,  'raw_min' => 9,  'raw_max' => 10],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Cognitive',            'scaled' => 9,  'raw_min' => 11, 'raw_max' => 11],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Cognitive',            'scaled' => 10, 'raw_min' => 12, 'raw_max' => 12],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Cognitive',            'scaled' => 11, 'raw_min' => 13, 'raw_max' => 14],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Cognitive',            'scaled' => 12, 'raw_min' => 15, 'raw_max' => 15],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Cognitive',            'scaled' => 13, 'raw_min' => 16, 'raw_max' => 17],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Cognitive',            'scaled' => 14, 'raw_min' => 18, 'raw_max' => 18],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Cognitive',            'scaled' => 15, 'raw_min' => 19, 'raw_max' => 20],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Cognitive',            'scaled' => 16, 'raw_min' => 21, 'raw_max' => 21],

            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Social-Emotional',     'scaled' => 1,  'raw_min' => 0,  'raw_max' => 13],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Social-Emotional',     'scaled' => 2,  'raw_min' => 14, 'raw_max' => 14],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Social-Emotional',     'scaled' => 3,  'raw_min' => 15, 'raw_max' => 15],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Social-Emotional',     'scaled' => 4,  'raw_min' => 16, 'raw_max' => 16],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Social-Emotional',     'scaled' => 5,  'raw_min' => 17, 'raw_max' => 17],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Social-Emotional',     'scaled' => 7,  'raw_min' => 18, 'raw_max' => 18],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Social-Emotional',     'scaled' => 8,  'raw_min' => 19, 'raw_max' => 19],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Social-Emotional',     'scaled' => 9,  'raw_min' => 20, 'raw_max' => 20],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Social-Emotional',     'scaled' => 10, 'raw_min' => 21, 'raw_max' => 21],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Social-Emotional',     'scaled' => 11, 'raw_min' => 22, 'raw_max' => 22],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Social-Emotional',     'scaled' => 12, 'raw_min' => 23, 'raw_max' => 23],
            ['age_min' => 49, 'age_max' => 60, 'domain' => 'Social-Emotional',     'scaled' => 13, 'raw_min' => 24, 'raw_max' => 24],

            // Ages 5.1 – 5.11 years (61–71 months)
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Gross Motor',          'scaled' => 1,  'raw_min' => 0,  'raw_max' => 10],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Gross Motor',          'scaled' => 4,  'raw_min' => 11, 'raw_max' => 11],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Gross Motor',          'scaled' => 7,  'raw_min' => 12, 'raw_max' => 12],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Gross Motor',          'scaled' => 11, 'raw_min' => 13, 'raw_max' => 13],

            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Fine Motor',           'scaled' => 1,  'raw_min' => 0,  'raw_max' => 5],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Fine Motor',           'scaled' => 3,  'raw_min' => 6,  'raw_max' => 6],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Fine Motor',           'scaled' => 5,  'raw_min' => 7,  'raw_max' => 7],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Fine Motor',           'scaled' => 7,  'raw_min' => 8,  'raw_max' => 8],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Fine Motor',           'scaled' => 8,  'raw_min' => 9,  'raw_max' => 9],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Fine Motor',           'scaled' => 10, 'raw_min' => 10, 'raw_max' => 10],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Fine Motor',           'scaled' => 12, 'raw_min' => 11, 'raw_max' => 11],

            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Self-Help',            'scaled' => 2,  'raw_min' => 0,  'raw_max' => 19],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Self-Help',            'scaled' => 3,  'raw_min' => 20, 'raw_max' => 20],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Self-Help',            'scaled' => 4,  'raw_min' => 21, 'raw_max' => 21],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Self-Help',            'scaled' => 6,  'raw_min' => 22, 'raw_max' => 22],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Self-Help',            'scaled' => 7,  'raw_min' => 23, 'raw_max' => 23],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Self-Help',            'scaled' => 9,  'raw_min' => 24, 'raw_max' => 24],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Self-Help',            'scaled' => 10, 'raw_min' => 25, 'raw_max' => 25],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Self-Help',            'scaled' => 12, 'raw_min' => 26, 'raw_max' => 26],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Self-Help',            'scaled' => 13, 'raw_min' => 27, 'raw_max' => 27],

            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Receptive Language',   'scaled' => 1,  'raw_min' => 0,  'raw_max' => 2],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Receptive Language',   'scaled' => 4,  'raw_min' => 3,  'raw_max' => 3],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Receptive Language',   'scaled' => 8,  'raw_min' => 4,  'raw_max' => 4],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Receptive Language',   'scaled' => 11, 'raw_min' => 5,  'raw_max' => 5],

            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Expressive Language',  'scaled' => 5,  'raw_min' => 0,  'raw_max' => 7],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Expressive Language',  'scaled' => 11, 'raw_min' => 8,  'raw_max' => 8],

            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Cognitive',            'scaled' => 1,  'raw_min' => 0,  'raw_max' => 9],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Cognitive',            'scaled' => 2,  'raw_min' => 10, 'raw_max' => 10],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Cognitive',            'scaled' => 3,  'raw_min' => 11, 'raw_max' => 11],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Cognitive',            'scaled' => 4,  'raw_min' => 12, 'raw_max' => 12],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Cognitive',            'scaled' => 5,  'raw_min' => 13, 'raw_max' => 13],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Cognitive',            'scaled' => 6,  'raw_min' => 14, 'raw_max' => 14],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Cognitive',            'scaled' => 7,  'raw_min' => 15, 'raw_max' => 15],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Cognitive',            'scaled' => 8,  'raw_min' => 16, 'raw_max' => 16],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Cognitive',            'scaled' => 9,  'raw_min' => 17, 'raw_max' => 17],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Cognitive',            'scaled' => 10, 'raw_min' => 18, 'raw_max' => 18],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Cognitive',            'scaled' => 11, 'raw_min' => 19, 'raw_max' => 19],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Cognitive',            'scaled' => 12, 'raw_min' => 20, 'raw_max' => 20],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Cognitive',            'scaled' => 13, 'raw_min' => 21, 'raw_max' => 21],

            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Social-Emotional',     'scaled' => 1,  'raw_min' => 0,  'raw_max' => 15],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Social-Emotional',     'scaled' => 2,  'raw_min' => 16, 'raw_max' => 16],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Social-Emotional',     'scaled' => 3,  'raw_min' => 17, 'raw_max' => 17],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Social-Emotional',     'scaled' => 5,  'raw_min' => 18, 'raw_max' => 18],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Social-Emotional',     'scaled' => 6,  'raw_min' => 19, 'raw_max' => 19],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Social-Emotional',     'scaled' => 7,  'raw_min' => 20, 'raw_max' => 20],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Social-Emotional',     'scaled' => 9,  'raw_min' => 21, 'raw_max' => 21],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Social-Emotional',     'scaled' => 10, 'raw_min' => 22, 'raw_max' => 22],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Social-Emotional',     'scaled' => 11, 'raw_min' => 23, 'raw_max' => 23],
            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Social-Emotional',     'scaled' => 13, 'raw_min' => 24, 'raw_max' => 24],
        ];

        foreach ($domainScaled as $row) {
            $domainId = DB::table('domains')
                ->where('name', $row['domain'])
                ->value('domain_id');

            if (!$domainId) {
                $domainId = DB::table('domains')->insertGetId([
                    'name' => $row['domain'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('domain_scaled_scores')->updateOrInsert(
                [
                    'scale_version_id' => $scaleVersionId,
                    'domain_id' => $domainId,
                    'age_min_months' => $row['age_min'],
                    'raw_min' => $row['raw_min'],
                ],
                [
                    'age_max_months' => $row['age_max'],
                    'raw_max' => $row['raw_max'],
                    'scaled_score' => $row['scaled'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        // Standard score lookup rows (sum of scaled -> standard score)
        $standardLookup = [
            29 => 37,  30 => 38,  31 => 40,  32 => 41,  33 => 43,  34 => 44,
            35 => 45,  36 => 47,  37 => 48,  38 => 50,  39 => 51,  40 => 53,
            41 => 54,  42 => 56,  43 => 57,  44 => 59,  45 => 60,  46 => 62,
            47 => 63,  48 => 65,  49 => 66,  50 => 67,  51 => 69,  52 => 70,
            53 => 72,  54 => 73,  55 => 75,  56 => 76,  57 => 78,  58 => 79,
            59 => 81,  60 => 82,  61 => 84,  62 => 85,  63 => 86,  64 => 88,
            65 => 89,  66 => 91,  67 => 92,  68 => 94,  69 => 95,  70 => 97,
            71 => 98,  72 => 100, 73 => 101, 74 => 103, 75 => 104, 76 => 105,
            77 => 107, 78 => 108, 79 => 110, 80 => 111, 81 => 113, 82 => 114,
            83 => 116, 84 => 117, 85 => 119, 86 => 120, 87 => 122, 88 => 123,
            89 => 124, 90 => 126, 91 => 127, 92 => 129, 93 => 130, 94 => 132,
            95 => 133, 96 => 135, 97 => 136, 98 => 138,
        ];

        foreach ($standardLookup as $sumScaled => $standardScore) {
            DB::table('standard_score_scales')->updateOrInsert(
                [
                    'scale_version_id' => $scaleVersionId,
                    'sum_scaled_min' => $sumScaled,
                ],
                [
                    'sum_scaled_max' => $sumScaled,
                    'standard_score' => $standardScore,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
