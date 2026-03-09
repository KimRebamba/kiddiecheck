@extends('teacher.layout')



@section('content')

<div class="d-flex align-items-center mb-4">

  <h1 class="h3 mb-0">{{ $section->name }}</h1>

  <div class="ms-auto">

    <a href="{{ route('teacher.sections') }}" class="btn btn-outline-secondary">Back</a>

    

  </div>

</div>



<div class="row g-3">

  <!-- Section Information -->

  <div class="col-md-6">

    <div class="card">

      <div class="card-header">

        <h5 class="mb-0">Section Information</h5>

      </div>

      <div class="card-body">

        <p class="mb-2"><strong>Name:</strong> {{ $section->name }}</p>

        <p class="mb-2"><strong>Total Students:</strong> {{ $students->count() }}</p>

      </div>

    </div>

  </div>



  <!-- Students -->

  <div class="col-md-12">

    <div class="card">

      <div class="card-header">

        <h5 class="mb-0">Assigned Students</h5>

      </div>

      <div class="card-body">

        @if($students->isEmpty())

          <p class="text-muted">No students assigned to this section.</p>

        @else

          <div class="table-responsive">

            <table class="table">

              <thead>

                <tr>

                  <th>Name</th>

                  <th>Age</th>

                  <th>Eligible for Test</th>

                  <th>Last Standard Score</th>

                  <th>Actions</th>

                </tr>

              </thead>

              <tbody>

                @foreach($students as $student)

                  <tr>

                    <td>

                      <a href="{{ route('teacher.student', $student->student_id) }}" class="text-decoration-none">

                        {{ $student->first_name }} {{ $student->last_name }}

                      </a>

                    </td>

                    <td>{{ $student->age ?? 'N/A' }} years</td>

                    <td>

                      @if($student->eligible)

                        <span class="badge bg-success">Yes</span>

                      @else

                        <span class="badge bg-secondary">No</span>

                      @endif

                    </td>

                    <td>{{ $student->last_standard_score ?? 'No score' }}</td>

                    <td>

                      <div class="btn-group btn-group-sm" role="group">

                        <a href="{{ route('teacher.student', $student->student_id) }}" class="btn btn-outline-secondary">View</a>

                        

                        @if($student->eligible)

                          @php

                            $availablePeriod = DB::table('assessment_periods')

                                ->where('student_id', $student->student_id)

                                ->where('status', '!=', 'overdue')

                                ->where('status', '!=', 'completed')

                                ->where('end_date', '>=', now()->startOfDay())

                                ->first();

                            $inProgressTest = null;

                            if ($availablePeriod) {

                                $inProgressTest = DB::table('tests')

                                    ->where('period_id', $availablePeriod->period_id)

                                    ->where('student_id', $student->student_id)

                                    ->where('examiner_id', auth()->id())

                                    ->where('status', 'in_progress')

                                    ->first();

                            }

                          @endphp

                          @if($availablePeriod)

                            @if($inProgressTest)

                              <a href="{{ route('teacher.tests.question', [$inProgressTest->test_id, 1, 0]) }}" class="btn btn-outline-primary">Continue Test</a>

                            @else

                              <form action="{{ route('teacher.tests.start', $student->student_id) }}" method="POST" style="display: inline;">

                                @csrf

                                <input type="hidden" name="period_id" value="{{ $availablePeriod->period_id }}">

                                <button type="submit" class="btn btn-outline-primary">Start Test</button>

                              </form>

                            @endif

                          @endif

                        @endif

                        

                        <!-- Delete Section Button -->

                        @if($section->student_count == 0)

                          <form action="{{ route('teacher.sections.destroy', $section->section_id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this section?');">

                            @csrf

                            @method('DELETE')

                            <button type="submit" class="btn btn-outline-danger">Delete Section</button>

                          </form>

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



/* ── PAGE HEADER ── */

.h3.fw-bold {

  font-family: 'Baloo 2', cursive !important; font-size: 1.6rem !important;

  background: linear-gradient(135deg, var(--violet), var(--coral));

  -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;

}

.page-subhead { font-size: 0.82rem; font-weight: 700; color: var(--text-muted); margin-top: 2px; }



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

.col-md-6 .card  { animation-delay: 0.05s; }

.col-md-12 .card { animation-delay: 0.10s; }



/* ── CARD HEADERS ── */

.card-header {

  padding: 13px 18px !important; display: flex; align-items: center; gap: 10px;

  border-bottom: 2px solid #F0E8FF !important;

}

