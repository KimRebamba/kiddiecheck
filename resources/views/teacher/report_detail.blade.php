@extends('teacher.layout')

@section('content')
<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0">Assessment Report</h1>
  <div class="ms-auto">
    <a href="{{ route('teacher.reports') }}" class="btn btn-sm btn-outline-secondary">Back to Reports</a>
  </div>
</div>

<div class="row g-3">
  <!-- Student & Test Info -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <p><strong>Student:</strong> {{ $student->first_name }} {{ $student->last_name }}</p>
            @php
              $dob = is_string($student->date_of_birth) ? \Carbon\Carbon::parse($student->date_of_birth) : $student->date_of_birth;
              $testDate = is_string($test->test_date) ? \Carbon\Carbon::parse($test->test_date) : $test->test_date;
            @endphp
            <p><strong>Date of Birth:</strong> {{ $dob->format('M d, Y') }}</p>
            <p><strong>Age at Test:</strong> {{ $dob->diffInYears($testDate) }} years</p>
          </div>
          <div class="col-md-6">
            <p><strong>Assessment Period:</strong> {{ $period->description }}</p>
            <p><strong>Period Dates:</strong> {{ $period->start_date->format('M d, Y') }} - {{ $period->end_date->format('M d, Y') }}</p>
            <p><strong>Test Date:</strong> {{ $test->test_date->format('M d, Y') }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- A. Test Summary -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header bg-primary" style="background-color: rgba(231, 122, 116, 0.2) !important;">
        <h5 class="mb-0">Test Summary</h5>
      </div>
      <div class="card-body">
        @php
          $standardScore = $test->standardScore;
        @endphp

        @if($test->domainScores && $test->domainScores->count() > 0)
          <h6 class="mb-3">Domain Scores</h6>
          <div class="table-responsive mb-3">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>Domain</th>
                  <th>Raw Score</th>
                  <th>Scaled Score</th>
                  <th style="width: 40%;">Visual</th>
                </tr>
              </thead>
              <tbody>
                @foreach($test->domainScores as $domainScore)
                  <tr>
                    <td><strong>{{ $domainScore->domain->name ?? 'Unknown' }}</strong></td>
                    <td>{{ $domainScore->raw_score ?? 'N/A' }}</td>
                    <td>{{ $domainScore->scaled_score ?? 'N/A' }}</td>
                    <td>
                      @if($domainScore->scaled_score)
                        <div class="progress" style="height: 20px;">
                          @php
                            $percentage = min(100, ($domainScore->scaled_score / 19) * 100);
                          @endphp
                          <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                            {{ $domainScore->scaled_score }}
                          </div>
                        </div>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif

        @if($standardScore)
          <hr>
          <div class="row">
            <div class="col-md-6">
              <p class="mb-2"><strong>Sum of Scaled Scores:</strong> {{ $standardScore->sum_scaled_scores }}</p>
              <p class="mb-2"><strong>Standard Score:</strong> {{ $standardScore->standard_score }}</p>
            </div>
            <div class="col-md-6">
              <p class="mb-2"><strong>Interpretation:</strong> <span class="badge bg-info">{{ $standardScore->interpretation }}</span></p>
            </div>
          </div>
        @else
          <p class="text-muted">Standard score not yet calculated.</p>
        @endif
      </div>
    </div>
  </div>

  <!-- B. Period Summary (if period is completed) -->
  @if($period->status === 'completed')
    <div class="col-md-12">
      <div class="card">
        <div class="card-header bg-primary" style="background-color: rgba(231, 122, 116, 0.2) !important;">
          <h5 class="mb-0">Period Summary</h5>
        </div>
        <div class="card-body">
          @php
            $periodSummary = \App\Models\PeriodSummaryScore::where('period_id', $period->period_id)->first();
          @endphp

          @if($periodSummary)
            <div class="row">
              <div class="col-md-6">
                <p class="mb-2">
                  <strong>Teacher's Avg Score:</strong><br>
                  {{ $periodSummary->teachers_standard_score_avg ?? 'N/A' }}
                </p>
                <p class="mb-2">
                  <strong>Family Score:</strong><br>
                  {{ $periodSummary->family_standard_score ?? 'Not provided' }}
                </p>
              </div>
              <div class="col-md-6">
                <p class="mb-2">
                  <strong>Weighted Final Score:</strong><br>
                  <strong>{{ $periodSummary->final_standard_score ?? 'N/A' }}</strong>
                </p>
                <p class="mb-2">
                  <strong>Final Interpretation:</strong><br>
                  <span class="badge bg-success">{{ $periodSummary->final_interpretation ?? 'N/A' }}</span>
                </p>
              </div>
            </div>

            <hr>

            <div class="row">
              <div class="col-md-6">
                <p class="mb-2">
                  <strong>Teacher Discrepancy:</strong><br>
                  <span class="badge bg-warning">{{ ucfirst($periodSummary->teacher_discrepancy ?? 'none') }}</span>
                </p>
              </div>
              <div class="col-md-6">
                <p class="mb-2">
                  <strong>Teacher-Family Discrepancy:</strong><br>
                  <span class="badge bg-warning">{{ ucfirst($periodSummary->teacher_family_discrepancy ?? 'none') }}</span>
                </p>
              </div>
            </div>
          @else
            <p class="text-muted">Period summary not available yet.</p>
          @endif
        </div>
      </div>
    </div>
  @endif

  <!-- Test Notes -->
  @if($test->notes)
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Notes</h5>
        </div>
        <div class="card-body">
          <p>{{ $test->notes }}</p>
        </div>
      </div>
    </div>
  @endif

  <!-- Actions -->
  <div class="col-md-12">
    <a href="{{ route('teacher.reports') }}" class="btn btn-outline-secondary">Back to Reports</a>
    <!-- PDF download can be added here later -->
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
.col-md-12:nth-child(1) .card { animation-delay:0.05s; }
.col-md-12:nth-child(2) .card { animation-delay:0.10s; }
.col-md-12:nth-child(3) .card { animation-delay:0.15s; }
.col-md-12:nth-child(4) .card { animation-delay:0.20s; }
.col-md-12:nth-child(5) .card { animation-delay:0.25s; }

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
.header-mint   { background: var(--mint-soft)  !important; border-left: 4px solid var(--mint)   !important; }
.header-lemon  { background: var(--lemon-soft) !important; border-left: 4px solid var(--lemon)  !important; }

.section-icon {
  width: 30px; height: 30px; border-radius: 8px;
  display: flex; align-items: center; justify-content: center; font-size: 0.95rem; flex-shrink: 0;
}
.si-violet { background: var(--violet-soft); }
.si-teal   { background: var(--teal-soft);   }
.si-mint   { background: var(--mint-soft);   }
.si-lemon  { background: var(--lemon-soft);  }

/* ── CARD BODY ── */
.card-body { padding: 18px 20px !important; }

/* ── INFO BLOCKS ── */
.info-block {
  background: #FDFBFF; border: 1.5px solid #F0E8FF;
  border-radius: 10px; padding: 12px 14px;
}
.info-block-label {
  font-size: 0.67rem; font-weight: 800; text-transform: uppercase;
  letter-spacing: 0.07em; color: var(--text-muted); margin-bottom: 4px;
}
.info-block-value { font-size: 0.9rem; font-weight: 700; color: var(--text); }

/* ── SUBSECTION ── */
.subsection-title { font-family: 'Baloo 2', cursive; font-size: 0.88rem; font-weight: 800; color: var(--violet); }

/* ── TABLE ── */
.table { margin-bottom: 0 !important; }
.table thead th {
  font-size: 0.7rem !important; font-weight: 800 !important;
  text-transform: uppercase; letter-spacing: 0.07em;
  color: var(--text-muted) !important; background: #FDFBFF !important;
  border-bottom: 2px solid #F0E8FF !important; padding: 10px 14px !important; white-space: nowrap;
}
.table tbody tr { border-bottom: 1px solid #F9F5FF !important; transition: background 0.15s; }
.table tbody tr:last-child { border-bottom: none !important; }
.table tbody tr:hover { background: #FDFBFF !important; }
.table tbody td { padding: 11px 14px !important; font-size: 0.86rem; vertical-align: middle !important; border: none !important; }
.fw-semibold { font-weight: 800 !important; font-size: 0.87rem; color: var(--text); }
.score-sm    { font-family: 'Baloo 2', cursive; font-size: 1rem; font-weight: 800; color: var(--violet); }
.na-text     { font-size: 0.78rem; font-weight: 700; color: var(--text-muted); font-style: italic; }

/* ── DOMAIN PROGRESS BAR ── */
.domain-bar-track {
  height: 22px; background: #F0E8FF; border-radius: 8px; overflow: hidden; position: relative;
}
.domain-bar-fill {
  height: 100%; border-radius: 8px;
  background: linear-gradient(90deg, var(--violet), var(--coral));
  display: flex; align-items: center; justify-content: flex-end;
  padding-right: 8px; min-width: 28px;
  transition: width 0.6s ease;
}
.domain-bar-label { font-size: 0.72rem; font-weight: 900; color: white; }

/* ── SCORE SUMMARY ROW ── */
.score-summary-row {
  display: flex; align-items: center; gap: 0;
  background: #FDFBFF; border: 1.5px solid #F0E8FF;
  border-radius: 12px; overflow: hidden; margin-top: 4px;
}
.score-summary-item { flex: 1; text-align: center; padding: 18px 12px; }
.score-summary-divider { width: 1.5px; background: #F0E8FF; align-self: stretch; }
.score-summary-label { font-size: 0.67rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.07em; color: var(--text-muted); margin-bottom: 6px; }
.score-summary-value {
  font-family: 'Baloo 2', cursive; font-size: 2rem; font-weight: 800; line-height: 1;
}
.score-summary-interp { margin-top: 6px; }
.c-violet { color: var(--violet); }
.c-mint   { color: var(--mint);   }
.c-sky    { color: var(--sky);    }

/* ── PERIOD SUMMARY SCORES ── */
.score-lg { font-family: 'Baloo 2', cursive !important; font-size: 1.5rem !important; font-weight: 800 !important; line-height: 1.1; }

/* ── INTERPRETATION BADGES ── */
.interp-badge {
  display: inline-block; font-size: 0.72rem; font-weight: 800;
  padding: 4px 12px; border-radius: 20px; white-space: nowrap;
}
.interp-advanced-development { background: var(--mint-soft);   color: #2a7a50; }
.interp-average-development  { background: var(--sky-soft);    color: #2260a0; }
.interp-below-average        { background: var(--lemon-soft);  color: #9a6800; }
.interp-delayed-development  { background: var(--coral-soft);  color: #c0294a; }
.interp-badge:not([class*="interp-advanced"]):not([class*="interp-average"]):not([class*="interp-below"]):not([class*="interp-delayed"]) {
  background: var(--violet-soft); color: #5a3e8a;
}

/* ── DISCREPANCY BLOCKS ── */
.disc-block {
  background: #FDFBFF; border: 1.5px solid #F0E8FF;
  border-radius: 10px; padding: 14px 16px;
  display: flex; align-items: center; justify-content: space-between;
}
.disc-block-label { font-size: 0.8rem; font-weight: 800; color: var(--text); }
.disc-badge {
  font-size: 0.72rem; font-weight: 800; padding: 4px 12px; border-radius: 20px;
}
.badge-none  { background: var(--mint-soft);  color: #2a7a50; }
.badge-minor { background: var(--lemon-soft); color: #9a6800; }
.badge-major { background: var(--coral-soft); color: #c0294a; }

/* ── DIVIDER ── */
.modal-divider { height: 2px; background: #F0E8FF; border-radius: 2px; margin: 16px 0; }

/* ── NOTES ── */
.notes-text { font-size: 0.9rem; font-weight: 600; color: var(--text); line-height: 1.7; margin: 0; }
</style>
@endsection
