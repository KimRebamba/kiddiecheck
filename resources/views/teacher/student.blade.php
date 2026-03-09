@extends('teacher.layout')



@section('content')

<div class="d-flex align-items-center mb-4">

  <h1 class="h3 mb-0">{{ $student->first_name }} {{ $student->last_name }}</h1>

  <div class="ms-auto">

    <a href="{{ route('teacher.sections') }}" class="btn btn-sm btn-outline-secondary">Back</a>

  </div>

</div>



<div class="row g-3">

  <!-- Student Information -->

  <div class="col-md-6">

    <div class="card">

      <div class="card-header">

        <h5 class="mb-0">Student Information</h5>

      </div>

      <div class="card-body">

        <p class="mb-2"><strong>Name:</strong> {{ $student->first_name }} {{ $student->last_name }}</p>

        @php

          $dob = is_string($student->date_of_birth) ? \Carbon\Carbon::parse($student->date_of_birth) : $student->date_of_birth;

        @endphp

        <p class="mb-2"><strong>Date of Birth:</strong> {{ $dob ? $dob->format('M d, Y') : 'N/A' }}</p>

        <p class="mb-2"><strong>Age:</strong> {{ $student->age ?? 'N/A' }}</p>

        <p class="mb-2"><strong>Section:</strong> {{ optional($student->section)->name ?? 'N/A' }}</p>

        <p class="mb-2"><strong>Family:</strong> {{ optional($student->family)->family_name ?? 'N/A' }}</p>

      </div>

    </div>

  </div>



  <!-- Test Status -->

  <div class="col-md-6">

    <div class="card">

      <div class="card-header">

        <h5 class="mb-0">Assessment Status</h5>

      </div>

      <div class="card-body">

        <p class="mb-2">

          <strong>Eligible for Test:</strong><br>

          @if($student->eligible)

            <span class="badge bg-success">Yes</span>

          @else

            <span class="badge bg-secondary">No</span>

          @endif

        </p>

        <p class="mb-2">

          <strong>Last Standard Score:</strong><br>

          {{ $student->last_standard_score ?? 'No score' }}

        </p>

      </div>

    </div>

  </div>



  <!-- Assessment Periods -->

  <div class="col-md-12">

    <div class="card">

      <div class="card-header">

        <h5 class="mb-0">Assessment Periods</h5>

      </div>

      <div class="card-body">

        @if($student->assessmentPeriods->isEmpty())

          <p class="text-muted">No assessment periods.</p>

        @else

          <div class="table-responsive">

            <table class="table table-sm">

              <thead>

                <tr>

                  <th>Period</th>

                  <th>Dates</th>

                  <th>Status</th>

                  <th>Tests</th>

                  <th>Actions</th>

                </tr>

              </thead>

              <tbody>

                @foreach($student->assessmentPeriods as $period)

                  @php

                    $tests = $period->tests()->where('examiner_id', auth()->id())->get();

                  @endphp

                  <tr>

                    <td><strong>{{ $period->description }}</strong></td>

                    <td>{{ $period->start_date->format('M d') }} - {{ $period->end_date->format('M d, Y') }}</td>

                    <td>

                      <span class="badge bg-{{ $period->status === 'completed' ? 'success' : ($period->status === 'overdue' ? 'danger' : 'info') }}">

                        {{ ucfirst($period->status) }}

                      </span>

                    </td>

                    <td>{{ $tests->count() }}</td>

                    <td>

                      @php

                        $inProgressTest = $tests->firstWhere('status', 'in_progress');

                        $completedTest = $tests->firstWhere('status', 'completed');

                        $finalizedTest = $tests->firstWhere('status', 'finalized');

                        $viewableTest = $finalizedTest ?: $completedTest;

                      @endphp

                      @if($student->eligible && $period->status !== 'completed' && $period->status !== 'overdue' && $period->end_date >= now()->startOfDay())

                        @if($inProgressTest)

                          <a href="{{ route('teacher.tests.question', [$inProgressTest->test_id, 1, 0]) }}" class="btn btn-sm btn-outline-primary">Continue Test</a>

                        @elseif($viewableTest)

                          <a href="{{ route('teacher.tests.result', $viewableTest->test_id) }}" class="btn btn-sm btn-outline-secondary">View Result</a>

                        @else

                          <form action="{{ route('teacher.tests.start', $student->student_id) }}" method="POST" style="display: inline;">

                            @csrf

                            <input type="hidden" name="period_id" value="{{ $period->period_id }}">

                            <button type="submit" class="btn btn-sm btn-outline-primary">Start Test</button>

                          </form>

                        @endif

                      @else

                        <span class="text-muted small">

                          @if($period->status === 'overdue')

                            Period overdue

                          @elseif($period->status === 'completed')

                            Period completed

                          @elseif($period->end_date < now()->startOfDay())

                            Period ended

                          @else

                            Not eligible

                          @endif

                        </span>

                      @endif

                    </td>

                  </tr>

                @endforeach

              </tbody>

            </table>

          </div>

        @endif

      </div>

    </div>

  </div>



  <!-- Previous Tests -->

  <div class="col-md-12">

    <div class="card">

      <div class="card-header">

        <h5 class="mb-0">Test History</h5>

      </div>

      <div class="card-body">

        @php

          $teacherTests = $student->tests()

            ->where('examiner_id', auth()->id())

            ->orderBy('test_date', 'desc')

            ->get();

        @endphp

        

        @if($teacherTests->isEmpty())

          <p class="text-muted">No tests yet.</p>

        @else

          <div class="table-responsive">

            <table class="table table-sm">

              <thead>

                <tr>

                  <th>Date</th>

                  <th>Period</th>

                  <th>Status</th>

                  <th>Score</th>

                  <th>Actions</th>

                </tr>

              </thead>

              <tbody>

                @foreach($teacherTests as $test)

                  @php

                    $standardScore = $test->standardScore;

                  @endphp

                  <tr>

                    <td>{{ $test->test_date->format('M d, Y') }}</td>

                    <td>{{ optional($test->assessmentPeriod)->description ?? 'N/A' }}</td>

                    <td>

                      <span class="badge bg-{{ 

                        $test->status === 'finalized' ? 'success' : 

                        ($test->status === 'completed' ? 'info' : 

                        ($test->status === 'canceled' ? 'danger' : 'warning'))

                      }}">

                        {{ ucfirst($test->status) }}

                      </span>

                    </td>

                    <td>{{ $standardScore ? $standardScore->standard_score : 'N/A' }}</td>

                    <td>

                      <a href="{{ route('teacher.reports.detail', [$student->student_id, $test->period_id, $test->test_id]) }}" class="btn btn-xs btn-outline-secondary" style="font-size: 0.8rem;">View</a>

                    </td>

                  </tr>

                @endforeach

              </tbody>

            </table>

          </div>

        @endif

      </div>

    </div>

  </div>