.card-header h5 {

  font-family: 'Baloo 2', cursive !important;

  font-size: 0.95rem !important; font-weight: 800 !important; color: var(--text) !important; margin: 0 !important;

}

.header-violet { background: var(--violet-bg) !important; border-left: 4px solid var(--violet) !important; }

.header-teal   { background: var(--teal-soft)  !important; border-left: 4px solid var(--teal)   !important; }



.section-icon {

  width: 30px; height: 30px; border-radius: 8px;

  display: flex; align-items: center; justify-content: center; font-size: 0.95rem; flex-shrink: 0;

}

.si-violet { background: var(--violet-soft); }

.si-teal   { background: var(--teal-soft);   }



.card-body { padding: 18px 20px !important; }



/* ── INFO ROWS ── */

.info-row {

  display: flex; align-items: center; justify-content: space-between;

  padding: 9px 0; border-bottom: 1px solid #F5F0FF;

}

.info-row:last-child { border-bottom: none; }

.info-label { font-size: 0.74rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-muted); }

.info-value { font-size: 0.88rem; font-weight: 700; color: var(--text); }

.count-badge {

  font-family: 'Baloo 2', cursive; font-size: 1.1rem; font-weight: 800;

  color: var(--violet);

}



/* ── TABLE ── */

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

.table tbody td { padding: 12px 16px !important; font-size: 0.86rem; vertical-align: middle !important; border: none !important; }



/* ── AVATAR ── */

.table-avatar {

  width: 34px; height: 34px; border-radius: 9px; flex-shrink: 0;

  display: flex; align-items: center; justify-content: center;

  font-weight: 900; font-size: 0.82rem; color: white;

}

tbody tr:nth-child(5n+1) .table-avatar { background: linear-gradient(135deg, var(--coral),  var(--peach)); }

tbody tr:nth-child(5n+2) .table-avatar { background: linear-gradient(135deg, var(--teal),   var(--sky));   }

tbody tr:nth-child(5n+3) .table-avatar { background: linear-gradient(135deg, var(--violet), var(--coral)); }

tbody tr:nth-child(5n+4) .table-avatar { background: linear-gradient(135deg, var(--mint),   var(--teal));  }

tbody tr:nth-child(5n+5) .table-avatar { background: linear-gradient(135deg, var(--lemon),  var(--peach)); }



.student-link {

  font-weight: 800; font-size: 0.88rem; color: var(--violet);

  text-decoration: none; transition: color 0.15s;

}

.student-link:hover { color: var(--coral); }

.age-text  { font-size: 0.84rem; font-weight: 700; color: var(--text); }

.na-text   { font-size: 0.78rem; font-weight: 700; color: var(--text-muted); font-style: italic; }

.score-display { font-family: 'Baloo 2', cursive; font-size: 1.1rem; font-weight: 800; color: var(--mint); }



/* ── BADGES ── */

.badge { font-size: 0.7rem !important; font-weight: 800 !important; padding: 4px 11px !important; border-radius: 20px !important; }

.badge.bg-success { background: var(--mint-soft)   !important; color: #2a7a50 !important; }

.badge.bg-muted   { background: #F0E8FF             !important; color: var(--text-muted) !important; }



/* ── BUTTONS ── */

.btn { font-family: 'Nunito', sans-serif !important; font-weight: 800 !important; border-radius: 10px !important; transition: all 0.18s !important; }

.btn-xs { font-size: 0.74rem !important; padding: 4px 11px !important; }



.btn-primary-grad {

  background: #845EC2 !important; color: white !important; border: none !important;

  box-shadow: 0 2px 8px rgba(132,94,194,0.25) !important;

}

.btn-primary-grad:hover { background: #6e4aab !important; transform: translateY(-1px); color: white !important; }



.btn-outline-secondary {

  color: var(--text-muted) !important; border: 1.5px solid #E8E0F0 !important; background: white !important;

}

.btn-outline-secondary:hover { background: #F0E8FF !important; color: var(--violet) !important; }



.btn-danger-soft {

  background: var(--coral-soft) !important; color: #c0294a !important; border: none !important;

}

.btn-danger-soft:hover { background: var(--coral) !important; color: white !important; }



/* ── EMPTY STATE ── */

.empty-state { text-align: center; padding: 48px 20px; }

.empty-icon  { font-size: 2.6rem; margin-bottom: 10px; }

.empty-state h5 { font-family: 'Baloo 2', cursive; font-size: 1.1rem; font-weight: 800; color: var(--text); margin-bottom: 5px; }

.empty-state p  { color: var(--text-muted); font-size: 0.88rem; font-weight: 600; }

</style>

@endsection

