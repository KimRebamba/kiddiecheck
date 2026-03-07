@extends('teacher.layout')

@section('content')

<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0 fw-bold">ECCD Overview</h1>
</div>

@if(session('success'))
  <div class="alert alert-success-custom">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
  </div>
@endif
@if(session('error'))
  <div class="alert alert-danger-custom">
    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
  </div>
@endif

@if($students->isEmpty())
  <div class="empty-state">
    <div class="empty-icon">📊</div>
    <h5>No ECCD Data Yet</h5>
    <p>No assigned students with finalized ECCD assessments yet.</p>
  </div>
@else

  {{-- ── SUMMARY TABLE ── --}}
  <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="card-title mb-0">
        <i class="fas fa-table me-2"></i>Student ECCD Summary
      </h5>
      @php
        $user = $teacher;
        $teacherProfile = $user->teacher ?? null;
        $displayName = $teacherProfile && ($teacherProfile->first_name || $teacherProfile->last_name)
          ? trim(($teacherProfile->first_name ?? '').' '.($teacherProfile->last_name ?? ''))
          : ($user->username ?? $user->email ?? '');
      @endphp
      <span class="teacher-chip">
        <i class="fas fa-chalkboard-teacher me-1"></i>{{ $displayName }}
      </span>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th>Student</th>
              <th>Section</th>
              <th>Latest Period</th>
              <th>Test Date</th>
              <th>Score</th>
              <th>Interpretation</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($students as $student)
              @php
                $latestTest    = $student->tests->sortByDesc('test_date')->first();
                $standardScore = $latestTest?->standardScore;
                $latestPeriod  = $latestTest?->assessmentPeriod;
              @endphp
              <tr>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="table-avatar">{{ strtoupper(substr($student->first_name, 0, 1)) }}</div>
                    <div>
                      <div class="fw-semibold">{{ $student->first_name }} {{ $student->last_name }}</div>
                      <div class="text-muted small">ID: {{ $student->student_id }}</div>
                    </div>
                  </div>
                </td>
                <td>
                  @if($student->section)
                    <span class="badge bg-section">{{ $student->section->name }}</span>
                  @else
                    <span class="na-text">Unassigned</span>
                  @endif
                </td>
                <td>
                  @if($latestPeriod)
                    <span class="period-text">{{ $latestPeriod->description }}</span>
                  @else
                    <span class="na-text">N/A</span>
                  @endif
                </td>
                <td>
                  @if($latestTest)
                    <span class="date-text">{{ $latestTest->test_date->format('M d, Y') }}</span>
                  @else
                    <span class="na-text">N/A</span>
                  @endif
                </td>
                <td>
                  @if($standardScore)
                    <span class="score-badge">{{ $standardScore->standard_score }}</span>
                  @else
                    <span class="na-text">N/A</span>
                  @endif
                </td>
                <td>
                  @if($standardScore)
                    <span class="interp-badge interp-{{ Str::slug($standardScore->interpretation) }}">
                      {{ $standardScore->interpretation }}
                    </span>
                  @else
                    <span class="na-text">N/A</span>
                  @endif
                </td>
                <td>
                  @if($latestPeriod && $latestTest)
                    <a href="{{ route('teacher.reports.detail', [$student->student_id, $latestPeriod->period_id, $latestTest->test_id]) }}"
                       class="btn btn-sm btn-outline-primary">
                      <i class="fas fa-eye me-1"></i>View Details
                    </a>
                  @else
                    <span class="na-text">No finalized test</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- ── PER-STUDENT LONGITUDINAL CARDS ── --}}
  @foreach($students as $student)
    @php $tests = $student->tests->sortBy('test_date'); @endphp
    @if($tests->isNotEmpty())
      <div class="card mb-4 student-longitudinal-card">
        <div class="card-header">
          <div class="d-flex align-items-center gap-3">
            <div class="student-avatar">{{ strtoupper(substr($student->first_name, 0, 1)) }}</div>
            <div>
              <h5 class="card-title mb-0">{{ $student->first_name }} {{ $student->last_name }}</h5>
              <small class="card-subtitle">Longitudinal ECCD Scores</small>
            </div>
          </div>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm table-hover align-middle">
              <thead>
                <tr>
                  <th>Period</th>
                  <th>Test Date</th>
                  <th>Score</th>
                  <th>Interpretation</th>
                </tr>
              </thead>
              <tbody>
                @foreach($tests as $test)
                  @php $score = $test->standardScore; @endphp
                  <tr>
                    <td><span class="period-text">{{ $test->assessmentPeriod?->description ?? 'N/A' }}</span></td>
                    <td><span class="date-text">{{ $test->test_date?->format('M d, Y') ?? 'N/A' }}</span></td>
                    <td>
                      @if($score)
                        <span class="score-badge">{{ $score->standard_score }}</span>
                      @else
                        <span class="na-text">N/A</span>
                      @endif
                    </td>
                    <td>
                      @if($score)
                        <span class="interp-badge interp-{{ Str::slug($score->interpretation) }}">
                          {{ $score->interpretation }}
                        </span>
                      @else
                        <span class="na-text">N/A</span>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    @endif
  @endforeach

