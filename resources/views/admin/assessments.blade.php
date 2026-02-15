@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h1 class="h4 mb-1">Assessments</h1>
    <p class="text-muted mb-0">Monitor assessment periods, progress, and scoring health.</p>
  </div>
</div>

{{-- Top summary --}}
<div class="row g-3 mb-3">
  <div class="col-6 col-md-4 col-xl-2">
    <div class="card shadow-sm h-100">
      <div class="card-body py-2">
        <div class="text-muted small">Total Periods</div>
        <div class="h5 mb-0">{{ $totalPeriods }}</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="card shadow-sm h-100">
      <div class="card-body py-2">
        <div class="text-muted small">Scheduled</div>
        <div class="h5 mb-0">{{ $scheduledPeriods }}</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="card shadow-sm h-100">
      <div class="card-body py-2">
        <div class="text-muted small">Ongoing (tests)</div>
        <div class="h5 mb-0">{{ $ongoingAssessments }}</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="card shadow-sm h-100">
      <div class="card-body py-2">
        <div class="text-muted small">Completed</div>
        <div class="h5 mb-0">{{ $completedPeriods }}</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="card shadow-sm h-100">
      <div class="card-body py-2">
        <div class="text-muted small">Overdue</div>
        <div class="h5 mb-0 text-danger">{{ $overduePeriods }}</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="card shadow-sm h-100">
      <div class="card-body py-2">
        <div class="text-muted small">Tests awaiting finalization</div>
        <div class="h5 mb-0">{{ $testsAwaitingFinalization }}</div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-12 col-xl-9">
    {{-- Filters --}}
    <div class="card mb-3">
      <div class="card-body py-2">
        <form method="get" class="row g-2 align-items-end">
          <div class="col-6 col-md-3">
            <label class="form-label form-label-sm">Status</label>
            <select name="status" class="form-select form-select-sm">
              <option value="">All</option>
              <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
              <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
              <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
              <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
            </select>
          </div>
          <div class="col-6 col-md-3">
            <label class="form-label form-label-sm">Teacher</label>
            <select name="teacher_id" class="form-select form-select-sm">
              <option value="">All</option>
              @foreach($teacherOptions as $t)
                <option value="{{ $t->user_id }}" {{ (string)request('teacher_id') === (string)$t->user_id ? 'selected' : '' }}>
                  {{ $t->last_name }}, {{ $t->first_name }} ({{ $t->username }})
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-6 col-md-3">
            <label class="form-label form-label-sm">Student name</label>
            <input type="text" name="student_name" value="{{ request('student_name') }}" class="form-control form-control-sm">
          </div>
          <div class="col-6 col-md-3">
            <label class="form-label form-label-sm">Family name</label>
            <input type="text" name="family_name" value="{{ request('family_name') }}" class="form-control form-control-sm">
          </div>
          <div class="col-6 col-md-3">
            <label class="form-label form-label-sm">Start from</label>
            <input type="date" name="start_from" value="{{ request('start_from') }}" class="form-control form-control-sm">
          </div>
          <div class="col-6 col-md-3">
            <label class="form-label form-label-sm">Start to</label>
            <input type="date" name="start_to" value="{{ request('start_to') }}" class="form-control form-control-sm">
          </div>
          <div class="col-12 col-md-3">
            <div class="form-check form-check-sm">
              <input class="form-check-input" type="checkbox" name="with_discrepancies" id="with_discrepancies" value="1" {{ request()->boolean('with_discrepancies') ? 'checked' : '' }}>
              <label class="form-check-label" for="with_discrepancies">With discrepancies only</label>
            </div>
            <div class="form-check form-check-sm">
              <input class="form-check-input" type="checkbox" name="missing_teacher_test" id="missing_teacher_test" value="1" {{ request()->boolean('missing_teacher_test') ? 'checked' : '' }}>
              <label class="form-check-label" for="missing_teacher_test">Missing teacher test</label>
            </div>
            <div class="form-check form-check-sm">
              <input class="form-check-input" type="checkbox" name="missing_family_test" id="missing_family_test" value="1" {{ request()->boolean('missing_family_test') ? 'checked' : '' }}>
              <label class="form-check-label" for="missing_family_test">Missing family test</label>
            </div>
          </div>
          <div class="col-12 col-md-3 mt-2 mt-md-0 text-md-end">
            <button type="submit" class="btn btn-outline-secondary btn-sm">Apply Filters</button>
          </div>
        </form>
      </div>
    </div>

    {{-- Periods table --}}
    <div class="card">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th>Student</th>
                <th>Family</th>
                <th>Teachers</th>
                <th>Period</th>
                <th>Start</th>
                <th>End</th>
                <th>Status</th>
                <th>Teacher tests</th>
                <th>Family test</th>
                <th>Final score</th>
                <th>Last activity</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($periods as $p)
                <tr>
                  <td>{{ $p->student_last_name }}, {{ $p->student_first_name }}</td>
                  <td>{{ $p->family_name ?? '—' }}</td>
                  <td>
                    @php $assigned = $p->assigned_teachers; @endphp
                    @if($assigned->isEmpty())
                      <span class="text-muted">None</span>
                    @else
                      <span>
                        {{ $assigned->take(2)->map(fn($t) => trim(($t->first_name ?? '').' '.($t->last_name ?? '')) ?: $t->username)->implode(', ') }}
                        @if($assigned->count() > 2)
                          <span class="text-muted">+{{ $assigned->count() - 2 }} more</span>
                        @endif
                      </span>
                    @endif
                  </td>
                  <td>{{ $p->description }}</td>
                  <td>{{ $p->start_date }}</td>
                  <td>{{ $p->end_date }}</td>
                  <td>
                    @php $status = $p->computed_status; @endphp
                    @if($status === 'overdue')
                      <span class="badge bg-danger">Overdue</span>
                    @elseif($status === 'completed')
                      <span class="badge bg-success">Completed</span>
                    @elseif($status === 'ongoing')
                      <span class="badge bg-warning text-dark">Ongoing</span>
                    @elseif($status === 'scheduled')
                      <span class="badge bg-info text-dark">Scheduled</span>
                    @else
                      <span class="badge bg-secondary">Other</span>
                    @endif
                  </td>
                  <td>{{ $p->teacher_progress_label }}</td>
                  <td>{{ $p->family_status_label }}</td>
                  <td>
                    @if($p->final_score_status === 'Computed')
                      <span class="badge bg-success">Computed</span>
                    @else
                      <span class="badge bg-secondary">Not computed</span>
                    @endif
                  </td>
                  <td>{{ $p->last_activity ? $p->last_activity->format('Y-m-d') : '—' }}</td>
                  <td class="text-end">
                    <a href="{{ route('admin.assessments.show', $p->period_id) }}" class="btn btn-outline-secondary btn-sm">View</a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="12" class="text-center text-muted py-3">No assessment periods found.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="p-2">
          {{ $periods->links() }}
        </div>
      </div>
    </div>
  </div>

  {{-- Alerts panel --}}
  <div class="col-12 col-xl-3">
    <div class="card mb-3">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Alerts & Problems</h2>
      </div>
      <div class="card-body small">
        <h3 class="h6">Overdue assessment periods</h3>
        @if($alerts['overdue']->isEmpty())
          <p class="text-muted">None.</p>
        @else
          <ul class="list-group list-group-flush mb-2">
            @foreach($alerts['overdue'] as $a)
              <li class="list-group-item px-0">{{ $a->student_name }} · {{ $a->period_description }} (ended {{ $a->end_date }})</li>
            @endforeach
          </ul>
        @endif

        <h3 class="h6 mt-3">Missing teacher evaluations</h3>
        @if($alerts['missing_teacher']->isEmpty())
          <p class="text-muted">All periods have at least one completed teacher test.</p>
        @else
          <ul class="list-group list-group-flush mb-2">
            @foreach($alerts['missing_teacher'] as $a)
              <li class="list-group-item px-0">{{ $a->student_name }} · {{ $a->period_description }}</li>
            @endforeach
          </ul>
        @endif

        <h3 class="h6 mt-3">Family test not completed</h3>
        @if($alerts['missing_family']->isEmpty())
          <p class="text-muted">All periods have a completed family test.</p>
        @else
          <ul class="list-group list-group-flush mb-2">
            @foreach($alerts['missing_family'] as $a)
              <li class="list-group-item px-0">{{ $a->student_name }} · {{ $a->period_description }}</li>
            @endforeach
          </ul>
        @endif

        <h3 class="h6 mt-3">Major scoring discrepancies</h3>
        @if($alerts['major_discrepancy']->isEmpty())
          <p class="text-muted">No major discrepancies detected.</p>
        @else
          <ul class="list-group list-group-flush mb-2">
            @foreach($alerts['major_discrepancy'] as $a)
              <li class="list-group-item px-0">
                {{ $a->student_name }} · {{ $a->period_description }}
              </li>
            @endforeach
          </ul>
        @endif

        <h3 class="h6 mt-3">Tests stuck in progress</h3>
        @if($alerts['stuck_tests']->isEmpty())
          <p class="text-muted mb-0">No long-running in-progress tests.</p>
        @else
          <ul class="list-group list-group-flush mb-0">
            @foreach($alerts['stuck_tests'] as $a)
              <li class="list-group-item px-0">{{ $a->student_name }} · {{ $a->period_description }} (since {{ $a->test_date }})</li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
