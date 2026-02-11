@extends('admin.layout')

@section('content')
<div class="d-flex align-items-center mb-3">
  <h1 class="h4 mb-0">Student Detail</h1>
  <div class="ms-auto">
    <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.index') }}">Back</a>
  </div>
  </div>

<div class="card mb-3">
  <div class="card-body">
    <div class="row g-3">
      <div class="col-12 col-md-6">
        <div><span class="fw-semibold">Name:</span> {{ $student->name }}</div>
        <div><span class="fw-semibold">Gender:</span> {{ $student->gender }}</div>
        <div><span class="fw-semibold">Status:</span> {{ $student->status }}</div>
        <div><span class="fw-semibold">DOB:</span> {{ $student->dob }}</div>
        <div><span class="fw-semibold">Enrollment Date:</span> {{ $student->enrollment_date }}</div>
      </div>
      <div class="col-12 col-md-6">
        <div><span class="fw-semibold">Section:</span> {{ $student->section?->name ?? '—' }}</div>
        <div><span class="fw-semibold">Family:</span> {{ $student->family?->name ?? '—' }}</div>
        <div><span class="fw-semibold">Teachers:</span> {{ $student->teachers->pluck('user.name')->join(', ') ?: '—' }}</div>
        <div><span class="fw-semibold">Tags:</span> {{ $student->tags->pluck('tag_type')->join(', ') ?: '—' }}</div>
      </div>
    </div>
    <div class="mt-3 d-flex gap-2">
      <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.students.record', $student->id) }}">View ECCD Record</a>
      <form method="POST" action="{{ route('admin.students.delete', $student->id) }}" onsubmit="return confirm('Delete this student? This will be blocked if tests exist.')">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-danger">Delete Student</button>
      </form>
    </div>
  </div>
 </div>

<div class="card mb-3">
  <div class="card-header bg-light">Assessment Periods</div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-sm table-hover mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th>Starts</th>
            <th>Ends</th>
            <th>Teacher Grace End</th>
            <th>Status</th>
            <th>Window Data</th>
          </tr>
        </thead>
        <tbody>
        @forelse($student->assessmentPeriods as $p)
          @php
            $tests = $student->tests()->with(['observer'])
              ->where('assessment_period_id', $p->id)->finalized()->get();
            $hasTeacher = $tests->first(fn($t)=> $t->observer?->role==='teacher');
            $hasFamily = $tests->first(fn($t)=> $t->observer?->role==='family');
          @endphp
          <tr>
            <td>{{ $p->index }}</td>
            <td>{{ $p->starts_at }}</td>
            <td>{{ $p->ends_at }}</td>
            <td>{{ $p->teacher_grace_end ?? '—' }}</td>
            <td>
              @if($p->status==='scheduled')
                <span class="badge bg-secondary">Scheduled – Not yet active</span>
              @elseif($p->status==='active')
                <span class="badge bg-primary">Active</span>
              @else
                <span class="badge bg-dark">Closed</span>
              @endif
            </td>
            <td>
              @if($tests->isEmpty())
                <span class="text-muted">No data available</span>
                <div class="small text-muted">No assessments were completed within the eligibility window.</div>
              @else
                <div>
                  @if($hasFamily && !$hasTeacher)
                    <span class="badge bg-info">Family-only</span>
                    <div class="small text-muted">Teacher assessments not submitted for this period.</div>
                  @elseif($hasTeacher && $hasFamily)
                    <span class="badge bg-success">Teacher + Family</span>
                  @else
                    <span class="badge bg-success">Teacher</span>
                  @endif
                </div>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-muted">No assessment periods</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="card mb-3">
  <div class="card-header bg-light">Longitudinal Summary</div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-sm table-hover mb-0">
        <thead>
          <tr>
            <th>Interval</th>
            <th>Data</th>
            <th>Standard Score</th>
          </tr>
        </thead>
        <tbody>
          @php $L = $longitudinal ?? null; @endphp
          <tr>
            <td>6 months</td>
            <td>
              @if(($L['longitudinal'][1] ?? null))
                Sum Scaled: {{ $L['longitudinal'][1]['sumScaled'] }}
              @else
                <span class="text-muted">No data</span>
              @endif
            </td>
            <td>
              @if(($L['longitudinal'][1] ?? null))
                {{ $L['longitudinal'][1]['standardScore'] }}
              @else
                <span class="text-muted">—</span>
              @endif
            </td>
          </tr>
          <tr>
            <td>12 months</td>
            <td>
              @if(($L['longitudinal'][2] ?? null))
                Avg Sum Scaled: {{ $L['longitudinal'][2]['sumScaled'] }}
              @else
                <span class="text-muted">No data</span>
              @endif
            </td>
            <td>
              @if(($L['longitudinal'][2] ?? null))
                {{ $L['longitudinal'][2]['standardScore'] }}
              @else
                <span class="text-muted">—</span>
              @endif
            </td>
          </tr>
          <tr>
            <td>18 months</td>
            <td>
              @if(($L['longitudinal'][3] ?? null))
                Avg Sum Scaled: {{ $L['longitudinal'][3]['sumScaled'] }}
              @else
                <span class="text-muted">No data</span>
              @endif
            </td>
            <td>
              @if(($L['longitudinal'][3] ?? null))
                {{ $L['longitudinal'][3]['standardScore'] }}
              @else
                <span class="text-muted">—</span>
              @endif
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="card-footer">
    <div class="small text-muted">Averages use only completed periods; missing/terminated periods are excluded and flagged as No data.</div>
  </div>
</div>

<div class="card">
  <div class="card-header bg-light">Tests</div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-sm table-hover mb-0">
        <thead>
          <tr>
            <th>Date</th>
            <th>Observer</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($student->tests as $t)
            <tr>
              <td>{{ $t->test_date }}</td>
              <td>{{ optional($t->observer)->name }} ({{ optional($t->observer)->role }})</td>
              <td>
                <span class="badge {{ in_array($t->status,['finalized','completed']) ? 'bg-success' : ($t->status==='archived' ? 'bg-secondary' : ($t->status==='terminated' ? 'bg-danger' : 'bg-warning')) }}">{{ $t->status }}</span>
                @if($t->termination_reason)
                  <div class="small text-muted">{{ $t->termination_reason }}</div>
                @endif
              </td>
              <td class="d-flex gap-2">
                @if(in_array($t->status,['finalized','completed']))
                  <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.tests.result', $t->id) }}">View Result</a>
                @endif
                @if(in_array($t->status,['finalized','completed','cancelled']))
                  <form method="post" action="{{ route('admin.tests.archive', $t->id) }}">
                    @csrf
                    <button class="btn btn-sm btn-outline-secondary" type="submit">Archive</button>
                  </form>
                @endif
                <form method="post" action="{{ route('admin.tests.delete', $t->id) }}" onsubmit="return confirm('Delete this test?')">
                  @csrf
                  <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-muted">No tests</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
