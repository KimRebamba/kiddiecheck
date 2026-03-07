@extends('teacher.layout')

@section('content')

<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0 fw-bold">Reports</h1>
</div>

<div class="card">
  <div class="card-header">
    <h5 class="card-title mb-0">
      <i class="fas fa-chart-bar me-2"></i>Select Assessment to Review
    </h5>
  </div>
  <div class="card-body p-0">

    @if($tests->isEmpty())
      <div class="empty-state">
        <div class="empty-icon">📋</div>
        <h5>No Finalized Assessments</h5>
        <p>There are no finalized assessments to review yet.</p>
      </div>
    @else
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Student</th>
              <th>Period</th>
              <th>Test Date</th>
              <th>Status</th>
              <th>Score</th>
              <th>Interpretation</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($tests as $test)
              @php $standardScore = $test->standardScore; @endphp
              <tr>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="table-avatar">{{ strtoupper(substr($test->student->first_name, 0, 1)) }}</div>
                    <span class="fw-semibold">{{ $test->student->first_name }} {{ $test->student->last_name }}</span>
                  </div>
                </td>
                <td><span class="period-text">{{ $test->assessmentPeriod->description }}</span></td>
                <td><span class="date-text">{{ $test->test_date->format('M d, Y') }}</span></td>
                <td><span class="badge bg-success">{{ ucfirst($test->status) }}</span></td>
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
                  <a href="{{ route('teacher.reports.detail', [$test->student_id, $test->period_id, $test->test_id]) }}"
                     class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-eye me-1"></i>View Details
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif

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

/* ── PAGE TITLE ── */
.h3.fw-bold {
  font-family: 'Baloo 2', cursive !important;
  font-size: 1.7rem !important;
  background: linear-gradient(135deg, var(--violet), var(--coral));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* ── CARD ── */
.card {
  border: none !important;
  border-radius: var(--radius) !important;
  box-shadow: var(--shadow) !important;
  overflow: hidden;
  animation: fadeUp 0.4s ease both;
}
@keyframes fadeUp { from { opacity:0; transform:translateY(14px); } to { opacity:1; transform:translateY(0); } }

.card-header {
  background: #FDFBFF !important;
  border-bottom: 2px solid #F0E8FF !important;
  padding: 14px 20px !important;
}
.card-title {
  font-family: 'Baloo 2', cursive !important;
  font-size: 1rem !important; font-weight: 700 !important; color: var(--text) !important;
}
.card-header .fa-chart-bar { color: var(--violet); }

/* ── TABLE ── */
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
.table tbody td { padding: 13px 16px !important; font-size: 0.875rem; vertical-align: middle !important; border: none !important; }

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

.fw-semibold { font-weight: 800 !important; font-size: 0.88rem; color: var(--text); }
.period-text { font-size: 0.83rem; font-weight: 700; color: var(--text); }
.date-text   { font-size: 0.83rem; font-weight: 700; color: var(--text); }
.na-text     { font-size: 0.78rem; font-weight: 700; color: var(--text-muted); font-style: italic; }

/* ── SCORE ── */
.score-badge {
  font-family: 'Baloo 2', cursive;
  font-size: 1.15rem; font-weight: 800; color: var(--mint);
}

/* ── STATUS BADGE ── */
.badge { font-size: 0.71rem !important; font-weight: 800 !important; padding: 4px 11px !important; border-radius: 20px !important; }
.badge.bg-success { background: var(--mint-soft) !important; color: #2a7a50 !important; }

/* ── INTERPRETATION BADGES ── */
.interp-badge {
  display: inline-block; font-size: 0.72rem; font-weight: 800;
  padding: 4px 11px; border-radius: 20px; white-space: nowrap;
}
.interp-advanced-development { background: var(--mint-soft);   color: #2a7a50; }
.interp-average-development  { background: var(--sky-soft);    color: #2260a0; }
.interp-below-average        { background: var(--lemon-soft);  color: #9a6800; }
.interp-delayed-development  { background: var(--coral-soft);  color: #c0294a; }
.interp-badge:not([class*="interp-advanced"]):not([class*="interp-average"]):not([class*="interp-below"]):not([class*="interp-delayed"]) {
  background: var(--violet-soft); color: #5a3e8a;
}

/* ── BUTTON ── */
.btn { font-family: 'Nunito', sans-serif !important; font-weight: 800 !important; border-radius: 10px !important; transition: all 0.18s !important; font-size: 0.78rem !important; }
.btn-outline-primary {
  color: var(--violet) !important; border: 1.5px solid var(--violet-soft) !important; background: white !important;
}
.btn-outline-primary:hover { background: var(--violet-soft) !important; transform: translateY(-1px); }

/* ── EMPTY STATE ── */
.empty-state {
  text-align: center; padding: 52px 20px;
}
.empty-icon { font-size: 2.8rem; margin-bottom: 10px; }
.empty-state h5 { font-family: 'Baloo 2', cursive; font-size: 1.1rem; font-weight: 800; color: var(--text); margin-bottom: 5px; }
.empty-state p  { color: var(--text-muted); font-size: 0.88rem; font-weight: 600; }
</style>

@endsection