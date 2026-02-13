<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Family;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Domain;
use App\Models\Question;
use App\Models\Test;
use App\Models\TestResponse;
use App\Models\DomainScore;
use App\Models\StudentTag;
use App\Models\TestPicture;
use App\Services\EccdScoring;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }
    
    public function eccd(Request $request)
    {
        // List of available scale versions for selection
        $scaleVersions = DB::table('scale_versions')
            ->select('scale_version_id as id', 'name')
            ->orderBy('name')
            ->get();

        $selectedScaleId = $request->input('scale_version_id');

        $questionsByDomain = collect();
        $domainScaledScores = collect();
        $standardScoreScales = collect();
        $testDomainScores = collect();
        $testStandardScores = collect();

        if ($selectedScaleId) {
            // Questions for the selected scale version, grouped by domain
            $questions = DB::table('questions as q')
                ->join('domains as d', 'q.domain_id', '=', 'd.domain_id')
                ->where('q.scale_version_id', $selectedScaleId)
                ->orderBy('d.name')
                ->orderBy('q.order')
                ->select(
                    'd.name as domain_name',
                    'q.order as question_order',
                    'q.text as question_text',
                    'q.display_text as question_display_text',
                    'q.question_type as question_type'
                )
                ->get();

            $questionsByDomain = $questions->groupBy('domain_name');

            // Domain scaled score lookup rows for this scale version
            $domainScaledScores = DB::table('domain_scaled_scores as ds')
                ->join('domains as d', 'ds.domain_id', '=', 'd.domain_id')
                ->where('ds.scale_version_id', $selectedScaleId)
                ->orderBy('d.name')
                ->orderBy('ds.age_min_months')
                ->select(
                    'd.name as domain_name',
                    'ds.age_min_months as age_min',
                    'ds.age_max_months as age_max',
                    'ds.raw_min as raw_min',
                    'ds.raw_max as raw_max',
                    'ds.scaled_score as scaled_score'
                )
                ->get();

            // Standard score scale rows for this scale version
            $standardScoreScales = DB::table('standard_score_scales as ss')
                ->where('ss.scale_version_id', $selectedScaleId)
                ->orderBy('ss.sum_scaled_min')
                ->select(
                    'ss.sum_scaled_min as sum_scaled_min',
                    'ss.sum_scaled_max as sum_scaled_max',
                    'ss.standard_score as standard_score'
                )
                ->get();

            // Per-test domain scaled scores for this scale version
            $testDomainScores = DB::table('test_domain_scaled_scores as tds')
                ->join('tests as t', 'tds.test_id', '=', 't.test_id')
                ->join('domains as d', 'tds.domain_id', '=', 'd.domain_id')
                ->join('students as s', 't.student_id', '=', 's.student_id')
                ->where('tds.scale_version_id', $selectedScaleId)
                ->orderBy('t.test_id')
                ->orderBy('d.name')
                ->select(
                    't.test_id as test_id',
                    DB::raw("CONCAT(s.first_name, ' ', s.last_name) as student_name"),
                    'd.name as domain_name',
                    'tds.raw_score as raw_score',
                    'tds.scaled_score as scaled_score'
                )
                ->get();

            // Test standard scores for this scale version
            $testStandardScores = DB::table('test_standard_scores as ts')
                ->join('tests as t', 'ts.test_id', '=', 't.test_id')
                ->join('students as s', 't.student_id', '=', 's.student_id')
                ->where('ts.scale_version_id', $selectedScaleId)
                ->orderBy('t.test_id')
                ->select(
                    't.test_id as test_id',
                    DB::raw("CONCAT(s.first_name, ' ', s.last_name) as student_name"),
                    'ts.sum_scaled_scores as sum_scaled_scores',
                    'ts.standard_score as standard_score',
                    'ts.interpretation as interpretation'
                )
                ->get();
        }

        return view('admin.eccd', [
            'scaleVersions' => $scaleVersions,
            'selectedScaleId' => $selectedScaleId,
            'questionsByDomain' => $questionsByDomain,
            'domainScaledScores' => $domainScaledScores,
            'standardScoreScales' => $standardScoreScales,
            'testDomainScores' => $testDomainScores,
            'testStandardScores' => $testStandardScores,
        ]);
    }


}
