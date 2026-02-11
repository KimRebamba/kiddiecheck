@extends('teacher.layout')

@section('content')
<div class="d-flex align-items-center mb-3">
  <h1 class="h4 mb-0">{{ $student->name }}</h1>
  <div class="ms-auto">
    <a class="btn btn-sm btn-outline-secondary" href="{{ route('teacher.index') }}">Back to Dashboard</a>
  </div>
  </div>

<div class="card mb-3">
  <div class="card-body">
    <div class="row g-3">
      <div class="col-6 col-md-3">
        <div class="text-muted">Gender</div>
        <div class="fw-semibold">{{ ucfirst($student->gender ?? 'N/A') }}</div>
      </div>
      <div class="col-6 col-md-3">
        <div class="text-muted">DOB</div>
        <div class="fw-semibold">{{ $student->dob ?? 'N/A' }}</div>
      </div>
      <div class="col-6 col-md-3">
        <div class="text-muted">Section</div>
        <div class="fw-semibold">{{ $student->section->name ?? 'N/A' }}</div>
      </div>
      <div class="col-6 col-md-3">
        <div class="text-muted">Emergency Contact</div>
        <div class="fw-semibold">{{ $student->emergency_contact ?? 'N/A' }}</div>
      </div>
    </div>
  </div>
</div>

<div class="card mb-3">
  <div class="card-header bg-light">All Tests</div>
  <div class="card-body p-0">
    @if($tests->isEmpty())
      <div class="p-3 text-muted">No tests yet.</div>
    @else
      <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>Date</th>
              <th>By</th>
              <th>Status</th>
              <th>Sum Scaled</th>
              <th>Standard Score</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($tests as $t)
              @php $by = $t->observer?->role; $sum = \App\Services\EccdScoring::summarize(collect([$t]), $domains)[$t->id]['sumScaled'] ?? null; @endphp
              <tr>
                <td>{{ $t->test_date }}</td>
                <td>{{ $by ?? '—' }}</td>
                <td>
                  <span class="badge bg-{{ $t->status === 'completed' ? 'success' : ($t->status === 'in_progress' ? 'warning' : 'secondary') }}">{{ ucfirst(str_replace('_',' ', $t->status)) }}</span>
                </td>
                <td>{{ $sum ? number_format($sum,2) : '—' }}</td>
                <td>{{ \App\Services\EccdScoring::deriveStandardScore((float)$sum, $domains->count()) ?? '—' }}</td>
                <td>
                  <a class="btn btn-sm btn-outline-primary" href="{{ $by==='teacher' ? route('teacher.tests.result',$t->id) : route('family.tests.result',$t->id) }}">View</a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>

<div class="card mb-3">
  <div class="card-header bg-light">Domain Performance (6 / 12 / 18 months)</div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-sm table-hover mb-0">
        <thead>
          <tr>
            <th>Domain</th>
            <th>Teacher Avg (6m)</th>
            <th>Teacher Avg (12m)</th>
            <th>Teacher Avg (18m)</th>
            <th>Family Avg (6m)</th>
          </tr>
        </thead>
        <tbody>
          @foreach($domains as $d)
            <tr>
              <td>{{ $d->name }}</td>
              <td>{{ $avg($teacherTests, 6, $d->id) }}</td>
              <td>{{ $avg($teacherTests, 12, $d->id) }}</td>
              <td>{{ $avg($teacherTests, 18, $d->id) }}</td>
              <td>{{ $avg($familyTests, 6, $d->id) }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

@php $agg = \App\Services\EccdScoring::aggregate($tests, $domains); @endphp
<div class="card mb-3">
  <div class="card-header bg-light">Aggregated Result (Teacher + optional Family)</div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-sm table-hover mb-0">
        <thead>
          <tr>
            <th>Domain</th>
            <th>Scaled (avg)</th>
          </tr>
        </thead>
        <tbody>
          @foreach($domains as $d)
            <tr>
              <td>{{ $d->name }}</td>
              <td>{{ optional($agg['domains'])[$d->id] ?? '—' }}</td>
            </tr>
          @endforeach
          <tr>
            <td><strong>Sum Scaled</strong></td>
            <td>{{ $agg['sumScaled'] ?? '—' }}</td>
          </tr>
          <tr>
            <td><strong>Standard Score</strong></td>
            <td>
              {{ $agg['standardScore'] ?? '—' }}
              @if(!empty($agg['standardScore']))
                @php $ssc = \App\Services\EccdScoring::classifyStandard((int)$agg['standardScore']); @endphp
                — {{ $ssc ?? '' }}
              @endif
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body d-flex gap-2">
    <form method="post" action="{{ route('teacher.tests.start', $student->id) }}">
      @csrf
      <button type="submit" class="btn btn-primary">Start New Test (if eligible)</button>
    </form>
    @php $inProgress = $student->tests->firstWhere('status','in_progress'); @endphp
    @if($inProgress)
      <a class="btn btn-warning" href="{{ route('teacher.tests.question', [$inProgress->id, \App\Models\Domain::orderBy('id')->first()->id, 0]) }}">Continue In-Progress</a>
      <form method="post" action="{{ route('teacher.tests.pause', $inProgress->id) }}">
        @csrf
        <button type="submit" class="btn btn-outline-secondary">Pause</button>
      </form>
      <form method="post" action="{{ route('teacher.tests.cancel', $inProgress->id) }}">
        @csrf
        <button type="submit" class="btn btn-outline-danger">Cancel</button>
      </form>
      <form method="post" action="{{ route('teacher.tests.terminate', $inProgress->id) }}">
        @csrf
        <button type="submit" class="btn btn-outline-warning">Terminate</button>
      </form>
    @endif
  </div>
</div>
@endsection
