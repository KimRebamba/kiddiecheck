<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Family;
use App\Models\Student;
use App\Models\AssessmentPeriod;
use App\Models\Test;
use App\Models\TestStandardScore;
use Carbon\Carbon;

class FamilyController extends Controller 
{
    /**
     * Create a new controller instance.
     * Ensure only authenticated family users can access
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the family dashboard
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get authenticated user
        $user = Auth::user();
        
        // Verify user is a family
        if ($user->role !== 'family') {
            abort(403, 'Unauthorized access');
        }

        // Get family profile
        $family = $user->family;
        
        if (!$family) {
            abort(404, 'Family profile not found');
        }

        // Get available avatars for profile selection
        $avatars = [
            'bunny',
            'fox',
            'frog',
            'mouse',
            'panda',
            'tiger',
        ];

        // Get all students belonging to this family
        $students = Student::where('family_id', $family->user_id)
            ->orderBy('date_of_birth', 'desc')
            ->get();

        // Prepare children data with their progress
        $childrenData = $students->map(function ($student) {
            // Count total and completed tests for this student
            $totalTests = Test::where('student_id', $student->student_id)->count();
            $completedTests = Test::where('student_id', $student->student_id)
                ->whereIn('status', ['completed', 'finalized'])
                ->count();

            return [
                'id' => $student->student_id,
                'name' => $student->first_name . ' ' . $student->last_name,
                'age' => $this->calculateAge($student->date_of_birth),
                'birth_date' => $student->date_of_birth->format('Y-m-d'),
                'total_tests' => $totalTests,
                'completed' => $completedTests,
                'avatar' => $this->getAvatarEmoji($student),
                'profile_image' => $student->profile_image, // Add profile image support
            ];
        });

        // Get upcoming scheduled assessment periods for all family students
        $upcomingAssessments = AssessmentPeriod::whereIn('student_id', $students->pluck('student_id'))
            ->where('start_date', '>=', Carbon::now()->subDays(30))
            ->with('student')
            ->orderBy('start_date', 'asc')
            ->limit(10)
            ->get()
            ->map(function ($period) {
                // Determine actual status based on dates
                $now = Carbon::now();
                
                if ($period->status === 'completed') {
                    $period->display_status = 'completed';
                } elseif ($now->greaterThan($period->end_date)) {
                    $period->display_status = 'overdue';
                } elseif ($now->between($period->start_date, $period->end_date)) {
                    $period->display_status = 'in_progress';
                } else {
                    $period->display_status = 'scheduled';
                }
                
                return $period;
            });

        // Prepare latest test results per child (only finalized tests with standard scores)
        $latestResults = [];
        
        foreach ($students as $student) {
            // Get the latest completed/finalized test with a standard score
            $latestTest = Test::where('student_id', $student->student_id)
                ->whereIn('status', ['completed', 'finalized'])
                ->whereHas('standardScore')
                ->with('standardScore')
                ->latest('test_date')
                ->first();

            if ($latestTest && $latestTest->standardScore) {
                $standardScore = $latestTest->standardScore;

                $latestResults[] = [
                    'child_id' => $student->student_id,
                    'child_name' => $student->first_name . ' ' . $student->last_name,
                    'avatar' => $this->getAvatarEmoji($student),
                    'profile_image' => $student->profile_image, // Add profile image support
                    'test_name' => 'ECCD Assessment',
                    'score' => $standardScore->standard_score,
                    'date' => $latestTest->test_date->format('Y-m-d'),
                    'interpretation' => $standardScore->interpretation ?? $this->getInterpretation($standardScore->standard_score),
                    'percentage' => $this->calculatePercentage($standardScore->standard_score)
                ];
            }
        }

        return view('family.index', [
            'family_name' => $family->family_name ?? 'Family',
            'children' => $childrenData,
            'upcoming_assessments' => $upcomingAssessments,
            'latest_results' => $latestResults,
            'avatars' => $avatars // Pass avatars to view
        ]);
    }

    /**
     * Update student profile image
     *
     * @param Request $request
     * @param int $studentId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfileImage(Request $request, $studentId)
    {
        $user = Auth::user();
        $family = $user->family;
        
        // Verify the student belongs to this family
        $student = Student::where('student_id', $studentId)
            ->where('family_id', $family->user_id)
            ->firstOrFail();

        // Validate request
        $request->validate([
            'selected_avatar' => 'nullable|string',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Handle pre-made avatar selection
        if ($request->has('selected_avatar') && $request->selected_avatar) {
            $student->profile_image = $request->selected_avatar;
        }
        
        // Handle custom image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists and it's a custom upload (not a pre-made avatar)
            if ($student->profile_image && str_starts_with($student->profile_image, 'profiles/')) {
                Storage::disk('public')->delete($student->profile_image);
            }
            
            $path = $request->file('profile_image')->store('profiles', 'public');
            $student->profile_image = $path;
        }

        $student->save();

        return redirect()->route('family.dashboard')
            ->with('success', 'Profile image updated successfully!');
    }

    /**
     * Calculate age from birth date in a readable format
     *
     * @param Carbon $birthDate
     * @return string
     */
    private function calculateAge($birthDate)
{
    $age = $birthDate->diff(Carbon::now());

    if ($age->y > 0) {
        if ($age->m > 0) {
            return $age->y . ' year' . ($age->y > 1 ? 's' : '') .
                   ' and ' .
                   $age->m . ' month' . ($age->m > 1 ? 's' : '');
        }

        return $age->y . ' year' . ($age->y > 1 ? 's' : '');
    }

    return $age->m . ' month' . ($age->m > 1 ? 's' : '');
}

