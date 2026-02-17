@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h1 class="h4 mb-1">Students</h1>
    <p class="text-muted mb-0">Monitor enrollments, assignments, and assessment progress.</p>
  </div>
  <div class="d-flex flex-wrap gap-2">
    <a href="{{ route('admin.students.create') }}" class="btn btn-primary btn-sm">Add New Student</a>
    <a href="{{ route('admin.students.export') }}" class="btn btn-outline-secondary btn-sm">Export List</a>
  </div>
</div>

{{-- Alerts & Warnings --}}
<div class="row g-3 mb-3">
  <div class="col-12 col-lg-6">
    <div class="card h-100">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Overdue Assessments</h2>
      </div>
      <div class="card-body small">
        @if($alerts['overdue']->isEmpty())
          <p class="text-muted mb-0">No students with overdue assessment periods.</p>
        @else
          <ul class="list-group list-group-flush">
            @foreach($alerts['overdue'] as $item)
              <li class="list-group-item px-0 d-flex justify-content-between">
                <span>{{ $item->last_name }}, {{ $item->first_name }} · {{ $item->period_description }}</span>
                <span class="text-muted">Ended {{ $item->end_date }}</span>
              </li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-6">
    <div class="card h-100">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Assignment & Evaluation Alerts</h2>
      </div>
      <div class="card-body small">
        <h3 class="h6">Students without assigned teachers</h3>
        @if($alerts['no_teachers']->isEmpty())
          <p class="text-muted">All students have at least one teacher assigned.</p>
        @else
          <ul class="list-group list-group-flush mb-2">
            @foreach($alerts['no_teachers'] as $item)
              <li class="list-group-item px-0">{{ $item->last_name }}, {{ $item->first_name }}</li>
            @endforeach
          </ul>
        @endif

        <h3 class="h6 mt-3">Missing family evaluations</h3>
        @if($alerts['missing_family_eval']->isEmpty())
          <p class="text-muted">No missing family standard scores detected.</p>
        @else
          <ul class="list-group list-group-flush mb-2">
            @foreach($alerts['missing_family_eval'] as $item)
              <li class="list-group-item px-0">{{ $item->last_name }}, {{ $item->first_name }} · {{ $item->period_description }}</li>
            @endforeach
          </ul>
        @endif

        <h3 class="h6 mt-3">Scheduled periods with no tests</h3>
        @if($alerts['scheduled_no_tests']->isEmpty())
          <p class="text-muted mb-0">All scheduled periods have tests started.</p>
        @else
          <ul class="list-group list-group-flush mb-0">
            @foreach($alerts['scheduled_no_tests'] as $item)
              <li class="list-group-item px-0">{{ $item->last_name }}, {{ $item->first_name }} · {{ $item->period_description }} (starts {{ $item->start_date }})</li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- Search & Filters --}}
