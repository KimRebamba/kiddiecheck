@extends('teacher.layout')

@section('content')
<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0">Teacher Dashboard</h1>
</div>

@php
  $inProgress = collect($students)->map(function($s) use ($status) {
    $st = $status[$s->student_id] ?? null;
    return ($st && ($st['in_progress'] ?? null)) ? ['student' => $s, 'test' => $st['in_progress']] : null;
  })->filter();
  $eligibleNow = collect($students)->filter(function($s) use ($status) {
    $st = $status[$s->student_id] ?? null;
    return $st && ($st['eligible'] ?? false);
  });
  $recentCompleted = \App\Models\Test::with(['student'])
    ->where('examiner_id', auth()->id())
    ->where('status', 'completed')
    ->orderByDesc('test_date')
    ->limit(10)
    ->get();
@endphp

<!-- Summary Cards -->
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center">
        <div class="mb-3">
          <div class="text-primary fs-2 mb-2">👥</div>
          <h6 class="text-muted">Assigned Students</h6>
        </div>
        <div class="display-4 fs-1 fw-bold">{{ $students->count() }}</div>
        <div class="text-muted small">Active in your sections</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center">
        <div class="mb-3">
          <div class="text-warning fs-2 mb-2">⏱️</div>
          <h6 class="text-muted">In-Progress Tests</h6>
        </div>
        <div class="display-4 fs-1 fw-bold text-warning">{{ $inProgress->count() }}</div>
        <div class="text-muted small">Tests currently active</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center">
        <div class="mb-3">
          <div class="text-success fs-2 mb-2">✅</div>
          <h6 class="text-muted">Eligible Now</h6>
        </div>
        <div class="display-4 fs-1 fw-bold text-success">{{ $eligibleNow->count() }}</div>
        <div class="text-muted small">Ready for new tests</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center">
        <div class="mb-3">
          <div class="text-info fs-2 mb-2">📊</div>
          <h6 class="text-muted">Completed Tests</h6>
        </div>
        <div class="display-4 fs-1 fw-bold text-info">{{ $recentCompleted->count() }}</div>
        <div class="text-muted small">Last 30 days</div>
      </div>
    </div>
  </div>
</div>