</div>



<style>

@import url('https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Nunito:wght@400;600;700;800;900&display=swap');



:root {

  --violet:      #845EC2;

  --violet-soft: #EDE4FF;

  --violet-bg:   #F8F4FF;

  --teal:        #2EC4B6;

  --teal-soft:   #C8F4F1;

  --coral:       #FF6B8A;

  --coral-soft:  #FFE0E8;

  --mint:        #52C27B;

  --mint-soft:   #D4F5E2;

  --lemon:       #F9C74F;

  --lemon-soft:  #FFF6CC;

  --sky:         #4EA8DE;

  --sky-soft:    #D6EEFF;

  --peach:       #FF9A76;

  --text:        #2D2040;

  --text-muted:  #8A7A99;

  --radius:      14px;

  --shadow:      0 4px 20px rgba(100,60,160,0.09);

}



body { font-family: 'Nunito', sans-serif !important; background: var(--violet-bg); color: var(--text); }



/* ── HERO HEADER ── */

.student-hero-avatar {

  width: 54px; height: 54px; border-radius: 14px; flex-shrink: 0;

  background: linear-gradient(135deg, var(--violet), var(--coral));

  display: flex; align-items: center; justify-content: center;

  font-family: 'Baloo 2', cursive; font-size: 1.5rem; font-weight: 800; color: white;

  box-shadow: 0 4px 14px rgba(132,94,194,0.3);

}

.h3.fw-bold {

  font-family: 'Baloo 2', cursive !important;

  font-size: 1.5rem !important;

  background: linear-gradient(135deg, var(--violet), var(--coral));

  -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;

}

.student-hero-sub { font-size: 0.8rem; font-weight: 700; color: var(--text-muted); margin-top: 2px; }



/* ── BACK BUTTON ── */

.btn-ghost-back {

  font-family: 'Nunito', sans-serif; font-weight: 800; font-size: 0.8rem;

  background: white; color: var(--text-muted); border: 1.5px solid #E8E0F0;

  border-radius: 10px; padding: 6px 14px; text-decoration: none;

  display: inline-flex; align-items: center; transition: all 0.18s;

}

.btn-ghost-back:hover { background: var(--violet-soft); color: var(--violet); border-color: var(--violet-soft); }



/* ── CARDS ── */

.card {

  border: none !important; border-radius: var(--radius) !important;

  box-shadow: var(--shadow) !important; overflow: hidden;

  animation: fadeUp 0.4s ease both; transition: transform 0.2s, box-shadow 0.2s;

}

.card:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(100,60,160,0.13) !important; }

@keyframes fadeUp { from { opacity:0; transform:translateY(14px); } to { opacity:1; transform:translateY(0); } }

.col-md-6:nth-child(1) .card, .col-md-12:nth-child(1) .card { animation-delay: 0.05s; }

.col-md-6:nth-child(2) .card, .col-md-12:nth-child(2) .card { animation-delay: 0.10s; }

.col-md-12:nth-child(3) .card { animation-delay: 0.15s; }

.col-md-12:nth-child(4) .card { animation-delay: 0.20s; }



/* ── CARD HEADERS ── */

