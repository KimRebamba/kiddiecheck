<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Family;
use App\Models\Student;
use App\Models\Test;
use App\Models\TestResponse;
use App\Models\Domain;
use App\Models\Question;
use App\Models\DomainScore;
use App\Services\EccdScoring;

class FamilyController extends Controller
{
    public function index()
    {

    }
}