<!-- Assigned Students Table -->
<div class="row g-3">
  <div class="col-12">
    <div class="card border-0 shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Assigned Students</h5>
        <span class="badge bg-primary rounded-pill">{{ $students->count() }}</span>
      </div>
      <div class="card-body p-0">
        @if($students->isEmpty())
          <div class="text-center text-muted py-4">
            <div class="fs-1 mb-2">📚</div>
            <div class="h6">No students assigned</div>
            <p class="text-muted">Students will appear here once they are assigned to your sections.</p>
          </div>
        @else
          <div class="table-responsive">
            <table class="table table-hover">
              <thead class="table-light">
                <tr>
                  <th><i class="fas fa-user me-2"></i> Student</th>
                  <th><i class="fas fa-birthday-cake me-2"></i> Age</th>
                  <th><i class="fas fa-calendar me-2"></i> Latest Test</th>
                  <th><i class="fas fa-info-circle me-2"></i> Status</th>
                  <th class="text-center"><i class="fas fa-cogs me-2"></i> Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($students as $s)
                  @php 
                    $st = $status[$s->student_id] ?? null;
                    $latest = $st['latest_teacher'] ?? null;
                    $dob = is_string($s->date_of_birth) ? \Carbon\Carbon::parse($s->date_of_birth) : $s->date_of_birth;
                    $age = $dob ? (int)$dob->diffInYears(now()) : 'N/A';
                  @endphp
                  <tr>
                    <td>
                      <a href="{{ route('teacher.student', $s->student_id) }}" class="text-decoration-none d-flex align-items-center">
                        <div class="avatar bg-primary text-white me-2">
                          {{ strtoupper(substr($s->first_name, 0, 1)) }}
                        </div>
                        <div>
                          <strong>{{ $s->first_name }} {{ $s->last_name }}</strong>
                        <br>
                          <small class="text-muted">{{ $age }} years old</small>
                        </div>
                      </a>
                    </td>
                    <td class="text-center">{{ $age }}</td>
                    <td>
                      @if($latest)
                        {{ $latest->test_date->format('M d, Y') }}<br>
                        <small class="badge bg-{{ $latest->status === 'completed' ? 'success' : ($latest->status === 'in_progress' ? 'warning' : 'secondary') }} rounded-pill">{{ ucfirst($latest->status) }}</small>
                      @else
                        <span class="text-muted">No tests</span>
                      @endif
                    </td>
                    <td>
                      @if($st['in_progress'])
                        <span class="badge bg-warning rounded-pill">In Progress</span>
                      @elseif($st['eligible'])
                        <span class="badge bg-success rounded-pill">Eligible</span>
                      @else
                        <span class="badge bg-secondary rounded-pill">Not Eligible</span>
                      @endif
                    </td>
                    <td class="text-center">
                      <div class="btn-group btn-group-sm" role="group">
                        <a href="{{ route('teacher.student', $s->student_id) }}" class="btn btn-sm btn-outline-primary">
                          <i class="fas fa-eye me-1"></i> View
                        </a>
                        @if($st['in_progress'])
                          <a href="{{ route('teacher.tests.question', [$st['in_progress']->test_id, \App\Models\Domain::orderBy('domain_id')->first()->domain_id ?? 1, 0]) }}" class="btn btn-sm btn-outline-warning">
                            <i class="fas fa-play me-1"></i> Continue
                          </a>
                        @elseif($st['eligible'])
                          @php 
                            $availablePeriod = $s->assessmentPeriods()
                                ->where('status', '!=', 'overdue')
                                ->where('status', '!=', 'completed')
                                ->first();
                          @endphp
                          @if($availablePeriod)
                            <form action="{{ route('teacher.tests.start', $s->student_id) }}" method="POST" class="d-inline">
                              @csrf
                              <input type="hidden" name="period_id" value="{{ $availablePeriod->period_id }}">
                              <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i> Start Test
                              </button>
                            </form>
                          @else
                            <span class="text-muted small">No periods available</span>
                          @endif
                        @endif
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- In-Progress Tests -->
@if($inProgress->count() > 0)
  <div class="col-12 col-lg-6">
        <div class="card-header">
          <h5 class="mb-0">In-Progress Assessments</h5>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>Student</th>
                  <th>Started</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($inProgress as $ip)
                  <tr>
                    <td>{{ $ip['student']->first_name }} {{ $ip['student']->last_name }}</td>
                    <td>{{ $ip['test']->test_date->format('M d, Y') }}</td>
                    <td>
                      <div class="btn-group btn-group-sm" role="group">
                        <a href="{{ route('teacher.tests.question', [$ip['test']->test_id, \App\Models\Domain::orderBy('domain_id')->first()->domain_id ?? 1, 0]) }}" class="btn btn-sm btn-outline-warning">Resume</a>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  @endif

  <!-- Eligible Students -->
  @if($eligibleNow->count() > 0)
    <div class="col-12 col-lg-6">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Eligible for Testing</h5>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>Student</th>
                  <th>Last Test</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($eligibleNow as $s)
                  @php 
                    $st = $status[$s->student_id] ?? null;
                    $latest = $st['latest_teacher'] ?? null;
                  @endphp
                  <tr>
                    <td>{{ $s->first_name }} {{ $s->last_name }}</td>
                    <td>{{ $latest ? $latest->test_date->format('M d, Y') : 'N/A' }}</td>
                    <td>
                      @php
                        $availablePeriod = $s->assessmentPeriods()
                            ->where('status', '!=', 'overdue')
                            ->where('status', '!=', 'completed')
                            ->first();
                      @endphp
                      @if($availablePeriod)
                        <form action="{{ route('teacher.tests.start', $s->student_id) }}" method="POST">
                          @csrf
                          <input type="hidden" name="period_id" value="{{ $availablePeriod->period_id }}">
                          <button type="submit" class="btn btn-sm btn-primary">Start Test</button>
                        </form>
                      @else
                        <span class="text-muted small">No available periods</span>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  @endif

  <!-- Recent Completed Tests -->
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Recent Completed Tests</h5>
      </div>
      <div class="card-body p-0">
        @if($recentCompleted->isEmpty())
          <div class="p-3 text-muted">No recent completed tests.</div>
        @else
          <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>Student</th>
                  <th>Date</th>
                  <th>Score</th>
                  <th>Interpretation</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($recentCompleted as $t)
                  @php
                    $standardScore = $t->standardScore;
                  @endphp
                  <tr>
                    <td>{{ $t->student->first_name }} {{ $t->student->last_name }}</td>
                    <td>{{ $t->test_date->format('M d, Y') }}</td>
                    <td>{{ $standardScore ? $standardScore->standard_score : 'N/A' }}</td>
                    <td>{{ $standardScore ? $standardScore->interpretation : 'N/A' }}</td>
                    <td>
                      <a href="{{ route('teacher.tests.result', $t->test_id) }}" class="btn btn-sm btn-outline-primary">View</a>
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
  .card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
  }
  .btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
  }
</style>
@endsection