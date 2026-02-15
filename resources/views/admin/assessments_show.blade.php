@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div class="d-flex align-items-center gap-3">
    @if($period->student_feature_path)
      <img src="{{ asset($period->student_feature_path) }}" alt="" class="rounded-circle" style="width:64px;height:64px;object-fit:cover;">
    @else
      <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width:64px;height:64px;font-size:1.25rem;">
        {{ strtoupper(substr($period->student_first_name, 0, 1)) }}
      </div>
    @endif
    <div>
      <h1 class="h4 mb-1">{{ $period->student_first_name }} {{ $period->student_last_name }}</h1>
      <div class="text-muted small">
        Period: {{ $period->description }} · {{ $period->start_date }} to {{ $period->end_date }}
      </div>
      <div class="text-muted small">
        Age during assessment: {{ $ageYearsAtStart !== null ? $ageYearsAtStart . ' years' : '—' }}
      </div>
    </div>
  </div>
  <div class="d-flex flex-wrap gap-2">
    <a href="{{ route('admin.assessments') }}" class="btn btn-outline-secondary btn-sm">Back to Assessments</a>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-12 col-lg-4">
    <div class="card h-100">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Student & Period Info</h2>
      </div>
      <div class="card-body small">
        <dl class="row mb-0">
          <dt class="col-5">Student</dt>
          <dd class="col-7">{{ $period->student_first_name }} {{ $period->student_last_name }}</dd>
          <dt class="col-5">DOB</dt>
          <dd class="col-7">{{ $period->date_of_birth }}</dd>
          <dt class="col-5">Period</dt>
          <dd class="col-7">{{ $period->description }}</dd>
          <dt class="col-5">Dates</dt>
          <dd class="col-7">{{ $period->start_date }} – {{ $period->end_date }}</dd>
          <dt class="col-5">Status</dt>
          <dd class="col-7 text-capitalize">{{ $period->status }}</dd>
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
          <dd class="col-7">{{ $period->family_name ?? '—' }}</dd>
          <dt class="col-5">Address</dt>
          <dd class="col-7">{{ $period->family_home_address ?? '—' }}</dd>
          <dt class="col-5">Emergency contact</dt>
          <dd class="col-7">{{ $period->emergency_contact ?? '—' }}</dd>
          <dt class="col-5">Emergency phone</dt>
          <dd class="col-7">{{ $period->emergency_phone ?? '—' }}</dd>
        </dl>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-4">
    <div class="card h-100">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Assigned Teachers & Examiners</h2>
      </div>
      <div class="card-body small">
        <h3 class="h6">Assigned teachers</h3>
        @if($teachers->isEmpty())
          <p class="text-muted mb-2">No teachers assigned.</p>
        @else
          <ul class="list-group list-group-flush mb-2">
            @foreach($teachers as $t)
              <li class="list-group-item px-0">{{ $t->last_name }}, {{ $t->first_name }} ({{ $t->username }})</li>
            @endforeach
          </ul>
        @endif

        <h3 class="h6 mt-3">Examiners (tests)</h3>
        @if($examiners->isEmpty())
          <p class="text-muted mb-0">No tests recorded.</p>
        @else
          <ul class="list-group list-group-flush mb-0">
            @foreach($examiners as $e)
              <li class="list-group-item px-0">{{ $e->username }} <span class="text-muted">({{ $e->role }})</span></li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-12 col-xl-7">
    <div class="card h-100">
      <div class="card-header bg-white border-0 pb-0 d-flex justify-content-between align-items-center">
        <h2 class="h6 mb-1">Test Progress</h2>
      </div>
      <div class="card-body small">
        @if($tests->isEmpty())
          <p class="text-muted mb-0">No tests have been started for this period.</p>
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
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-5">
    <div class="card h-100 mb-3 mb-xl-2">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Domain Results Summary</h2>
      </div>
      <div class="card-body small">
        @if($domainScores->isEmpty())
          <p class="text-muted mb-0">No domain scores computed yet.</p>
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

