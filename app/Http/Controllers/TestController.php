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
                'order' => $question->order
            ];
        }
    }
    
    // Calculate domain scores
    $domains = DB::table('domains')->get();
    $domainScores = [];
    
    foreach ($domains as $domain) {
        $domainQuestions = DB::table('questions')
            ->where('domain_id', $domain->domain_id)
            ->pluck('question_id');
        
        $totalDomainQuestions = count($domainQuestions);
        
        $yesCount = DB::table('test_responses')
            ->where('test_id', $testId)
            ->whereIn('question_id', $domainQuestions)
            ->where('response', 'yes')
            ->count();
        
        $answeredDomainCount = DB::table('test_responses')
            ->where('test_id', $testId)
            ->whereIn('question_id', $domainQuestions)
            ->count();
        
        $percentage = $totalDomainQuestions > 0 ? round(($yesCount / $totalDomainQuestions) * 100) : 0;
        
        $domainScores[] = [
            'domain' => $domain->name,
            'domain_id' => $domain->domain_id,  // ← Added this
            'icon' => $this->domainIcons[$domain->name] ?? '📋',
            'yes_count' => $yesCount,
            'answered' => $answeredDomainCount,
            'total' => $totalDomainQuestions,
            'percentage' => $percentage
        ];
    }
    
    return view('test.review', [
        'test' => $test,
        'student' => $student,
        'totalQuestions' => $totalQuestions,
        'answeredCount' => $answeredCount,
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
        
        return view('test.complete', [
            'test' => $test,
            'student' => $student,
            'domainScores' => $domainScores
        ]);
    }
}