@extends('teacher.layout')

@section('content')
<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0">Assessment Results - {{ $test->student->first_name }} {{ $test->student->last_name }}</h1>
  <div class="ms-auto">
    <a href="{{ route('teacher.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
  </div>
</div>

<div class="row g-3">
  <!-- Test Info -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-3">
            <p class="text-muted mb-1">Test Date</p>
            <p class="fw-semibold">{{ $test->test_date->format('M d, Y') }}</p>
          </div>
          <div class="col-md-3">
            <p class="text-muted mb-1">Period</p>
            <p class="fw-semibold">{{ optional($test->assessmentPeriod)->description ?? 'N/A' }}</p>
          </div>
          <div class="col-md-3">
            <p class="text-muted mb-1">Status</p>
            <p>
              <span class="badge bg-{{ 
                $test->status === 'finalized' ? 'success' : 
                ($test->status === 'completed' ? 'info' : 'warning')
              }}">
                {{ ucfirst($test->status) }}
              </span>
            </p>
          </div>
          <div class="col-md-3">
            <p class="text-muted mb-1">Standard Score</p>
            <p class="fw-semibold">{{ $standardScore ?? 'Not calculated' }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Domain Scores -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Domain Scores</h5>
      </div>
      <div class="card-body p-0">
        @if($test->domainScores->isEmpty())
          <p class="p-3 text-muted">No domain scores yet.</p>
        @else
          <div class="table-responsive">
            <table class="table table-sm">
              <thead class="table-light">
                <tr>
                  <th>Domain</th>
                  <th>Raw Score</th>
                  <th>Scaled Score</th>
                  <th>Progress</th>
                </tr>
              </thead>
              <tbody>
                @foreach($test->domainScores as $score)
                  <tr>
                    <td><strong>{{ $score->domain->name ?? 'Unknown' }}</strong></td>
                    <td>{{ $score->raw_score ?? 'N/A' }}</td>
                    <td>{{ $score->scaled_score ?? 'N/A' }}</td>
                    <td>
                      @if($score->scaled_score)
                        <div class="progress" style="height: 20px;">
                          @php
                            $percentage = min(100, ($score->scaled_score / 19) * 100);
                          @endphp
                          <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                            {{ $score->scaled_score }}
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
      </div>
    </div>
  </div>

  <!-- Summary -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Assessment Summary</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <p class="text-muted mb-1">Sum of Scaled Scores</p>
            <p class="display-6">{{ $sumScaled }}</p>
          </div>
          <div class="col-md-6">
            <p class="text-muted mb-1">Standard Score</p>
            <p class="display-6">{{ $standardScore ?? 'N/A' }}</p>
          </div>
        </div>
        @if($interpretation)
          <p class="mt-3">
            <strong>Interpretation:</strong>
            <span class="badge bg-info">{{ $interpretation }}</span>
          </p>
        @endif
      </div>
    </div>
  </div>

  <!-- Actions -->
  <div class="col-md-12">
    <div class="d-flex gap-2">
      @if($test->status === 'completed')
        <form action="{{ route('teacher.tests.finalize', $test->test_id) }}" method="POST">
          @csrf
          <button type="submit" class="btn btn-success">Finalize Test</button>
        </form>
        <form action="{{ route('teacher.tests.cancel', $test->test_id) }}" method="POST">
          @csrf
          <button type="submit" class="btn btn-outline-danger">Cancel Test</button>
        </form>
      @elseif($test->status === 'finalized')
        <span class="badge bg-success" style="padding: 0.5rem 1rem;">Test Finalized</span>
      @endif

      <a href="{{ route('teacher.index') }}" class="btn btn-outline-secondary">Back to Dashboard</a>
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
