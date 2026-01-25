<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

class WelcomeController
{
    public function index()
    {
        $users = User::with(['family', 'teacher'])->get();
        $families = Family::with(['user', 'students'])->get();
        $students = Student::with(['family', 'teachers', 'tags', 'tests'])->get();
        $teachers = Teacher::with(['user', 'students'])->get();
        $domains = Domain::with(['questions'])->get();
        $questions = Question::with(['domain'])->get();
        $tests = Test::with(['student', 'observer', 'responses', 'scores', 'pictures'])->get();
        $responses = TestResponse::with(['test', 'question'])->get();
        $scores = DomainScore::with(['test', 'domain'])->get();
        $tags = StudentTag::with(['student'])->get();
        $pictures = TestPicture::with(['test', 'question'])->get();

        return view('welcome', compact(
            'users','families','students','teachers','domains','questions','tests','responses','scores','tags','pictures'
        ));
    }
}