.card-header {

  padding: 13px 18px !important; display: flex; align-items: center; gap: 10px;

  border-bottom: 2px solid #F0E8FF !important;

}

.card-header h5 {

  font-family: 'Baloo 2', cursive !important;

  font-size: 0.95rem !important; font-weight: 800 !important; color: var(--text) !important; margin: 0 !important;

}

.header-violet { background: var(--violet-bg) !important;  border-left: 4px solid var(--violet) !important; }

.header-teal   { background: var(--teal-soft)  !important; border-left: 4px solid var(--teal)   !important; }

.header-sky    { background: var(--sky-soft)   !important; border-left: 4px solid var(--sky)    !important; }

.header-mint   { background: var(--mint-soft)  !important; border-left: 4px solid var(--mint)   !important; }



.section-icon {

  width: 30px; height: 30px; border-radius: 8px;

  display: flex; align-items: center; justify-content: center; font-size: 0.95rem; flex-shrink: 0;

}

.si-violet { background: var(--violet-soft); }

.si-teal   { background: var(--teal-soft);   }

.si-sky    { background: var(--sky-soft);     }

.si-mint   { background: var(--mint-soft);    }



/* ── CARD BODY ── */

.card-body { padding: 18px 20px !important; }



/* ── INFO ROWS ── */

.info-row {

  display: flex; align-items: center; justify-content: space-between;

  padding: 9px 0; border-bottom: 1px solid #F5F0FF;

}

.info-row:last-child { border-bottom: none; }

.info-label { font-size: 0.74rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-muted); }

.info-value { font-size: 0.88rem; font-weight: 700; color: var(--text); text-align: right; }



/* ── SCORE DISPLAY ── */

.score-display {

  font-family: 'Baloo 2', cursive; font-size: 1.2rem; font-weight: 800; color: var(--mint);

}



/* ── BADGES ── */

.badge { font-size: 0.7rem !important; font-weight: 800 !important; padding: 4px 11px !important; border-radius: 20px !important; }

.badge.bg-success  { background: var(--mint-soft)   !important; color: #2a7a50 !important; }

.badge.bg-info     { background: var(--sky-soft)    !important; color: #2260a0 !important; }

.badge.bg-warning  { background: var(--lemon-soft)  !important; color: #9a6800 !important; }

.badge.bg-danger   { background: var(--coral-soft)  !important; color: #c0294a !important; }

.badge.bg-muted    { background: #F0E8FF             !important; color: var(--text-muted) !important; }

.badge.bg-section  { background: var(--violet-soft) !important; color: #5a3e8a !important; }



/* ── TABLES ── */

.table { margin-bottom: 0 !important; }

.table thead th {

  font-size: 0.7rem !important; font-weight: 800 !important;

  text-transform: uppercase; letter-spacing: 0.07em;

  color: var(--text-muted) !important; background: #FDFBFF !important;

  border-bottom: 2px solid #F0E8FF !important;

  padding: 10px 16px !important; white-space: nowrap;

}

.table tbody tr { border-bottom: 1px solid #F9F5FF !important; transition: background 0.15s; }

.table tbody tr:last-child { border-bottom: none !important; }

.table tbody tr:hover { background: #FDFBFF !important; }

.table tbody td { padding: 11px 16px !important; font-size: 0.86rem; vertical-align: middle !important; border: none !important; }

.fw-semibold { font-weight: 800 !important; font-size: 0.87rem; color: var(--text); }

.date-text   { font-size: 0.83rem; font-weight: 700; color: var(--text); }

.test-count  { font-family: 'Baloo 2', cursive; font-size: 1rem; font-weight: 800; color: var(--violet); }

.na-text     { font-size: 0.78rem; font-weight: 700; color: var(--text-muted); font-style: italic; }

.empty-inline { padding: 18px 20px; font-size: 0.86rem; color: var(--text-muted); font-weight: 600; font-style: italic; }



/* ── BUTTONS ── */

.btn { font-family: 'Nunito', sans-serif !important; font-weight: 800 !important; border-radius: 10px !important; transition: all 0.18s !important; font-size: 0.77rem !important; }

.btn-primary-grad {

  background: linear-gradient(135deg, var(--violet), var(--coral)) !important;

  color: white !important; border: none !important;

  box-shadow: 0 3px 10px rgba(132,94,194,0.25) !important;

}

.btn-primary-grad:hover { transform: translateY(-1px); box-shadow: 0 5px 14px rgba(132,94,194,0.35) !important; color: white !important; }

.btn-outline-primary {

  color: var(--violet) !important; border: 1.5px solid var(--violet-soft) !important; background: white !important;

}

.btn-outline-primary:hover { background: var(--violet-soft) !important; }

.btn-outline-secondary {

  color: var(--text-muted) !important; border: 1.5px solid #E8E0F0 !important; background: white !important;

}

.btn-outline-secondary:hover { background: #F0E8FF !important; }

.btn-xs { padding: 4px 11px !important; font-size: 0.74rem !important; }

</style>

@endsection

