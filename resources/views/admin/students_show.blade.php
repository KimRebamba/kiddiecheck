@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div class="d-flex align-items-center gap-3">
    @if($student->feature_path)
      <img src="{{ asset($student->feature_path) }}" alt="" class="rounded-circle" style="width:64px;height:64px;object-fit:cover;">
    @else
      <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width:64px;height:64px;font-size:1.25rem;">
        {{ strtoupper(substr($student->first_name, 0, 1)) }}
      </div>
    @endif
    <div>
      <h1 class="h4 mb-1">{{ $student->first_name }} {{ $student->last_name }}</h1>
      <div class="text-muted small">DOB: {{ $student->date_of_birth }} · Age: {{ $ageYears !== null ? $ageYears . ' yrs' : '—' }}</div>
    </div>
  </div>
  <div class="d-flex flex-wrap gap-2">
    <a href="{{ route('admin.students.edit', $student->student_id) }}" class="btn btn-outline-secondary btn-sm">Edit</a>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-12 col-lg-4">
    <div class="card h-100">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Basic Information</h2>
      </div>
      <div class="card-body small">
        <dl class="row mb-0">
          <dt class="col-5">Section</dt>
          <dd class="col-7">{{ $student->section_name ?? '—' }}</dd>
          <dt class="col-5">Full name</dt>
          <dd class="col-7">{{ $student->first_name }} {{ $student->last_name }}</dd>
          <dt class="col-5">Date of birth</dt>
          <dd class="col-7">{{ $student->date_of_birth }}</dd>
          <dt class="col-5">Age</dt>
          <dd class="col-7">{{ $ageYears !== null ? $ageYears . ' years' : '—' }}</dd>
          <dt class="col-5">Created</dt>
          <dd class="col-7">{{ optional($student->created_at)->format('Y-m-d H:i') }}</dd>
        </dl>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-4">
    <div class="card h-100">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Family</h2>
      </div>
      <div class="card-body small">
        <dl class="row mb-0">
          <dt class="col-5">Family name</dt>
          <dd class="col-7">{{ $student->family_name ?? '—' }}</dd>
          <dt class="col-5">Address</dt>
          <dd class="col-7">{{ $student->family_home_address ?? '—' }}</dd>
          <dt class="col-5">Emergency contact</dt>
          <dd class="col-7">{{ $student->emergency_contact ?? '—' }}</dd>
          <dt class="col-5">Emergency phone</dt>
          <dd class="col-7">{{ $student->emergency_phone ?? '—' }}</dd>
        </dl>
        <form method="post" action="{{ route('admin.students.transfer_family', $student->student_id) }}" class="mt-3">
          @csrf
          <label class="form-label">Transfer to another family</label>
          <select name="family_id" class="form-select form-select-sm mb-2">
            @foreach(\Illuminate\Support\Facades\DB::table('families as f')->join('users as u','f.user_id','=','u.user_id')->orderBy('f.family_name')->get(['f.user_id','f.family_name','u.email']) as $f)
              <option value="{{ $f->user_id }}" {{ (string)$student->family_id === (string)$f->user_id ? 'selected' : '' }}>{{ $f->family_name }} ({{ $f->email }})</option>
            @endforeach
          </select>
          <button type="submit" class="btn btn-outline-secondary btn-sm">Update Family</button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-4">
    <div class="card h-100">
      <div class="card-header bg-white border-0 pb-0 d-flex justify-content-between align-items-center">
        <h2 class="h6 mb-1">Assigned Teachers</h2>
      </div>
      <div class="card-body small">
        @if($teachers->isEmpty())
          <p class="text-muted mb-2">No teachers currently assigned.</p>
        @else
          <ul class="list-group list-group-flush mb-2">
            @foreach($teachers as $t)
              <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                <span>{{ $t->last_name }}, {{ $t->first_name }} ({{ $t->username }})</span>
                <form method="post" action="{{ route('admin.students.remove_teacher', [$student->student_id, $t->user_id]) }}" onsubmit="return confirm('Remove this teacher from the student?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-link btn-sm text-danger p-0">Remove</button>
                </form>
              </li>
            @endforeach
          </ul>
        @endif
        <form method="post" action="{{ route('admin.students.assign_teacher', $student->student_id) }}">
          @csrf
          <label class="form-label">Assign teacher</label>
          <select name="teacher_id" class="form-select form-select-sm mb-2">
            <option value="">Select...</option>
            @foreach($allTeacherOptions as $t)
              <option value="{{ $t->user_id }}">{{ $t->last_name }}, {{ $t->first_name }} ({{ $t->username }})</option>
            @endforeach
          </select>
          <button type="submit" class="btn btn-outline-secondary btn-sm">Assign</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-12 col-lg-6">
    <div class="card h-100">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Assessment Timeline</h2>
      </div>
      <div class="card-body small">
        @if($periods->isEmpty())
          <p class="text-muted mb-0">No assessment periods found for this student.</p>
        @else
          <div class="table-responsive">
            <table class="table table-sm mb-0">
              <thead>
                <tr>
                  <th>Period</th>
                  <th>Start</th>
                  <th>End</th>
                  <th>Status</th>
                  <th>Teacher avg</th>
                  <th>Family score</th>
                  <th>Final score</th>
                </tr>
              </thead>
              <tbody>
                @foreach($periods as $p)
                  <tr>
                    <td>{{ $p->description }}</td>
                    <td>{{ $p->start_date }}</td>
                    <td>{{ $p->end_date }}</td>
                    <td>
                      @if($p->status === 'overdue')
                        <span class="badge bg-danger">Overdue</span>
                      @elseif($p->status === 'completed')
                        <span class="badge bg-success">Completed</span>
                      @else
                        <span class="badge bg-info text-dark">Scheduled</span>
                      @endif
                    </td>
                    <td>{{ $p->teachers_standard_score_avg ?? '—' }}</td>
                    <td>{{ $p->family_standard_score ?? '—' }}</td>
                    <td>{{ $p->final_standard_score ?? '—' }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-6">
    <div class="card h-100">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Scores & Discrepancy Summary</h2>
      </div>
      <div class="card-body small">
        @if($discrepancySummaries->isEmpty())
          <p class="text-muted mb-0">No summary scores have been computed yet.</p>
        @else
          <div class="table-responsive">
            <table class="table table-sm mb-0">
              <thead>
                <tr>
                  <th>Period</th>
                  <th>Final score</th>
                  <th>Interpretation</th>
                  <th>Teacher disc.</th>
                  <th>Teacher–family</th>
                </tr>
              </thead>
              <tbody>
                @foreach($discrepancySummaries as $p)
                  <tr>
                    <td>{{ $p->description }}</td>
                    <td>{{ $p->final_standard_score ?? '—' }}</td>
                    <td>{{ $p->final_interpretation ?? '—' }}</td>
                    <td>
                      @if($p->teacher_discrepancy === 'major')
                        <span class="badge bg-danger">Major</span>
                      @elseif($p->teacher_discrepancy === 'minor')
                        <span class="badge bg-warning text-dark">Minor</span>
                      @elseif($p->teacher_discrepancy === 'none')
                        <span class="badge bg-success">None</span>
                      @else
                        —
                      @endif
                    </td>
                    <td>
                      @if($p->teacher_family_discrepancy === 'major')
                        <span class="badge bg-danger">Major</span>
                      @elseif($p->teacher_family_discrepancy === 'minor')
                        <span class="badge bg-warning text-dark">Minor</span>
                      @elseif($p->teacher_family_discrepancy === 'none')
                        <span class="badge bg-success">None</span>
                      @else
                        —
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif

        <hr>
        <h3 class="h6">Latest Domain Scores (most recent completed test)</h3>
        @if($domainScores->isEmpty())
          <p class="text-muted mb-0">No domain scores available yet.</p>
        @else
          <div class="table-responsive">
            <table class="table table-sm mb-0">
              <thead>
                <tr>
                  <th>Domain</th>
                  <th>Raw</th>
                  <th>Scaled</th>
                </tr>
              </thead>
              <tbody>
                @foreach($domainScores as $d)
                  <tr>
                    <td>{{ $d->domain_name }}</td>
                    <td>{{ $d->raw_score }}</td>
                    <td>{{ $d->scaled_score }}</td>
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

<div class="card mb-3">
  <div class="card-header bg-white border-0 pb-0 d-flex justify-content-between align-items-center">
    <h2 class="h6 mb-1">Tests Overview</h2>
  </div>
  <div class="card-body small">
    @if($tests->isEmpty())
      <p class="text-muted mb-0">No tests have been recorded for this student.</p>
    @else
      <div class="table-responsive">
        <table class="table table-sm mb-0 align-middle">
          <thead>
            <tr>
              <th>Examiner</th>
              <th>Role</th>
              <th>Date</th>
              <th>Status</th>
              <th>Notes</th>
              <th>Pictures</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($tests as $t)
              <tr>
                <td>{{ $t->examiner_username ?? '—' }}</td>
                <td class="text-capitalize">{{ $t->examiner_role ?? '—' }}</td>
                <td>{{ $t->test_date }}</td>
                <td>{{ ucfirst($t->status) }}</td>
                <td>{{ $t->notes ? 'Yes' : 'No' }}</td>
                <td>{{ $picturesCountByTest[$t->test_id] ?? 0 }}</td>
                <td class="text-end">
                  <form method="post" action="{{ route('admin.tests.cancel', $t->test_id) }}" class="d-inline" onsubmit="return confirm('Cancel this test? This is for invalid/erroneous tests only.');">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary btn-sm" {{ in_array($t->status, ['canceled','finalized']) ? 'disabled' : '' }}>Cancel</button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>
@endsection