<div class="card mb-3">
  <div class="card-body py-2">
    <form method="get" class="row g-2 align-items-end">
      <div class="col-12 col-md-3">
        <label class="form-label form-label-sm">Section</label>
        <select name="section_id" class="form-select form-select-sm">
          <option value="">All sections</option>
          @foreach($sectionOptions as $sec)
            <option value="{{ $sec->section_id }}" {{ (string)request('section_id') === (string)$sec->section_id ? 'selected' : '' }}>
              {{ $sec->name }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label form-label-sm">Student name</label>
        <input type="text" name="student_name" value="{{ request('student_name') }}" class="form-control form-control-sm" placeholder="Search student">
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label form-label-sm">Family name</label>
        <input type="text" name="family_name" value="{{ request('family_name') }}" class="form-control form-control-sm" placeholder="Search family">
      </div>
      <div class="col-6 col-md-2">
        <label class="form-label form-label-sm">Age min</label>
        <input type="number" name="age_min" value="{{ request('age_min') }}" class="form-control form-control-sm" min="0">
      </div>
      <div class="col-6 col-md-2">
        <label class="form-label form-label-sm">Age max</label>
        <input type="number" name="age_max" value="{{ request('age_max') }}" class="form-control form-control-sm" min="0">
      </div>
      <div class="col-12 col-md-2">
        <label class="form-label form-label-sm">Assigned teacher</label>
        <select name="teacher_id" class="form-select form-select-sm">
          <option value="">All</option>
          @foreach($teacherOptions as $t)
            <option value="{{ $t->user_id }}" {{ (string)request('teacher_id') === (string)$t->user_id ? 'selected' : '' }}>
              {{ $t->last_name }}, {{ $t->first_name }} ({{ $t->username }})
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-6 col-md-2">
        <label class="form-label form-label-sm">Assessment status</label>
        <select name="assessment_status" class="form-select form-select-sm">
          <option value="">All</option>
          <option value="overdue" {{ request('assessment_status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
        </select>
      </div>
      <div class="col-6 col-md-2">
        <label class="form-label form-label-sm">Interpretation</label>
        <select name="interpretation" class="form-select form-select-sm">
          <option value="">All</option>
          <option value="advanced" {{ request('interpretation') === 'advanced' ? 'selected' : '' }}>Advanced</option>
          <option value="average" {{ request('interpretation') === 'average' ? 'selected' : '' }}>Average</option>
          <option value="needs_retest" {{ request('interpretation') === 'needs_retest' ? 'selected' : '' }}>Needs Retest</option>
        </select>
      </div>
      <div class="col-12 col-md-3">
        <div class="form-check form-check-sm">
          <input class="form-check-input" type="checkbox" name="with_completed_tests" id="with_completed_tests" value="1" {{ request()->boolean('with_completed_tests') ? 'checked' : '' }}>
          <label class="form-check-label" for="with_completed_tests">With completed tests</label>
        </div>
        <div class="form-check form-check-sm">
          <input class="form-check-input" type="checkbox" name="without_completed_tests" id="without_completed_tests" value="1" {{ request()->boolean('without_completed_tests') ? 'checked' : '' }}>
          <label class="form-check-label" for="without_completed_tests">Without completed tests</label>
        </div>
      </div>
      <div class="col-12 col-md-2 mt-2 mt-md-0 text-md-end">
        <button type="submit" class="btn btn-outline-secondary btn-sm">Apply Filters</button>
      </div>
    </form>
  </div>
</div>

{{-- Bulk actions + list --}}
<form method="post" action="{{ route('admin.students.bulk_assign_teacher') }}">
  @csrf
  <div class="card mb-2">
    <div class="card-body py-2 d-flex flex-wrap gap-2 align-items-center">
      <div class="small fw-semibold">Bulk actions:</div>
      <div>
        <select name="teacher_id" class="form-select form-select-sm d-inline-block" style="min-width: 220px;">
          <option value="">Assign teacher...</option>
          @foreach($teacherOptions as $t)
            <option value="{{ $t->user_id }}">{{ $t->last_name }}, {{ $t->first_name }} ({{ $t->username }})</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-outline-primary btn-sm">Apply to selected</button>
    </div>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-sm mb-0 align-middle">
          <thead class="table-light">
            <tr>
              <th style="width:32px;"><input type="checkbox" onclick="document.querySelectorAll('.student-check').forEach(cb => cb.checked = this.checked);"></th>
              <th style="width:52px;">Photo</th>
              <th>Section</th>
              <th>Student</th>
              <th>Age</th>
              <th>Family</th>
              <th>Teachers</th>
              <th>Status</th>
              <th>Latest Score</th>
              <th>Interpretation</th>
              <th>Last Updated</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($students as $s)
              <tr>
                <td><input type="checkbox" class="student-check" name="student_ids[]" value="{{ $s->student_id }}"></td>
                <td>
                  @if($s->feature_path)
                    <img src="{{ asset($s->feature_path) }}" alt="" class="rounded-circle" style="width:36px;height:36px;object-fit:cover;">
                  @else
                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width:36px;height:36px;font-size:0.8rem;">
                      {{ strtoupper(substr($s->first_name, 0, 1)) }}
                    </div>
                  @endif
                </td>
                <td>{{ $s->section_name ?? '—' }}</td>
                <td>{{ $s->last_name }}, {{ $s->first_name }}</td>
                <td>{{ $s->computed_age_years !== null ? $s->computed_age_years . ' yrs' : '—' }}</td>
                <td>{{ $s->family_name ?? '—' }}</td>
                <td>
                  @php $teachers = $s->computed_teachers; @endphp
                  @if($teachers->isEmpty())
                    <span class="text-muted">None</span>
                  @else
                    <span>
                      {{ $teachers->take(2)->map(fn($t) => trim(($t->first_name ?? '').' '.($t->last_name ?? '')) ?: $t->username)->implode(', ') }}
                      @if($teachers->count() > 2)
                        <span class="text-muted">+{{ $teachers->count() - 2 }} more</span>
                      @endif
                    </span>
                  @endif
                </td>
                <td>
                  @php $status = $s->computed_status; @endphp
                  @if($status === 'Overdue')
                    <span class="badge bg-danger">Overdue</span>
                  @elseif($status === 'Ongoing')
                    <span class="badge bg-warning text-dark">Ongoing</span>
                  @elseif($status === 'Completed')
                    <span class="badge bg-success">Completed</span>
                  @elseif($status === 'Scheduled')
                    <span class="badge bg-info text-dark">Scheduled</span>
                  @else
                    <span class="badge bg-secondary">No assessment</span>
                  @endif
                </td>
                <td>{{ $s->computed_latest_score ?? '—' }}</td>
                <td>{{ $s->computed_latest_interpretation ?? '—' }}</td>
                <td>{{ optional($s->updated_at)->format('Y-m-d') }}</td>
                <td class="text-end">
                  <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('admin.students.show', $s->student_id) }}" class="btn btn-outline-secondary">View</a>
                    <a href="{{ route('admin.students.edit', $s->student_id) }}" class="btn btn-outline-secondary">Edit</a>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="11" class="text-center text-muted py-3">No students found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="p-2">
        {{ $students->links() }}
      </div>
    </div>
  </div>
</form>
@endsection