    /**
     * Get interpretation text based on standard score
     *
     * @param int $standardScore
     * @return string
     */
    private function getInterpretation($standardScore)
    {
        if ($standardScore >= 130) return 'Very Superior';
        if ($standardScore >= 120) return 'Superior';
        if ($standardScore >= 110) return 'High Average';
        if ($standardScore >= 90) return 'Average';
        if ($standardScore >= 80) return 'Low Average';
        if ($standardScore >= 70) return 'Borderline';
        return 'Extremely Low';
    }

    /**
     * Calculate percentage for display (standard scores typically range from 40-160)
     *
     * @param int $standardScore
     * @return int
     */
    private function calculatePercentage($standardScore)
    {
        // ECCD standard scores typically range from 40 (lowest) to 160 (highest)
        // 100 is average
        // We'll normalize this to a 0-100 percentage for visual display
        $minScore = 40;
        $maxScore = 160;
        
        $percentage = (($standardScore - $minScore) / ($maxScore - $minScore)) * 100;
        return round(max(0, min(100, $percentage))); // Clamp between 0-100
    }

    /**
     * Get avatar emoji for student
     *
     * @param Student $student
     * @return string
     */
    private function getAvatarEmoji($student)
    {
        // Assign based on student_id to keep it consistent
        $emojis = ['👧', '👦', '👶', '🧒', '👧🏻', '👦🏻'];
        return $emojis[$student->student_id % count($emojis)];
    }

    /**
     * Get detailed child information (AJAX endpoint)
     *
     * @param int $studentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChildDetails($studentId)
    {
        $user = Auth::user();
        $family = $user->family;
        
        // Verify the student belongs to this family
        $student = Student::where('student_id', $studentId)
            ->where('family_id', $family->user_id)
            ->firstOrFail();

        // Get all completed tests for this student with standard scores
        $tests = Test::where('student_id', $student->student_id)
            ->whereIn('status', ['completed', 'finalized'])
            ->with('standardScore')
            ->orderBy('test_date', 'desc')
            ->get()
            ->map(function ($test) {
                return [
                    'id' => $test->test_id,
                    'test_date' => $test->test_date->format('Y-m-d'),
                    'status' => $test->status,
                    'standard_score' => $test->standardScore ? $test->standardScore->standard_score : null,
                    'interpretation' => $test->standardScore ? $test->standardScore->interpretation : null,
                    'notes' => $test->notes,
                ];
            });

        return response()->json([
            'student' => [
                'id' => $student->student_id,
                'name' => $student->first_name . ' ' . $student->last_name,
                'age' => $this->calculateAge($student->date_of_birth),
                'birth_date' => $student->date_of_birth->format('Y-m-d'),
            ],
            'tests' => $tests
        ]);
    }

    /**
     * Get upcoming assessments for family (AJAX endpoint)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUpcomingAssessments()
    {
        $user = Auth::user();
        $family = $user->family;
        $students = Student::where('family_id', $family->user_id)->get();
        $studentIds = $students->pluck('student_id'); 

        $assessments = AssessmentPeriod::whereIn('student_id', $studentIds)
            ->where('start_date', '>=', now())
            ->with('student')
            ->orderBy('start_date', 'asc')
            ->get()
            ->map(function ($period) {
                return [
                    'id' => $period->period_id,
                    'title' => $period->description,
                    'student_name' => $period->student->first_name . ' ' . $period->student->last_name,
                    'description' => 'Developmental assessment period',
                    'start_date' => $period->start_date->format('Y-m-d'),
                    'end_date' => $period->end_date->format('Y-m-d'),
                    'status' => $period->status
                ];
            });

        return response()->json($assessments);
    }
}