@endif

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

/* ── PAGE TITLE ── */
.h3.fw-bold {
  font-family: 'Baloo 2', cursive !important;
  font-size: 1.7rem !important;
  background: linear-gradient(135deg, var(--violet), var(--coral));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* ── ALERTS ── */
.alert-success-custom {
  background: var(--mint-soft); border-left: 4px solid var(--mint);
  color: #1a6640; border-radius: 10px; padding: 12px 16px;
  font-weight: 700; font-size: 0.88rem; margin-bottom: 16px;
}
.alert-danger-custom {
  background: var(--coral-soft); border-left: 4px solid var(--coral);
  color: #a0203a; border-radius: 10px; padding: 12px 16px;
  font-weight: 700; font-size: 0.88rem; margin-bottom: 16px;
}

/* ── CARDS ── */
.card {
  border: none !important;
  border-radius: var(--radius) !important;
  box-shadow: var(--shadow) !important;
  overflow: hidden;
  animation: fadeUp 0.4s ease both;
  transition: transform 0.2s, box-shadow 0.2s;
}
.card:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(100,60,160,0.13) !important; }
@keyframes fadeUp { from { opacity:0; transform:translateY(14px); } to { opacity:1; transform:translateY(0); } }

/* ── CARD HEADERS ── */
.card-header {
  background: #FDFBFF !important;
  border-bottom: 2px solid #F0E8FF !important;
  padding: 14px 20px !important;
}
.card-title {
  font-family: 'Baloo 2', cursive !important;
  font-size: 1rem !important; font-weight: 700 !important; color: var(--text) !important;
}
.card-subtitle {
  font-size: 0.75rem; font-weight: 700; color: var(--text-muted);
  display: block; margin-top: 2px;
}
.card-header .fa-table         { color: var(--violet); }
.card-header .fa-chalkboard-teacher { color: var(--teal); }

.teacher-chip {
  font-size: 0.76rem; font-weight: 700; color: var(--text-muted);
  background: var(--violet-soft); padding: 4px 12px;
  border-radius: 20px; white-space: nowrap;
}

/* ── LONGITUDINAL CARD TOP BAR ── */
.student-longitudinal-card { }
.student-longitudinal-card:nth-child(6n+1) .card-header { border-left: 4px solid var(--violet); }
.student-longitudinal-card:nth-child(6n+2) .card-header { border-left: 4px solid var(--teal);   }
.student-longitudinal-card:nth-child(6n+3) .card-header { border-left: 4px solid var(--coral);  }
.student-longitudinal-card:nth-child(6n+4) .card-header { border-left: 4px solid var(--mint);   }
.student-longitudinal-card:nth-child(6n+5) .card-header { border-left: 4px solid var(--lemon);  }
.student-longitudinal-card:nth-child(6n+6) .card-header { border-left: 4px solid var(--sky);    }

/* ── STUDENT AVATAR (longitudinal cards) ── */
.student-avatar {
  width: 42px; height: 42px; border-radius: 12px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  font-family: 'Baloo 2', cursive; font-size: 1.15rem; font-weight: 800; color: white;
  background: linear-gradient(135deg, var(--violet), var(--coral));
}