<div class="row g-3 mb-3">
  <div class="col-12 col-xl-7">
    <div class="card h-100">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Standard Score Results</h2>
      </div>
      <div class="card-body small">
        <h3 class="h6">Individual teacher standard scores</h3>
        @if($teacherStandardScores->isEmpty())
          <p class="text-muted">No teacher standard scores recorded.</p>
        @else
          <div class="table-responsive mb-2">
            <table class="table table-sm mb-0">
              <thead>
                <tr>
                  <th>Teacher</th>
                  <th>Standard score</th>
                  <th>Interpretation</th>
                </tr>
              </thead>
              <tbody>
                @foreach($teacherStandardScores as $ts)
                  <tr>
                    <td>{{ $ts->teacher_username }}</td>
                    <td>{{ $ts->standard_score }}</td>
                    <td>{{ $ts->interpretation }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif

        <h3 class="h6 mt-3">Summary</h3>
        @if($summary)
          <dl class="row mb-0">
            <dt class="col-6 col-md-5">Teachers average score</dt>
            <dd class="col-6 col-md-7">{{ $summary->teachers_standard_score_avg ?? '—' }}</dd>
            <dt class="col-6 col-md-5">Family standard score</dt>
            <dd class="col-6 col-md-7">{{ $summary->family_standard_score ?? '—' }}</dd>
            <dt class="col-6 col-md-5">Final standard score</dt>
            <dd class="col-6 col-md-7">{{ $summary->final_standard_score ?? '—' }}</dd>
            <dt class="col-6 col-md-5">Final interpretation</dt>
            <dd class="col-6 col-md-7">{{ $summary->final_interpretation ?? '—' }}</dd>
          </dl>
        @else
          <p class="text-muted mb-0">No summary scores computed yet.</p>
        @endif
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-5">
    <div class="card h-100 mb-3 mb-xl-2">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Discrepancy Monitoring</h2>
      </div>
      <div class="card-body small">
        @if(!$summary)
          <p class="text-muted mb-0">No discrepancy data yet.</p>
        @else
          <div class="mb-2">
            <div class="text-muted">Teacher vs teacher discrepancy</div>
            @php $td = $summary->teacher_discrepancy; @endphp
            @if($td === 'major')
              <span class="badge bg-danger">Major</span>
            @elseif($td === 'minor')
              <span class="badge bg-warning text-dark">Minor</span>
            @elseif($td === 'none')
              <span class="badge bg-success">None</span>
            @else
              <span class="badge bg-secondary">Unknown</span>
            @endif
          </div>
          <div class="mb-2">
            <div class="text-muted">Teacher vs family discrepancy</div>
            @php $tfd = $summary->teacher_family_discrepancy; @endphp
            @if($tfd === 'major')
              <span class="badge bg-danger">Major</span>
            @elseif($tfd === 'minor')
              <span class="badge bg-warning text-dark">Minor</span>
            @elseif($tfd === 'none')
              <span class="badge bg-success">None</span>
            @else
              <span class="badge bg-secondary">Unknown</span>
            @endif
          </div>
        @endif
      </div>
    </div>

    <div class="card h-100">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Admin Actions</h2>
      </div>
      <div class="card-body small">
        <form method="post" action="{{ route('admin.assessments.extend', $period->period_id) }}" class="mb-2">
          @csrf
          <label class="form-label">Extend deadline</label>
          <div class="input-group input-group-sm mb-1">
            <input type="date" name="end_date" value="{{ $period->end_date }}" class="form-control form-control-sm">
            <button class="btn btn-outline-secondary" type="submit">Update</button>
          </div>
          <div class="form-text">Admins may only adjust deadlines, not responses.</div>
        </form>

        <form method="post" action="{{ route('admin.assessments.close', $period->period_id) }}" class="mb-2" onsubmit="return confirm('Mark this assessment period as closed?');">
          @csrf
          <button type="submit" class="btn btn-outline-secondary btn-sm w-100">Mark period as closed</button>
        </form>

        <form method="post" action="{{ route('admin.assessments.recompute', $period->period_id) }}" class="mb-2">
          @csrf
          <button type="submit" class="btn btn-outline-secondary btn-sm w-100">Recompute scores</button>
        </form>

        <a href="{{ route('admin.assessments.export', $period->period_id) }}" class="btn btn-outline-secondary btn-sm w-100 mb-2">Export assessment report (PDF)</a>

        <form method="post" action="{{ route('admin.assessments.notify', $period->period_id) }}" class="mb-2">
          @csrf
          <input type="hidden" name="target" value="teachers">
          <button type="submit" class="btn btn-outline-secondary btn-sm w-100">Send notification to teachers</button>
        </form>

        <form method="post" action="{{ route('admin.assessments.notify', $period->period_id) }}">
          @csrf
          <input type="hidden" name="target" value="family">
          <button type="submit" class="btn btn-outline-secondary btn-sm w-100">Send notification to family</button>
        </form>

        <p class="text-muted mt-2 mb-0">
          Admins cannot edit answers or scores here. This page is for monitoring, oversight, and coordination.
        </p>
      </div>
    </div>
  </div>
</div>
@endsection
