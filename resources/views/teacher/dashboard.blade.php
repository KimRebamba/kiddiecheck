@extends('teacher.layout')

@section('content')
<div class="d-flex align-items-center mb-3">
  <h1 class="h3 mb-0">Teacher Dashboard</h1>
  <div class="ms-auto">
    <a class="btn btn-sm btn-outline-secondary" href="{{ route('index') }}">Home</a>
  </div>
</div>

@php
  $inProgress = collect($students)->map(function($s) use ($status) {
    $st = $status[$s->id] ?? null;
    return ($st && ($st['in_progress'] ?? null)) ? ['student' => $s, 'test' => $st['in_progress']] : null;
  })->filter();
  $eligibleNow = collect($students)->filter(function($s) use ($status) {
    $st = $status[$s->id] ?? null;
    return $st && ($st['eligible'] ?? false);
  });
  $recentCompleted = \App\Models\Test::with(['student'])
    ->where('observer_id', auth()->id())
    ->where('status', 'completed')
    ->orderByDesc('test_date')
    ->limit(10)
    ->get();
@endphp

<div class="row g-3 mb-3">
  <div class="col-6 col-md-3">
    <div class="card text-center">
      <div class="card-body">
        <div class="text-muted">Assigned Children</div>
        <div class="display-6">{{ $students->count() }}</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center">
      <div class="card-body">
        <div class="text-muted">In-Progress Tests</div>
        <div class="display-6">{{ $inProgress->count() }}</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center">
      <div class="card-body">
        <div class="text-muted">Eligible Now</div>
        <div class="display-6">{{ $eligibleNow->count() }}</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center">
      <div class="card-body">
        <div class="text-muted">Recent Completed</div>
        <div class="display-6">{{ $recentCompleted->count() }}</div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-12">
    <div class="card">
      <div class="card-header bg-light">Assigned Children</div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>Child</th>
                <th>Latest Teacher Test</th>
                <th>Longitudinal</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
            @foreach($students as $s)
              @php $st = $status[$s->id] ?? null; $latest = $st['latest_teacher'] ?? null; @endphp
              <tr>
                <td><a href="{{ route('teacher.student', $s->id) }}">{{ $s->name }}</a></td>
                <td>
                  @if($latest)
                    {{ $latest->test_date }} ({{ $latest->status }})
                  @else
                    <span class="text-muted">none</span>
                  @endif
                </td>
                <td class="small">
                  @php $L = $longitudinals[$s->id] ?? null; @endphp
                  @if($L && ($L[1] ?? null))
                    6m: {{ $L[1]['standardScore'] }}
                  @else
                    6m: No data
                  @endif
                  | @if($L && ($L[2] ?? null))
                    12m: {{ $L[2]['standardScore'] }}
                  @else
                    12m: No data
                  @endif
                  | @if($L && ($L[3] ?? null))
                    18m: {{ $L[3]['standardScore'] }}
                  @else
                    18m: No data
                  @endif
                </td>
                <td>
                  @if(($st['in_progress'] ?? null))
                    <span class="badge bg-warning">In progress</span>
                  @elseif(($st['eligible'] ?? false))
                    <span class="badge bg-success">Eligible</span>
                  @else
                    <span class="badge bg-secondary">Not eligible</span>
                  @endif
                </td>
                <td class="d-flex gap-2">
                  @if(($st['in_progress'] ?? null))
                    <a class="btn btn-sm btn-warning" href="{{ route('teacher.tests.question', [($st['in_progress'])->id, \App\Models\Domain::orderBy('id')->first()->id, 0]) }}">Continue</a>
                    <form method="post" action="{{ route('teacher.tests.pause', ($st['in_progress'])->id) }}">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-outline-secondary">Pause</button>
                    </form>
                    <form method="post" action="{{ route('teacher.tests.cancel', ($st['in_progress'])->id) }}">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                    </form>
                  @elseif(($st['eligible'] ?? false))
                    <form method="post" action="{{ route('teacher.tests.start', $s->id) }}">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-primary">Start Test</button>
                    </form>
                  @else
                    <span class="text-muted">Next test after 6 months</span>
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

  <div class="col-12 col-lg-6">
    <div class="card">
      <div class="card-header bg-light">In-Progress Tests</div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead>
              <tr>
                <th>Child</th>
                <th>Started</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($inProgress as $ip)
                <tr>
                  <td>{{ $ip['student']->name }}</td>
                  <td>{{ $ip['test']->test_date }}</td>
                  <td class="d-flex gap-2">
                    <a class="btn btn-sm btn-warning" href="{{ route('teacher.tests.question', [$ip['test']->id, \App\Models\Domain::orderBy('id')->first()->id, 0]) }}">Continue</a>
                    <form method="post" action="{{ route('teacher.tests.pause', $ip['test']->id) }}">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-outline-secondary">Pause</button>
                    </form>
                    <form method="post" action="{{ route('teacher.tests.cancel', $ip['test']->id) }}">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                    </form>
                    <form method="post" action="{{ route('teacher.tests.terminate', $ip['test']->id) }}">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-outline-warning">Terminate</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr><td colspan="3" class="text-muted">No in-progress tests</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-6">
    <div class="card">
      <div class="card-header bg-light">Eligible Students</div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead>
              <tr>
                <th>Child</th>
                <th>Last Completed</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse($eligibleNow as $s)
                @php $st = $status[$s->id] ?? null; $latest = $st['latest_teacher'] ?? null; @endphp
                <tr>
                  <td>{{ $s->name }}</td>
                  <td>{{ $latest ? ($latest->status === 'completed' ? $latest->test_date : '—') : '—' }}</td>
                  <td>
                    <form method="post" action="{{ route('teacher.tests.start', $s->id) }}">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-primary">Start Test</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr><td colspan="3" class="text-muted">No students eligible right now</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12">
    <div class="card">
      <div class="card-header bg-light">Recent Completed Tests</div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead>
              <tr>
                <th>Child</th>
                <th>Date</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentCompleted as $t)
                <tr>
                  <td>{{ $t->student->name }}</td>
                  <td>{{ $t->test_date }}</td>
                  <td><a class="btn btn-sm btn-outline-primary" href="{{ route('teacher.tests.result', $t->id) }}">View Result</a></td>
                </tr>
              @empty
                <tr><td colspan="3" class="text-muted">No recent results</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
