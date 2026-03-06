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

            ['age_min' => 61, 'age_max' => 71, 'domain' => 'Cognitive',            'scaled' => 13, 'raw_min' => 21, 'raw_max' => 21]

        ];



        // Create sections

        $sections = [

            ['name' => 'Section A', 'created_at' => now(), 'updated_at' => now()],

            ['name' => 'Section B', 'created_at' => now(), 'updated_at' => now()],

            ['name' => 'Section C', 'created_at' => now(), 'updated_at' => now()]

        ];

        

        $sectionIds = [];

        foreach ($sections as $section) {

            $sectionId = DB::table('sections')->insertGetId($section);

            $sectionIds[] = $sectionId;

        }

        

        // Create teachers (3 teachers for 3 sections)

        $teachers = [

            ['first_name' => 'Teacher A', 'last_name' => 'Teacher A', 'home_address' => 'Address A', 'phone_number' => '123-456-7890', 'hire_date' => '2024-01-01', 'created_at' => now(), 'updated_at' => now()],

            ['first_name' => 'Teacher B', 'last_name' => 'Teacher B', 'home_address' => 'Address B', 'phone_number' => '234-567-8901', 'hire_date' => '2024-02-01', 'created_at' => now(), 'updated_at' => now()],

            ['first_name' => 'Teacher C', 'last_name' => 'Teacher C', 'home_address' => 'Address C', 'phone_number' => '345-678-9012', 'hire_date' => '2024-03-01', 'created_at' => now(), 'updated_at' => now()],

        ];

        

        $teacherIds = [];

        foreach ($teachers as $teacher) {

            $teacherName = 'teacher_' . strtolower(str_replace(' ', '', $teacher['first_name']));

            $teacherId = DB::table('users')->insertGetId([

                'username' => $teacherName,

                'email' => $teacherName . '@gmail.com',

                'password' => bcrypt($teacherName),

                'role' => 'teacher',

                'created_at' => now(),

                'updated_at' => now()

            ]);

            

            // Create teacher record

            DB::table('teachers')->insertGetId([

                'user_id' => $teacherId,

                'first_name' => $teacher['first_name'],

                'last_name' => $teacher['last_name'],

                'home_address' => $teacher['home_address'],

                'phone_number' => $teacher['phone_number'],

                'hire_date' => $teacher['hire_date'],

                'created_at' => now(),

                'updated_at' => now()

            ]);

            

            $teacherIds[] = $teacherId;

        }

        

        // Create families (3 families for 3 sections)

        $families = [

            ['user_id' => $teacherIds[0], 'family_name' => 'Family A', 'home_address' => 'Address A', 'emergency_contact' => 'Contact A', 'emergency_phone' => '111-222-3333', 'created_at' => now(), 'updated_at' => now()],

            ['user_id' => $teacherIds[1], 'family_name' => 'Family B', 'home_address' => 'Address B', 'emergency_contact' => 'Contact B', 'emergency_phone' => '222-333-4444', 'created_at' => now(), 'updated_at' => now()],

            ['user_id' => $teacherIds[2], 'family_name' => 'Family C', 'home_address' => 'Address C', 'emergency_contact' => 'Contact C', 'emergency_phone' => '333-444-5555', 'created_at' => now(), 'updated_at' => now()],

        ];

        

        $familyIds = [];

        foreach ($families as $family) {

            $familyId = DB::table('families')->insertGetId($family);

            $familyIds[] = $family['user_id']; // Use user_id for foreign key reference

        }

        

        // Create students (50 students distributed across sections)

        $students = [];

        for ($i = 1; $i <= 50; $i++) {

            $sectionId = $sectionIds[($i - 1) % 3];

            $familyId = $familyIds[($i - 1) % 3];

            

            $students[] = [

                'first_name' => "Student {$i}",

                'last_name' => "Student {$i}",

                'date_of_birth' => now()->subYears(rand(3, 5))->format('Y-m-d'),

                'section_id' => $sectionId,

                'family_id' => $familyId,

                'created_at' => now(),

                'updated_at' => now()

            ];

        }

        

        // Insert students

        $studentIds = [];

        foreach ($students as $student) {

            $studentId = DB::table('students')->insertGetId($student);

            $studentIds[] = $studentId;

        }

        

        // Assign students to teachers (each teacher gets students from their section only)

        foreach ($teacherIds as $teacherIndex => $teacherId) {

            foreach ($studentIds as $studentIndex => $studentId) {

                $studentSectionId = $students[$studentIndex]['section_id'];

                $teacherSectionId = $sectionIds[$teacherIndex];

                

                // Only assign student to teacher if they're in the same section

                if ($studentSectionId === $teacherSectionId) {

                    DB::table('student_teacher')->insert([

                        'student_id' => $studentId,

                        'teacher_id' => $teacherId

                    ]);

                }

            }

        }



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

