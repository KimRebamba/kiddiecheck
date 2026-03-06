<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    private $domainIcons = [
        'Gross Motor' => '⚡',
        'Fine Motor' => '✋',
        'Self-Help' => '🎯',
        'Receptive Language' => '👂',
        'Expressive Language' => '💬',
        'Cognitive' => '🧠',
        'Social-Emotional' => '❤️',
    ];

    /**
     * Start test
     */
    public function start($testId)
    {
        // Find first unanswered question
        $allQuestions = DB::table('questions')
            ->orderBy('domain_id')
            ->orderBy('order')
            ->get();

        $answeredQuestionIds = DB::table('test_responses')
            ->where('test_id', $testId)
            ->pluck('question_id')
            ->toArray();

        // Find first unanswered
        $firstUnanswered = null;
        foreach ($allQuestions as $question) {
            if (!in_array($question->question_id, $answeredQuestionIds)) {
                $firstUnanswered = $question;
                break;
            }
        }

        // If all answered, go to review page
        if (!$firstUnanswered) {
            return redirect()->route('test.review', $testId);
        }

        // Go to first unanswered question
        return redirect()->route('test.question', [
            'test_id' => $testId,
            'question_id' => $firstUnanswered->question_id
        ]);
    }

    /**
     * Show question
     */
    public function showQuestion($testId, $questionId)
    {
        // Get basic data
        $test = DB::table('tests')->where('test_id', $testId)->first();
        $student = DB::table('students')->where('student_id', $test->student_id)->first();
        $currentQuestion = DB::table('questions')->where('question_id', $questionId)->first();
        $currentDomain = DB::table('domains')->where('domain_id', $currentQuestion->domain_id)->first();
        
        // Get all questions
        $allQuestions = DB::table('questions')
            ->orderBy('domain_id')
            ->orderBy('order')
            ->get();
        
        // Find position
        $currentIndex = 0;
        foreach ($allQuestions as $index => $q) {
            if ($q->question_id == $questionId) {
                $currentIndex = $index;
                break;
            }
        }
        
        // Calculate progress
        $currentQuestionNumber = $currentIndex + 1;
        $totalQuestions = count($allQuestions);
        $progressPercentage = ($currentQuestionNumber / $totalQuestions) * 100;
        
        // Count answered questions
        $answeredCount = DB::table('test_responses')
            ->where('test_id', $testId)
            ->count();
        
        // Get prev/next
        $previousQuestionId = $currentIndex > 0 ? $allQuestions[$currentIndex - 1]->question_id : null;
        $nextQuestionId = $currentIndex < count($allQuestions) - 1 ? $allQuestions[$currentIndex + 1]->question_id : null;
        
        // Get existing answer
        $existingResponse = DB::table('test_responses')
            ->where('test_id', $testId)
            ->where('question_id', $questionId)
            ->first();
        
        // Check if this is the last question
        $isLastQuestion = $currentIndex == count($allQuestions) - 1;
        
        return view('test.question', [
            'test' => $test,
            'student' => $student,
            'currentQuestion' => $currentQuestion,
            'currentDomain' => $currentDomain,
            'domainIcon' => $this->domainIcons[$currentDomain->name] ?? '📋',
            'currentQuestionNumber' => $currentQuestionNumber,
            'totalQuestions' => $totalQuestions,
            'progressPercentage' => $progressPercentage,
            'previousQuestionId' => $previousQuestionId,
            'nextQuestionId' => $nextQuestionId,
            'existingResponse' => $existingResponse,
            'answeredCount' => $answeredCount,
            'isLastQuestion' => $isLastQuestion
        ]);
    }

    /**
 * Go to first unanswered question in a specific domain
 */