/* ── TABLE AVATAR (summary table) ── */
.table-avatar {
  width: 34px; height: 34px; border-radius: 9px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  font-weight: 900; font-size: 0.82rem; color: white;
  background: linear-gradient(135deg, var(--violet), var(--coral));
}
tbody tr:nth-child(5n+1) .table-avatar { background: linear-gradient(135deg, var(--coral),  var(--peach)); }
tbody tr:nth-child(5n+2) .table-avatar { background: linear-gradient(135deg, var(--teal),   var(--sky));   }
tbody tr:nth-child(5n+3) .table-avatar { background: linear-gradient(135deg, var(--violet), var(--coral)); }
tbody tr:nth-child(5n+4) .table-avatar { background: linear-gradient(135deg, var(--mint),   var(--teal));  }
tbody tr:nth-child(5n+5) .table-avatar { background: linear-gradient(135deg, var(--lemon),  var(--peach)); }

/* ── TABLES ── */
.table { margin-bottom: 0 !important; }
.table thead th {
  font-size: 0.71rem !important; font-weight: 800 !important;
  text-transform: uppercase; letter-spacing: 0.07em;
  color: var(--text-muted) !important; background: #FDFBFF !important;
  border-bottom: 2px solid #F0E8FF !important;
  padding: 11px 16px !important; white-space: nowrap;
}
.table tbody tr { border-bottom: 1px solid #F9F5FF !important; transition: background 0.15s; }
.table tbody tr:last-child { border-bottom: none !important; }
.table-hover tbody tr:hover { background: #FDFBFF !important; }
.table tbody td { padding: 12px 16px !important; font-size: 0.875rem; vertical-align: middle !important; border: none !important; }
.fw-semibold { font-weight: 800 !important; font-size: 0.88rem; color: var(--text); }
.text-muted.small { font-size: 0.74rem !important; font-weight: 600; color: var(--text-muted) !important; }

/* ── INLINE ELEMENTS ── */
.na-text   { font-size: 0.78rem; font-weight: 700; color: var(--text-muted); font-style: italic; }
.date-text { font-size: 0.83rem; font-weight: 700; color: var(--text); }
.period-text { font-size: 0.83rem; font-weight: 700; color: var(--text); }

/* ── SCORE BADGE ── */
.score-badge {
  font-family: 'Baloo 2', cursive;
  font-size: 1.15rem; font-weight: 800;
  color: var(--mint);
}

/* ── INTERPRETATION BADGES ── */
.interp-badge {
  display: inline-block;
  font-size: 0.72rem; font-weight: 800;
  padding: 4px 11px; border-radius: 20px;
  white-space: nowrap;
}
.interp-advanced-development  { background: var(--mint-soft);   color: #2a7a50; }
.interp-average-development   { background: var(--sky-soft);    color: #2260a0; }
.interp-below-average         { background: var(--lemon-soft);  color: #9a6800; }
.interp-delayed-development   { background: var(--coral-soft);  color: #c0294a; }
/* fallback for any other value */
.interp-badge:not([class*="interp-advanced"]):not([class*="interp-average"]):not([class*="interp-below"]):not([class*="interp-delayed"]) {
  background: var(--violet-soft); color: #5a3e8a;
}

/* ── SECTION BADGE ── */
.badge.bg-section,
.badge { font-size: 0.71rem !important; font-weight: 800 !important; padding: 4px 10px !important; border-radius: 20px !important; }
.badge.bg-section { background: var(--violet-soft) !important; color: #5a3e8a !important; }

/* ── BUTTONS ── */
.btn { font-family: 'Nunito', sans-serif !important; font-weight: 800 !important; border-radius: 10px !important; transition: all 0.18s !important; font-size: 0.78rem !important; }
.btn-outline-primary {
  color: var(--violet) !important; border: 1.5px solid var(--violet-soft) !important; background: white !important;
}
.btn-outline-primary:hover { background: var(--violet-soft) !important; }

/* ── EMPTY STATE ── */
.empty-state {
  text-align: center; padding: 60px 20px;
  background: white; border-radius: var(--radius);
  box-shadow: var(--shadow); animation: fadeUp 0.4s ease;
}
.empty-icon { font-size: 3rem; margin-bottom: 12px; }
.empty-state h5 { font-family: 'Baloo 2', cursive; font-size: 1.2rem; font-weight: 800; color: var(--text); margin-bottom: 6px; }
.empty-state p  { color: var(--text-muted); font-size: 0.9rem; font-weight: 600; }
</style>

@endsection