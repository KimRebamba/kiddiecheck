<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Test;
use App\Models\Domain;
use App\Services\EccdScoring;

class ChecklistController extends Controller
{
    public function record($studentId)
    {
        $student = Student::with(['family','section','teachers.user'])->findOrFail($studentId);

        $tests = Test::with(['student','responses.question.domain','scores.domain'])
            ->where('student_id', $student->id)
            ->orderBy('test_date','desc')
            ->take(3)
            ->get();

        $domains = Domain::with(['questions'])->orderBy('name')->get();
        $summaries = EccdScoring::summarize($tests, $domains);

        return view('admin.checklist.record', compact('student','tests','domains','summaries'));
    }
}