public function firstUnansweredInDomain($testId, $domainId)
{
    // Get all questions in this domain
    $domainQuestions = DB::table('questions')
        ->where('domain_id', $domainId)
        ->orderBy('order')
        ->get();
    
    // Get answered question IDs
    $answeredQuestionIds = DB::table('test_responses')
        ->where('test_id', $testId)
        ->pluck('question_id')
        ->toArray();
    
    // Find first unanswered in this domain
    foreach ($domainQuestions as $question) {
        if (!in_array($question->question_id, $answeredQuestionIds)) {
            return redirect()->route('test.question', [
                'test_id' => $testId,
                'question_id' => $question->question_id
            ]);
        }
    }
    
    // All answered in this domain, go back to review
    return redirect()->route('test.review', $testId);
}

    /**
     * Submit answer
     */
    public function submitAnswer(Request $request, $testId)
    {
        // Validate
        $request->validate([
            'question_id' => 'required|exists:questions,question_id',
            'response' => 'required|in:yes,no',
            'notes' => 'nullable|string|max:1000'
        ]);

        // Delete old response if exists
        DB::table('test_responses')
            ->where('test_id', $testId)
            ->where('question_id', $request->question_id)
            ->delete();

        // Insert new response
        DB::table('test_responses')->insert([
            'test_id' => $testId,
            'question_id' => $request->question_id,
            'response' => $request->response,
            'is_assumed' => 0,
            'notes' => $request->notes
        ]);
        
        // Get all questions
        $allQuestions = DB::table('questions')
            ->orderBy('domain_id')
            ->orderBy('order')
            ->get();
        
        // Find current index
        $currentIndex = 0;
        foreach ($allQuestions as $index => $q) {
            if ($q->question_id == $request->question_id) {
                $currentIndex = $index;
                break;
            }
        }
        
        // Go to next question or review page
        if ($currentIndex < count($allQuestions) - 1) {
            $nextQuestion = $allQuestions[$currentIndex + 1];
            return redirect()->route('test.question', [
                'test_id' => $testId,
                'question_id' => $nextQuestion->question_id
            ]);
        }
        
        // Last question answered - go to review
        return redirect()->route('test.review', $testId);
    }

    /**
     * Review page - shows unanswered questions
     */
    public function review($testId)
{
    $test = DB::table('tests')->where('test_id', $testId)->first();
    $student = DB::table('students')->where('student_id', $test->student_id)->first();
    
    // Get all questions
    $allQuestions = DB::table('questions')
        ->orderBy('domain_id')
        ->orderBy('order')
        ->get();
    
    $totalQuestions = count($allQuestions);
    
    // Get answered question IDs
    $answeredQuestionIds = DB::table('test_responses')
        ->where('test_id', $testId)
        ->pluck('question_id')
        ->toArray();
    
    $answeredCount = count($answeredQuestionIds);
    
    // Get unanswered questions with domain info
    $unansweredQuestions = [];
    foreach ($allQuestions as $question) {
        if (!in_array($question->question_id, $answeredQuestionIds)) {
            $domain = DB::table('domains')->where('domain_id', $question->domain_id)->first();
            $unansweredQuestions[] = [
                'question_id' => $question->question_id,
                'text' => $question->display_text ?? $question->text,
                'domain' => $domain->name,
                'domain_icon' => $this->domainIcons[$domain->name] ?? '📋',
        'unansweredQuestions' => $unansweredQuestions,
        'domainScores' => $domainScores,
        'canSubmit' => count($unansweredQuestions) === 0
    ]);
}

    /**
     * Submit complete test
     */
    public function submitTest($testId)
    {
        // Check if all questions are answered
        $totalQuestions = DB::table('questions')->count();
        $answeredCount = DB::table('test_responses')->where('test_id', $testId)->count();
        
        if ($answeredCount < $totalQuestions) {
            return redirect()->route('test.review', $testId)
                ->with('error', 'Please answer all questions before submitting.');
        }
        
        // Mark test as completed
        DB::table('tests')
            ->where('test_id', $testId)
            ->update(['status' => 'completed']);
        
        return redirect()->route('test.complete', $testId)
            ->with('success', 'Test submitted successfully!');
    }

    /**
     * Complete test - final results
     */
    public function complete($testId)
    {
        $test = DB::table('tests')->where('test_id', $testId)->first();
        
        // Ensure test is completed
        if ($test->status !== 'completed') {
            return redirect()->route('test.review', $testId);
        }
        
        $student = DB::table('students')->where('student_id', $test->student_id)->first();
        
        // Calculate scores
        $domains = DB::table('domains')->get();
        $domainScores = [];
        
        foreach ($domains as $domain) {
            $domainQuestions = DB::table('questions')
                ->where('domain_id', $domain->domain_id)
                ->pluck('question_id');
            
            $totalQuestions = count($domainQuestions);
            
            $yesCount = DB::table('test_responses')
                ->where('test_id', $testId)
                ->whereIn('question_id', $domainQuestions)
                ->where('response', 'yes')
                ->count();
            
            $percentage = $totalQuestions > 0 ? round(($yesCount / $totalQuestions) * 100) : 0;
            
            $domainScores[] = [
                'domain' => $domain->name,
                'icon' => $this->domainIcons[$domain->name] ?? '📋',
                'score' => $yesCount,
                'total' => $totalQuestions,
                'percentage' => $percentage
            ];
        }
        
        // Calculate and store sum_scaled_scores and standard_score
        $sumScaledScores = 0;
        foreach ($domainScores as $domainScore) {
            // Get scaled score for this domain based on age and raw score
            $studentAge = $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->diffInMonths(now()) : 0;
            $scaleVersionId = DB::table('scale_versions')
                ->where('name', 'ECCD 2004')
                ->value('scale_version_id');
            
            $domainScaledScore = DB::table('domain_scaled_scores')
                ->where('scale_version_id', $scaleVersionId)
                ->where('domain_id', $domainScore['domain'])
                ->where('age_min_months', '<=', $studentAge)
                ->where('age_max_months', '>=', $studentAge)
                ->where('raw_min', '<=', $domainScore['yes_count'])
                ->where('raw_max', '>=', $domainScore['yes_count'])
                ->value('scaled_score');
            
            if ($domainScaledScore) {
                $sumScaledScores += $domainScaledScore;
                
                // Store domain scaled score
                DB::table('test_domain_scaled_scores')->updateOrInsert([
                    'test_id' => $testId,
                    'domain_id' => $domainScore['domain'],
                    'scale_version_id' => $scaleVersionId,
                    'raw_score' => $domainScore['yes_count'],
                    'scaled_score' => $domainScaledScore,
                    'updated_at' => now(),
                    'created_at' => now(),
                ], [
                    'test_id' => $testId,
                    'domain_id' => $domainScore['domain'],
                    'scale_version_id' => $scaleVersionId,
                    'raw_score' => $domainScore['yes_count'],
                    'scaled_score' => $domainScaledScore,
                ]);
            }
        }
        
        // Calculate and store standard score based on sum of scaled scores
        $standardScore = $this->calculateStandardScore($sumScaledScores);
        
        // Store in test_standard_scores table
        DB::table('test_standard_scores')->updateOrInsert([
            'test_id' => $testId,
            'scale_version_id' => $scaleVersionId,
            'sum_scaled_scores' => $sumScaledScores,
            'standard_score' => $standardScore,
            'interpretation' => $this->getInterpretation($standardScore, $studentAge),
            'updated_at' => now(),
            'created_at' => now(),
        ], [
            'test_id' => $testId,
            'scale_version_id' => $scaleVersionId,
            'sum_scaled_scores' => $sumScaledScores,
            'standard_score' => $standardScore,
            'interpretation' => $this->getInterpretation($standardScore, $studentAge),
        ]);
        
        // Calculate standard score
        $sumScaledScores = array_sum(array_column($domainScores, 'score'));
        $standardScore = $this->calculateStandardScore($sumScaledScores);
        
        // Get interpretation based on standard score and age
        $studentAge = $student->age;
        $interpretation = $this->getInterpretation($standardScore, $studentAge);
        
        return view('test.complete', [
            'test' => $test,
            'student' => $student,
            'domainScores' => $domainScores,
            'standardScore' => $standardScore,
            'interpretation' => $interpretation
        ]);
    }
    
    /**
     * Calculate standard score from sum of scaled scores
     */
    private function calculateStandardScore($sumScaledScores)
    {
        $standardLookup = [
            29 => 37, 30 => 38, 31 => 40, 32 => 41, 33 => 43, 34 => 44,
            35 => 45, 36 => 47, 37 => 48, 38 => 50, 39 => 51, 40 => 53,
            41 => 54, 42 => 56, 43 => 57, 44 => 59, 45 => 60, 46 => 62,
            47 => 63, 48 => 65, 49 => 66, 50 => 67, 51 => 69, 52 => 70,
            53 => 72, 54 => 73, 55 => 75, 56 => 76, 57 => 78, 58 => 79,
            59 => 81, 60 => 82, 61 => 84, 62 => 85, 63 => 86, 64 => 88,
            65 => 89, 66 => 91, 67 => 92, 68 => 94, 69 => 95, 70 => 97,
            71 => 98, 72 => 100, 73 => 101, 74 => 103, 75 => 104, 76 => 105,
            77 => 107, 78 => 108, 79 => 109, 80 => 110, 81 => 111, 82 => 113,
            83 => 114, 84 => 115, 85 => 117, 86 => 118, 87 => 119, 88 => 120,
            89 => 121, 90 => 123, 91 => 124, 92 => 126, 93 => 127, 94 => 129,
            95 => 130, 96 => 132, 97 => 133, 98 => 135, 99 => 136, 100 => 137
        ];
        
        return $standardLookup[$sumScaledScores] ?? null;
    }
    
    /**
     * Get interpretation based on standard score and age
     */
    private function getInterpretation($standardScore, $studentAge)
    {
        if ($studentAge >= 61 && $studentAge <= 71) {
            // Ages 5.1 – 5.11 years
            if ($standardScore >= 85) return 'Advanced Development';
            if ($standardScore >= 70) return 'Average Development';
            return 'Below Average Development';
        }
        
        if ($studentAge >= 49 && $studentAge <= 60) {
            // Ages 4.1 – 5.0 years
            if ($standardScore >= 85) return 'Advanced Development';
            if ($standardScore >= 70) return 'Average Development';
            return 'Below Average Development';
        }
        
        if ($studentAge >= 37 && $studentAge <= 48) {
            // Ages 3.1 – 4.0 years
            if ($standardScore >= 85) return 'Advanced Development';
            if ($standardScore >= 70) return 'Average Development';
            return 'Below Average Development';
        }
        
        // Default for other ages or scores
        if ($standardScore >= 85) return 'Advanced Development';
        if ($standardScore >= 70) return 'Average Development';
        return 'Below Average Development';
    }
}